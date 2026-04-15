<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\Media\MediaImagePipeline;
use Illuminate\Console\Command;

class MediaGenerateVariantsCommand extends Command
{
    protected $signature = 'media:generate-variants {--dry-run} {--force}';
    protected $description = 'Backfill dimensions and generate responsive image variants for eligible media.';

    public function __construct(
        protected MediaImagePipeline $pipeline
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $dimensionsUpdated = 0;
        $variantsGenerated = 0;
        $skipped = 0;

        $items = Media::query()
            ->whereIn('extension', MediaImagePipeline::VARIANT_EXTENSIONS)
            ->orderBy('id')
            ->get();

        foreach ($items as $media) {
            $dimensionsResult = $this->pipeline->refreshDimensions($media, $dryRun);
            if (in_array($dimensionsResult['status'] ?? null, ['updated', 'dry-run'], true)) {
                $dimensionsUpdated++;
            }

            $variantsResult = $this->pipeline->generateVariants($media, $force, $dryRun);
            $status = $variantsResult['status'] ?? 'skipped';
            if ($status === 'generated' || $status === 'dry-run') {
                $variantsGenerated++;
                continue;
            }

            if ($status !== 'already_exists') {
                $reason = $variantsResult['reason'] ?? 'unknown';
                $this->warn("Skipped media #{$media->id}: {$reason}");
                $skipped++;
            }
        }

        $mode = $dryRun ? 'DRY-RUN' : 'LIVE';
        $this->info("[{$mode}] Dimensions processed: {$dimensionsUpdated}");
        $this->info("[{$mode}] Variant jobs processed: {$variantsGenerated}");
        $this->info("[{$mode}] Skipped: {$skipped}");

        return Command::SUCCESS;
    }
}

