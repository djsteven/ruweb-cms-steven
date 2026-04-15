<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\Media\MediaImagePipeline;
use Illuminate\Console\Command;

class MediaConvertWebpCommand extends Command
{
    protected $signature = 'media:convert-webp {--dry-run}';
    protected $description = 'Convert legacy JPG/JPEG/PNG media to WebP and persist optimization traceability.';

    public function __construct(
        protected MediaImagePipeline $pipeline
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $converted = 0;
        $skipped = 0;

        $items = Media::query()
            ->whereIn('extension', MediaImagePipeline::OPTIMIZABLE_EXTENSIONS)
            ->orderBy('id')
            ->get();

        foreach ($items as $media) {
            $result = $this->pipeline->convertToWebp($media, $dryRun);
            $status = $result['status'] ?? 'skipped';

            if ($status === 'converted' || $status === 'dry-run') {
                $converted++;
                continue;
            }

            $reason = $result['reason'] ?? 'unknown';
            $skipped++;
            $this->warn("Skipped media #{$media->id}: {$reason}");
        }

        $mode = $dryRun ? 'DRY-RUN' : 'LIVE';
        $this->info("[{$mode}] Converted candidates: {$converted}");
        $this->info("[{$mode}] Skipped: {$skipped}");
        $this->line('Recommended next step: php artisan media:generate-variants');

        return Command::SUCCESS;
    }
}

