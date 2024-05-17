<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentMeta;
use App\Rules\IsBoolean;
use App\Traits\CalculatesUsage;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ScrapeController extends BaseController
{
    use CalculatesUsage;

    /**
     * Enqueue site for scrapping
     */
    public function scrape(Request $request): JsonResponse
    {

        try {
            $request->validate([
                'url' => 'required|url:https,http',
                'css' => 'string|required_without:xpath',
                'xpath' => 'string|required_without:css',
                'format' => 'sometimes|required|string|in:text,html,json',
                'javascript' => ['sometimes', 'required', new IsBoolean()],
                'user_agent' => 'sometimes|required|string',
                'timeout' => 'sometimes|required|numeric|min:1|max:600',
                'webhook_url' => 'sometimes|required|string',
                'webhook_expire' => 'sometimes|required|numeric||min:600',
            ]);

            $content = $request->user()
                ->contents()
                ->create([
                    'type' => Content::TYPE_SITE,
                    'parent' => -1,
                    'content' => $request->input('url'),
                ]);

            if ($request->has('css') && $request->string('css')->isNotEmpty()) {
                $content->contentMetas()->create([
                    'key' => 'css',
                    'value' => $request->input('css'),
                ]);
            }

            if ($request->has('xpath') && $request->string('xpath')->isNotEmpty()) {
                $content->contentMetas()->create([
                    'key' => 'xpath',
                    'value' => $request->input('xpath'),
                ]);
            }

            $content->contentMetas()->create([
                'key' => 'format',
                'value' => $request->input('format', 'text'),
            ]);

            $content->contentMetas()->create([
                'key' => 'javascript',
                'value' => $request->input('javascript', 'false'),
            ]);

            if ($request->has('user_agent') && $request->string('user_agent')->isNotEmpty()) {
                $content->contentMetas()->create([
                    'key' => 'user_agent',
                    'value' => $request->input('user_agent'),
                ]);
            }

            $content->contentMetas()->create([
                'key' => 'status',
                'value' => Content::STATUS_PENDING,
            ]);

            $content->contentMetas()->create([
                'key' => 'IP_address',
                'value' => $request->ip(),
            ]);

            if ($request->has('webhook_url') && $request->string('webhook_url')->isNotEmpty()) {
                $content->contentMetas()->create([
                    'key' => 'webhook_url',
                    'value' => $request->input('webhook_url'),
                ]);
            }

            if ($request->has('webhook_expire') && $request->string('webhook_expire')->isNotEmpty()) {
                $content->contentMetas()->create([
                    'key' => 'webhook_expire',
                    'value' => $request->input('webhook_expire'),
                ]);
            }

            // wait for response timeout
            $timeout = $request->input('timeout', 300);

            set_time_limit($timeout * 1.5);

            $expire = time() + $timeout;

            do {
                $meta = $content->contentMetas()->where('key', 'status')->first();

                if (connection_aborted()) {
                    $meta->update(['value' => Content::STATUS_ABANDONED]);
                    break;
                }
                sleep(1);
            } while (is_a($meta, ContentMeta::class) && ! in_array($meta->value, [Content::STATUS_PROCESSED, Content::STATUS_ABANDONED]) && time() < $expire);

            if (is_a($meta, ContentMeta::class) && in_array($meta->value, [Content::STATUS_PENDING, Content::STATUS_PROCESSING])) {
                $meta->update(['value' => Content::STATUS_ABANDONED]);
            }

            $meta = $content->contentMetas()->where('key', 'status')->first();
            if (is_a($meta, ContentMeta::class) && $meta->value === Content::STATUS_PROCESSED) {
                $response['status'] = true;
                $response['data'] = $content->contentMetas()->where('key', 'response')->first()->value;
            }

            $usage = $this->getCurrentUsage($request->user()->first());

            $percentage = round($usage / $this->usageLimit * 100);

            $usage = number_format($usage);
            $limit = number_format($this->usageLimit);

            return response()->json([
                'status' => $response['status'] ?? false,
                'data' => $response['data'] ?? '',
                'message' => $usage.' of '.$limit.' ('.$percentage.'%) seconds used.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request): JsonResponse
    {

        try {
            $request->validate([
                'id' => 'required|numeric|exists:contents,id',
                'response' => 'required|string',
                'time' => 'required|numeric|min:0',
            ]);

            $content = Content::query()->find($request->integer('id'));

            if (is_a($content, Content::class)) {

                $content->contentMetas()->updateOrCreate(['key' => 'response'], ['value' => $request->input('response')]);
                $content->contentMetas()->where('key', 'status')->update(['value' => Content::STATUS_PROCESSED]);
                $content->contentMetas()->updateOrCreate(['key' => 'time'], ['value' => $request->input('time')]);

                $usage = $this->getCurrentUsage($content->user()->first());

                if ($this->hasUsageCredits($usage)) {

                    $webhook = $content
                        ->whereRelation('contentMetas', 'key', '=', 'webhook_url')
                        ->whereRelation('contentMetas', 'key', '=', 'webhook_expire')
                        ->pluck('value', 'key');

                    if ($webhook->isNotEmpty()) {

                        if (Carbon::now()->subSeconds($webhook->get('webhook_expire'))->isAfter(Carbon::now())) {
                            try {

                                $body = [
                                    'data' => $request->input('response'),
                                ];

                                $options = [
                                    'verify' => false,
                                ];

                                $client = new Client($options);

                                $client->post($webhook->get('webhook_url'), [
                                    RequestOptions::FORM_PARAMS => $body,
                                    RequestOptions::CONNECT_TIMEOUT => 3,
                                    RequestOptions::TIMEOUT => 1,
                                ]);
                            } catch (GuzzleException) {
                            }
                        }
                    }
                }

                return response()->json();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Content not found',
                ], Response::HTTP_NOT_FOUND);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Query for sites to scrape
     */
    public function query(Request $request): JsonResponse
    {

        try {

            $timeout = 600;
            $expire = time() + $timeout;

            set_time_limit($timeout * 1.5);
            do {

                $content = Content::query()
                    ->where('type', Content::TYPE_SITE)
                    ->whereRelation('contentMetas', 'key', 'status')
                    ->whereRelation('contentMetas', 'value', Content::STATUS_PENDING)
                    ->first();

                if (connection_aborted()) {
                    break;
                }
                sleep(1);
            } while (! is_a($content, Content::class) && time() < $expire);

            if (is_a($content, Content::class)) {

                $content->contentMetas()->where('key', 'status')->update(['value' => Content::STATUS_PROCESSING]);

                $response['data'] = [
                    'id' => $content->id,
                    'url' => $content->content,
                    'css' => $content->contentMetas()->where('key', 'css')->first()?->value ?: '',
                    'xpath' => $content->contentMetas()->where('key', 'xpath')->first()?->value ?: '',
                    'format' => $content->contentMetas()->where('key', 'format')->first()->value,
                    'javascript' => $content->contentMetas()->where('key', 'javascript')->first()->value,
                    'user_agent' => $content->contentMetas()->where('key', 'user_agent')->first()->value,
                    'IP_address' => $content->contentMetas()->where('key', 'IP_address')->first()->value,
                ];

                return response()->json($response);
            } else {
                return response()->json(status: Response::HTTP_NO_CONTENT);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
