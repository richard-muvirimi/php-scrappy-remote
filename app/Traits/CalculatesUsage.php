<?php

namespace App\Traits;

use App\Models\Content;
use App\Models\User;
use Illuminate\Support\Carbon;

trait CalculatesUsage
{
    protected function getCurrentUsage(User $user): float
    {
        return $user->contentMetas()
            ->where('key', '=', 'time')
            ->whereRelation('content', 'type', Content::TYPE_SITE)
            ->whereRelation('content', 'created_at', '>', Carbon::now()->subMonth())
            ->get()
            ->sum('value');
    }

    protected function hasUsageCredits(float $current): bool
    {
        return $current < $this->usageLimit;
    }

    protected int|float $usageLimit = 60 * 2000;
}
