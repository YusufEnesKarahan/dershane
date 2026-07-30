<?php

namespace App\Domain\HQ\Services\Backup;

use App\Models\HQBackupSnapshot;
use App\Models\HQBackupJob;

class SnapshotService
{
    /**
     * Register a new snapshot.
     */
    public function registerSnapshot(HQBackupJob $job, string $type, string $path, int $sizeBytes): HQBackupSnapshot
    {
        // Compute expiration date based on retention rules of the policy
        $expiresAt = null;
        if ($job->policy && $job->policy->retentionRules->isNotEmpty()) {
            $expiresAt = $this->calculateExpirationDate($job->policy->retentionRules);
        }

        return HQBackupSnapshot::create([
            'hq_backup_job_id' => $job->id,
            'type' => $type,
            'path' => $path,
            'size_bytes' => $sizeBytes,
            'checksum' => md5(uniqid()), // Simulated checksum for now
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Calculate expiration date based on rules.
     */
    protected function calculateExpirationDate($rules)
    {
        $longestDate = now();
        $keepForever = false;

        foreach ($rules as $rule) {
            switch ($rule->rule_type) {
                case '24_hour':
                    $date = now()->addHours(24);
                    break;
                case '7_day':
                    $date = now()->addDays(7);
                    break;
                case '30_day':
                    $date = now()->addDays(30);
                    break;
                case '90_day':
                    $date = now()->addDays(90);
                    break;
                case '365_day':
                    $date = now()->addDays(365);
                    break;
                case 'keep_forever':
                    $keepForever = true;
                    break;
                default:
                    $date = now()->addDays(30);
            }

            if (!$keepForever && isset($date) && $date > $longestDate) {
                $longestDate = $date;
            }
        }

        return $keepForever ? null : $longestDate;
    }
}
