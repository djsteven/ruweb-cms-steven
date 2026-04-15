<?php

namespace App\Services\Media;

use RuntimeException;

class ImageOptimizer
{
    public function optimizeToWebp(string $sourcePath, string $targetPath, int $quality = 80, ?int $maxWidth = null): array
    {
        if (! function_exists('imagewebp')) {
            throw new RuntimeException('GD WebP support is not available on this server.');
        }

        [$sourceImage, $sourceWidth, $sourceHeight, $sourceExtension] = $this->createImageResource($sourcePath);

        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;

        if ($maxWidth && $maxWidth > 0 && $sourceWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = (int) round(($sourceHeight / $sourceWidth) * $targetWidth);
        }

        $canvas = $sourceImage;
        if ($targetWidth !== $sourceWidth || $targetHeight !== $sourceHeight) {
            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($sourceExtension === 'png') {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
            }

            imagecopyresampled(
                $canvas,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );
        }

        $targetDir = dirname($targetPath);
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
            throw new RuntimeException("Unable to create directory: {$targetDir}");
        }

        if (! imagewebp($canvas, $targetPath, $quality)) {
            if ($canvas !== $sourceImage) {
                imagedestroy($canvas);
            }
            imagedestroy($sourceImage);
            throw new RuntimeException('Unable to encode image as WebP.');
        }

        if ($canvas !== $sourceImage) {
            imagedestroy($canvas);
        }
        imagedestroy($sourceImage);

        return [
            'width' => $targetWidth,
            'height' => $targetHeight,
            'size' => filesize($targetPath) ?: 0,
            'extension' => 'webp',
            'mime_type' => 'image/webp',
        ];
    }

    public function readDimensions(string $path): ?array
    {
        $info = @getimagesize($path);
        if (! $info || ! isset($info[0], $info[1])) {
            return null;
        }

        return [
            'width' => (int) $info[0],
            'height' => (int) $info[1],
        ];
    }

    public function generateResponsiveVariants(
        string $sourcePath,
        string $variantsDirectory,
        string $baseName,
        array $widths,
        int $quality = 80,
        bool $force = false
    ): array {
        if (! function_exists('imagewebp')) {
            throw new RuntimeException('GD WebP support is not available on this server.');
        }

        [$sourceImage, $sourceWidth, $sourceHeight] = $this->createImageResource($sourcePath);

        $widths = collect($widths)
            ->map(fn ($width) => (int) $width)
            ->filter(fn ($width) => $width > 0 && $width <= $sourceWidth)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (! is_dir($variantsDirectory) && ! mkdir($variantsDirectory, 0775, true) && ! is_dir($variantsDirectory)) {
            imagedestroy($sourceImage);
            throw new RuntimeException("Unable to create variants directory: {$variantsDirectory}");
        }

        $variants = [];

        foreach ($widths as $width) {
            $height = (int) round(($sourceHeight / $sourceWidth) * $width);
            $variantFile = "{$baseName}-{$width}w.webp";
            $variantPath = rtrim($variantsDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $variantFile;

            if (! $force && file_exists($variantPath)) {
                $variants[] = [
                    'width' => $width,
                    'height' => $height,
                    'filename' => $variantFile,
                    'size' => filesize($variantPath) ?: 0,
                ];
                continue;
            }

            $canvas = imagecreatetruecolor($width, $height);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
            imagecopyresampled($canvas, $sourceImage, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

            if (! imagewebp($canvas, $variantPath, $quality)) {
                imagedestroy($canvas);
                imagedestroy($sourceImage);
                throw new RuntimeException("Unable to encode responsive variant: {$variantFile}");
            }

            imagedestroy($canvas);

            $variants[] = [
                'width' => $width,
                'height' => $height,
                'filename' => $variantFile,
                'size' => filesize($variantPath) ?: 0,
            ];
        }

        imagedestroy($sourceImage);

        return $variants;
    }

    public function bytesSaved(?int $originalSize, ?int $finalSize): ?int
    {
        if ($originalSize === null || $finalSize === null || $originalSize <= 0) {
            return null;
        }

        return max($originalSize - $finalSize, 0);
    }

    public function optimizationRatio(?int $originalSize, ?int $finalSize): ?float
    {
        $saved = $this->bytesSaved($originalSize, $finalSize);
        if ($saved === null || $originalSize <= 0) {
            return null;
        }

        return round(($saved / $originalSize) * 100, 2);
    }

    protected function createImageResource(string $sourcePath): array
    {
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

        $resource = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($sourcePath),
            'png' => @imagecreatefrompng($sourcePath),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if (! $resource) {
            throw new RuntimeException("Unsupported image format for GD: .{$extension}");
        }

        $width = imagesx($resource);
        $height = imagesy($resource);

        return [$resource, $width, $height, $extension];
    }
}

