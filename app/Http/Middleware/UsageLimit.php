<?php

namespace App\Http\Middleware;

use App\Traits\CalculatesUsage;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsageLimit
{
    use CalculatesUsage;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     *
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usage = $this->getCurrentUsage($request->user());

        if (!$this->hasUsageCredits($usage)) {

            $message = '2000 minute execution time limit exceeded. ' . ($usage / 60) . ' minutes used.';

            return response()->json(compact('message'), Response::HTTP_PAYMENT_REQUIRED);
        }

        return $next($request);
    }
}
