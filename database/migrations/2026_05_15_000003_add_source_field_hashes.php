<?php

use App\Models\Page;
use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumn('pages');
        $this->addColumn('posts');
        $this->addColumn('taxonomies');

        $this->backfill(Page::class, 'pages');
        $this->backfill(Post::class, 'posts');
        $this->backfill(Taxonomy::class, 'taxonomies');
    }

    public function down(): void
    {
        foreach (['pages', 'posts', 'taxonomies'] as $tableName) {
            if (Schema::hasColumn($tableName, 'source_field_hashes')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('source_field_hashes');
                });
            }
        }
    }

    private function addColumn(string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'source_field_hashes')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->json('source_field_hashes')->nullable()->after('source_fingerprint');
            });
        }
    }

    /**
     * @param class-string<Model> $modelClass
     */
    private function backfill(string $modelClass, string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'translation_group_id')) {
            return;
        }

        $baseLocale = config('cms.locales.default', 'es');

        $modelClass::query()
            ->where('locale', $baseLocale)
            ->orderBy('id')
            ->each(function (Model $base) use ($modelClass, $tableName): void {
                $fingerprint = $base->translatableFingerprint();
                $fieldHashes = $base->translatableFieldFingerprints();

                if ($fingerprint === null || $fieldHashes === []) {
                    return;
                }

                $encoded = json_encode($fieldHashes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                DB::table($tableName)
                    ->where('id', $base->getKey())
                    ->update(['source_field_hashes' => $encoded]);

                if (! $base->translation_group_id) {
                    return;
                }

                $modelClass::query()
                    ->where('translation_group_id', $base->translation_group_id)
                    ->where('locale', '!=', $base->locale)
                    ->where('source_fingerprint', $fingerprint)
                    ->orderBy('id')
                    ->each(function (Model $translation) use ($encoded, $tableName): void {
                        DB::table($tableName)
                            ->where('id', $translation->getKey())
                            ->update(['source_field_hashes' => $encoded]);
                    });
            });
    }
};
