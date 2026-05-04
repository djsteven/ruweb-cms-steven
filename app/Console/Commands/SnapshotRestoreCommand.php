<?php

namespace App\Console\Commands;

use App\Services\Snapshots\SnapshotException;
use App\Services\Snapshots\SnapshotService;
use Illuminate\Console\Command;

class SnapshotRestoreCommand extends Command
{
    protected $signature = 'snapshot:restore {archive} {--force}';

    protected $description = 'Restore a portable .appbackup snapshot into the current database and public uploads.';

    public function __construct(
        protected SnapshotService $snapshots
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $result = $this->snapshots->restore(
                (string) $this->argument('archive'),
                (bool) $this->option('force')
            );
        } catch (SnapshotException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->info('Snapshot restored.');
        $this->line('Pre-restore backup: '.$result['pre_restore_backup']);
        $this->line('Tables restored: '.$result['tables']);
        $this->line('Upload files restored: '.$result['uploads_files']);

        return Command::SUCCESS;
    }
}
