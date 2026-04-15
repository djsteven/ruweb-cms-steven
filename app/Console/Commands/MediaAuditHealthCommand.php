<?php

namespace App\Console\Commands;

use App\Services\Media\MediaHealthService;
use Illuminate\Console\Command;

class MediaAuditHealthCommand extends Command
{
    protected $signature = 'media:audit-health';
    protected $description = 'Audit media optimization and responsive coverage health from live DB + storage state.';

    public function __construct(
        protected MediaHealthService $health
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $summary = $this->health->summary();
        $detail = $this->health->detailed();

        $this->info('Media Health Summary');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total media', $summary['total_media']],
                ['Optimizable images', $summary['raster_images']],
                ['WebP coverage %', $summary['webp_coverage_percent']],
                ['Responsive coverage %', $summary['responsive_coverage_percent']],
                ['Bytes saved', $summary['bytes_saved']],
                ['Missing physical files', $summary['missing_files']],
            ]
        );

        $this->info('Media Health Detail');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total optimizable images', $detail['total_raster_eligible']],
                ['Total optimized', $detail['total_optimized']],
                ['Total with variants', $detail['total_with_variants']],
                ['Total saved (MB)', $detail['total_saved_mb']],
                ['Average reduction %', $detail['average_reduction_ratio']],
                ['Preserved originals', $detail['preserved_originals']],
                ['Missing physical files', $detail['missing_physical_files']],
                ['Missing width/height', $detail['missing_dimensions']],
                ['Missing variants', $detail['missing_variants']],
            ]
        );

        if (! empty($detail['top_savings'])) {
            $this->info('Top images by bytes saved');
            $this->table(
                ['Media ID', 'Filename', 'Saved bytes', 'Saved %'],
                collect($detail['top_savings'])->map(fn ($row) => [
                    $row['id'],
                    $row['filename'],
                    $row['saved_bytes'],
                    $row['saved_percent'],
                ])->all()
            );
        }

        if (! empty($detail['top_pending_optimization'])) {
            $this->info('Top pending optimization');
            $this->table(
                ['Media ID', 'Filename', 'Extension', 'Size (bytes)'],
                collect($detail['top_pending_optimization'])->map(fn ($row) => [
                    $row['id'],
                    $row['filename'],
                    $row['extension'],
                    $row['size'],
                ])->all()
            );
        }

        return Command::SUCCESS;
    }
}
