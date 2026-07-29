<?php

namespace App\Backup\Strategies;

use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Tasks\Cleanup\CleanupStrategy;

class KeepOnlyLatestStrategy extends CleanupStrategy
{
    public function deleteOldBackups(BackupCollection $backups): void
    {
        $amount = max(1, (int) ($this->config->get('backup.cleanup.keep_only_latest.amount') ?? 3));

        // Sort newest first, keep only the latest $amount, delete the rest
        $backups
            ->sortByDesc(fn ($backup) => $backup->date())
            ->slice($amount)
            ->each(fn ($backup) => $backup->delete());
    }
}
