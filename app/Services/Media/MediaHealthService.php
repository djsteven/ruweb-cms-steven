<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaHealthService
{
    public function summary(): array
    {
        $totalMedia = Media::count();
        $rasterMedia = Media::query()
            ->whereIn('extension', MediaImagePipeline::VARIANT_EXTENSIONS)
            ->get();

        $rasterCount = $rasterMedia->count();
        $webpCount = $rasterMedia->where('extension', 'webp')->count();
        $responsiveCount = $rasterMedia->filter(fn (Media $media) => $media->hasResponsiveVariants())->count();
        $missingFiles = $this->countMissingPhysicalFiles(Media::all());
        $bytesSaved = $rasterMedia->sum(fn (Media $media) => $media->bytesSaved() ?? 0);

        return [
            'total_media' => $totalMedia,
            'raster_images' => $rasterCount,
            'webp_coverage_percent' => $rasterCount > 0 ? round(($webpCount / $rasterCount) * 100, 2) : 0.0,
            'responsive_coverage_percent' => $rasterCount > 0 ? round(($responsiveCount / $rasterCount) * 100, 2) : 0.0,
            'bytes_saved' => $bytesSaved,
            'missing_files' => $missingFiles,
        ];
    }

    public function detailed(): array
    {
        $rasterMedia = Media::query()
            ->whereIn('extension', MediaImagePipeline::VARIANT_EXTENSIONS)
            ->get();

        $optimized = $rasterMedia->filter(fn (Media $media) => $media->isOptimizedRaster());
        $withVariants = $rasterMedia->filter(fn (Media $media) => $media->hasResponsiveVariants());
        $withoutDimensions = $rasterMedia->filter(fn (Media $media) => ! $media->width || ! $media->height);
        $withoutVariants = $rasterMedia->filter(fn (Media $media) => ! $media->hasResponsiveVariants());
        $missingPhysical = $this->countMissingPhysicalFiles(Media::all());
        $totalSaved = $optimized->sum(fn (Media $media) => $media->bytesSaved() ?? 0);
        $avgRatio = round((float) $optimized
            ->map(fn (Media $media) => $media->optimizationRatio())
            ->filter(fn ($ratio) => $ratio !== null)
            ->avg(), 2);

        $topSavings = $optimized
            ->sortByDesc(fn (Media $media) => $media->bytesSaved() ?? 0)
            ->take(10)
            ->values()
            ->map(fn (Media $media) => [
                'id' => $media->id,
                'filename' => $media->original_filename,
                'saved_bytes' => $media->bytesSaved() ?? 0,
                'saved_percent' => $media->optimizationRatio() ?? 0,
            ])
            ->all();

        $topPending = Media::query()
            ->whereIn('extension', MediaImagePipeline::OPTIMIZABLE_EXTENSIONS)
            ->orderByDesc('size')
            ->take(10)
            ->get()
            ->map(fn (Media $media) => [
                'id' => $media->id,
                'filename' => $media->original_filename,
                'extension' => $media->extension,
                'size' => $media->size,
            ])
            ->all();

        return [
            'total_raster_eligible' => $rasterMedia->count(),
            'total_optimized' => $optimized->count(),
            'total_with_variants' => $withVariants->count(),
            'total_saved_bytes' => $totalSaved,
            'total_saved_mb' => round($totalSaved / 1048576, 2),
            'average_reduction_ratio' => $avgRatio,
            'preserved_originals' => Media::query()->whereNotNull('original_path')->count(),
            'missing_physical_files' => $missingPhysical,
            'missing_dimensions' => $withoutDimensions->count(),
            'missing_variants' => $withoutVariants->count(),
            'top_savings' => $topSavings,
            'top_pending_optimization' => $topPending,
        ];
    }

    protected function countMissingPhysicalFiles($mediaCollection): int
    {
        $missing = 0;

        foreach ($mediaCollection as $media) {
            try {
                if (! Storage::disk($media->disk)->exists($media->path)) {
                    $missing++;
                }
            } catch (\Throwable) {
                $missing++;
            }
        }

        return $missing;
    }
}

