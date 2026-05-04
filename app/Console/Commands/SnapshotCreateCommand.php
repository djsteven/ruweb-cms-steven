<?php

namespace App\Console\Commands;

use App\Services\Snapshots\SnapshotException;
use App\Services\Snapshots\SnapshotService;
use Illuminate\Console\Command;

class SnapshotCreateCommand extends Command
{
    protected $signature = 'snapshot:create {--name=}';

    protected $description = 'Create a portable .appbackup snapshot with database data and public uploads.';

    public function __construct(
        protected SnapshotService $snapshots
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $path = $this->snapshots->create($this->option('name'));
        } catch (SnapshotException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->info("Snapshot created: {$path}");

        return Command::SUCCESS;
    }
}
