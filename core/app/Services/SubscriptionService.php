<?php

namespace App\Services;

use App\Models\BuyerSubscription;
use App\Models\Plan;
use App\Models\UserSubscription;
use App\Support\SafeSchema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public const UNLIMITED = 999999;

    public function refreshUserSubscription(int $userId): void
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            return;
        }

        $today = Carbon::today();
        UserSubscription::where('user_id', $userId)
            ->where('is_active', 1)
            ->whereDate('end_date', '<', $today)
            ->update(['is_active' => 0]);

        if (! UserSubscription::where('user_id', $userId)->where('is_active', 1)->exists()) {
            try {
                $this->assignFreePlanToUser($userId);
            } catch (\Throwable $e) {
                \Log::error('SubscriptionService::refreshUserSubscription: '.$e->getMessage());
            }
        }
    }

    public function refreshBuyerSubscription(int $buyerId): void
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            return;
        }

        $today = Carbon::today();
        BuyerSubscription::where('buyer_id', $buyerId)
            ->where('is_active', 1)
            ->whereDate('end_date', '<', $today)
            ->update(['is_active' => 0]);

        if (! BuyerSubscription::where('buyer_id', $buyerId)->where('is_active', 1)->exists()) {
            try {
                $this->assignFreePlanToBuyer($buyerId);
            } catch (\Throwable $e) {
                \Log::error('SubscriptionService::refreshBuyerSubscription: '.$e->getMessage());
            }
        }
    }

    public function getUserPlan(?int $userId): ?Plan
    {
        if (! $userId || ! SafeSchema::subscriptionsAvailable()) {
            return null;
        }
        $sub = UserSubscription::where('user_id', $userId)->where('is_active', 1)->with('plan')->first();

        return $sub?->plan;
    }

    public function getBuyerPlan(?int $buyerId): ?Plan
    {
        if (! $buyerId || ! SafeSchema::subscriptionsAvailable()) {
            return null;
        }
        $sub = BuyerSubscription::where('buyer_id', $buyerId)->where('is_active', 1)->with('plan')->first();

        return $sub?->plan;
    }

    public function getActiveUserSubscription(int $userId): ?UserSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            return null;
        }

        return UserSubscription::where('user_id', $userId)->where('is_active', 1)->with('plan')->first();
    }

    public function getActiveBuyerSubscription(int $buyerId): ?BuyerSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            return null;
        }

        return BuyerSubscription::where('buyer_id', $buyerId)->where('is_active', 1)->with('plan')->first();
    }

    private function isUnlimited(int $limit): bool
    {
        return $limit >= self::UNLIMITED;
    }

    /** @return array{allowed: bool, reason?: string, message?: string, remaining?: int|null} */
    public function canApplyJob(int $userId): array
    {
        $sub = $this->getActiveUserSubscription($userId);
        if (! $sub || ! $sub->plan) {
            return [
                'allowed' => false,
                'reason' => 'plan_expired',
                'message' => __('Plan expired or inactive. Upgrade required.'),
            ];
        }
        $limit = (int) $sub->plan->job_apply_limit;
        if ($this->isUnlimited($limit)) {
            return ['allowed' => true, 'remaining' => null, 'message' => ''];
        }
        $remaining = max(0, $limit - (int) $sub->jobs_applied_count);
        if ($remaining <= 0) {
            return [
                'allowed' => false,
                'reason' => 'limit_reached',
                'message' => __('Upgrade your plan to apply more jobs.'),
            ];
        }

        return ['allowed' => true, 'remaining' => $remaining, 'message' => ''];
    }

    /** @return array{allowed: bool, reason?: string, message?: string, remaining?: int|null} */
    public function canViewJob(int $userId): array
    {
        $sub = $this->getActiveUserSubscription($userId);
        if (! $sub || ! $sub->plan) {
            return [
                'allowed' => false,
                'reason' => 'plan_expired',
                'message' => __('Plan expired or inactive. Upgrade required.'),
            ];
        }
        $limit = (int) $sub->plan->job_view_limit;
        if ($limit === 0 || $this->isUnlimited($limit)) {
            return ['allowed' => true, 'remaining' => null];
        }
        $remaining = max(0, $limit - (int) $sub->jobs_viewed_count);
        if ($remaining <= 0) {
            return [
                'allowed' => false,
                'reason' => 'limit_reached',
                'message' => __('Job view limit reached. Upgrade your plan.'),
            ];
        }

        return ['allowed' => true, 'remaining' => $remaining];
    }

    public function recordJobDetailView(int $userId): void
    {
        $sub = $this->getActiveUserSubscription($userId);
        if (! $sub || ! $sub->plan) {
            return;
        }
        $limit = (int) $sub->plan->job_view_limit;
        if ($limit === 0 || $this->isUnlimited($limit)) {
            return;
        }
        $sub->increment('jobs_viewed_count');
    }

    public function shouldBlurJobListingForUser(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }
        $plan = $this->getUserPlan($userId);
        if (! $plan) {
            return false;
        }

        return (int) $plan->listing_visible_jobs < self::UNLIMITED;
    }

    public function listingVisibleCap(?int $userId): int
    {
        if (! $userId) {
            return PHP_INT_MAX;
        }
        $plan = $this->getUserPlan($userId);
        if (! $plan) {
            return PHP_INT_MAX;
        }
        $cap = (int) $plan->listing_visible_jobs;
        if ($this->isUnlimited($cap)) {
            return PHP_INT_MAX;
        }

        return max(1, $cap);
    }

    /** @return array{allowed: bool, reason?: string, message?: string, remaining?: int|null} */
    public function canPostJob(int $buyerId): array
    {
        $sub = $this->getActiveBuyerSubscription($buyerId);
        if (! $sub || ! $sub->plan) {
            return [
                'allowed' => false,
                'reason' => 'plan_expired',
                'message' => __('Plan expired or inactive. Upgrade required.'),
            ];
        }
        $limit = (int) $sub->plan->job_post_limit;
        if ($this->isUnlimited($limit)) {
            return ['allowed' => true, 'remaining' => null];
        }
        $remaining = max(0, $limit - (int) $sub->jobs_posted_count);
        if ($remaining <= 0) {
            return [
                'allowed' => false,
                'reason' => 'limit_reached',
                'message' => __('Job post limit reached. Upgrade your plan.'),
            ];
        }

        return ['allowed' => true, 'remaining' => $remaining];
    }

    public function incrementJobsApplied(int $userId): void
    {
        $sub = $this->getActiveUserSubscription($userId);
        if ($sub) {
            $sub->increment('jobs_applied_count');
        }
    }

    public function incrementJobsPosted(int $buyerId): void
    {
        $sub = $this->getActiveBuyerSubscription($buyerId);
        if ($sub) {
            $sub->increment('jobs_posted_count');
        }
    }

    public function assignFreePlanToUser(int $userId): UserSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            throw new \RuntimeException(__('Subscription tables are not installed. Run database migrations.'));
        }

        return DB::transaction(function () use ($userId) {
            UserSubscription::where('user_id', $userId)->update(['is_active' => 0]);
            $plan = Plan::student()->active()->orderBy('price')->orderBy('id')->firstOrFail();

            return $this->createUserSubscription($userId, $plan);
        });
    }

    public function assignFreePlanToBuyer(int $buyerId): BuyerSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            throw new \RuntimeException(__('Subscription tables are not installed. Run database migrations.'));
        }

        return DB::transaction(function () use ($buyerId) {
            BuyerSubscription::where('buyer_id', $buyerId)->update(['is_active' => 0]);
            $plan = Plan::company()->active()->orderBy('price')->orderBy('id')->firstOrFail();

            return $this->createBuyerSubscription($buyerId, $plan);
        });
    }

    public function switchUserPlan(int $userId, int $planId): UserSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            throw new \RuntimeException(__('Subscription tables are not installed. Run database migrations.'));
        }

        $plan = Plan::where('id', $planId)->where('type', Plan::TYPE_STUDENT)->where('is_active', 1)->firstOrFail();

        return DB::transaction(function () use ($userId, $plan) {
            UserSubscription::where('user_id', $userId)->update(['is_active' => 0]);

            return $this->createUserSubscription($userId, $plan);
        });
    }

    public function switchBuyerPlan(int $buyerId, int $planId): BuyerSubscription
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            throw new \RuntimeException(__('Subscription tables are not installed. Run database migrations.'));
        }

        $plan = Plan::where('id', $planId)->where('type', Plan::TYPE_COMPANY)->where('is_active', 1)->firstOrFail();

        return DB::transaction(function () use ($buyerId, $plan) {
            BuyerSubscription::where('buyer_id', $buyerId)->update(['is_active' => 0]);

            return $this->createBuyerSubscription($buyerId, $plan);
        });
    }

    private function createUserSubscription(int $userId, Plan $plan): UserSubscription
    {
        $start = Carbon::today();
        $end = (clone $start)->addDays(max(1, (int) $plan->duration_days));

        return UserSubscription::create([
            'user_id' => $userId,
            'plan_id' => $plan->id,
            'start_date' => $start,
            'end_date' => $end,
            'jobs_applied_count' => 0,
            'jobs_viewed_count' => 0,
            'jobs_posted_count' => 0,
            'is_active' => 1,
        ]);
    }

    private function createBuyerSubscription(int $buyerId, Plan $plan): BuyerSubscription
    {
        $start = Carbon::today();
        $end = (clone $start)->addDays(max(1, (int) $plan->duration_days));

        return BuyerSubscription::create([
            'buyer_id' => $buyerId,
            'plan_id' => $plan->id,
            'start_date' => $start,
            'end_date' => $end,
            'jobs_applied_count' => 0,
            'jobs_viewed_count' => 0,
            'jobs_posted_count' => 0,
            'is_active' => 1,
        ]);
    }
}
