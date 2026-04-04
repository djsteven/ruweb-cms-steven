<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CmsAuditOrphanedMediaCommand extends Command
{
    protected $signature = 'cms:media:audit-orphans {--delete : Delete orphaned media records and files}';
    protected $description = 'Audit media not referenced by mediables or media-type settings';

    public function handle(): int
    {
        $mediaSettingIds = Setting::query()
            ->where('type', 'media')
            ->whereNotNull('value')
            ->pluck('value')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        $orphans = Media::query()
            ->leftJoin('mediables', 'media.id', '=', 'mediables.media_id')
            ->whereNull('mediables.media_id')
            ->when(! empty($mediaSettingIds), function ($query) use ($mediaSettingIds) {
                $query->whereNotIn('media.id', $mediaSettingIds);
            })
            ->orderByDesc('media.created_at')
            ->select('media.*')
            ->get();

        if ($orphans->isEmpty()) {
            $this->info('No orphaned media found.');

            return Command::SUCCESS;
        }

        $this->warn("Found {$orphans->count()} orphaned media items:");

        $rows = $orphans->map(fn ($item) => [
            $item->id,
            $item->original_filename,
            $item->mime_type,
            $item->path,
        ])->all();

        $this->table(['ID', 'Filename', 'MIME', 'Path'], $rows);

        if (! $this->option('delete')) {
            $this->line('Run again with --delete to remove records and files.');

            return Command::SUCCESS;
        }

        foreach ($orphans as $orphan) {
            Storage::disk($orphan->disk)->delete($orphan->path);
            $orphan->delete();
        }

        $this->info("Deleted {$orphans->count()} orphaned media records.");

        return Command::SUCCESS;
    }
}
