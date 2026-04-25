<?php

namespace App\Jobs;

use App\Models\ApplicationLog;
use App\Models\Incident;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CleanupExpiredMediaData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $creatorId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $creator = User::find($this->creatorId);
        if (!$creator) {
            Log::warning("Cleanup skipped: creator not found (ID {$this->creatorId})");
            return;
        }

        $plan = Plan::find($creator->plan);
        if (!$plan || empty($creator->plan_expire_date)) {
            Log::warning("Cleanup skipped: invalid plan for creator ID {$this->creatorId}");
            return;
        }

        // Stop if plan expired
        if (now()->greaterThan($creator->plan_expire_date)) {
            Log::info("Cleanup skipped: plan expired for creator ID {$this->creatorId}");
            return;
        }

        $companySlug = Str::slug($creator->company_name);
        $paths = [
            "uploads/companies/{$companySlug}/live-event",
            "uploads/companies/{$companySlug}/routine-event",
        ];

        $cutoffDate = now()->subDays($plan->backup_duration);

        // Cleanup old files
        foreach ($paths as $path) {
            try {
                Utility::deleteOldFiles($path, $plan->backup_duration);
                Log::info("Deleted old files at {$path} for creator ID {$this->creatorId}");
            } catch (\Exception $e) {
                Log::error("File cleanup failed at {$path}: " . $e->getMessage());
            }
        }

        // Cleanup old incidents + logs
        try {
            $usersIdOfCreator = User::where('created_by', $creator->id)->pluck('id')->push($creator->id);

            DB::transaction(function () use ($usersIdOfCreator, $cutoffDate) {
                Incident::whereIn('user_id', $usersIdOfCreator)
                    ->whereDate('created_at', '<', $cutoffDate)
                    ->delete();

                ApplicationLog::whereIn('user_id', $usersIdOfCreator)
                    ->whereDate('created_at', '<', $cutoffDate)
                    ->delete();
            });

            Log::info("Old incidents and logs cleaned for creator ID {$this->creatorId}");
        } catch (\Exception $e) {
            Log::error("DB cleanup failed for creator ID {$this->creatorId}: " . $e->getMessage());
        }
    }
}
