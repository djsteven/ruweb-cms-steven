<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $baseLocale = config('cms.locales.default', 'es');

        $this->upgradeContentTable('pages', $baseLocale, 'pages_slug_unique', ['locale', 'slug']);
        $this->upgradeContentTable('posts', $baseLocale, 'posts_slug_unique', ['locale', 'slug']);
        $this->upgradeContentTable('taxonomies', $baseLocale, 'taxonomies_slug_type_unique', ['locale', 'slug', 'type']);

        if (! Schema::hasColumn('menus', 'locale')) {
            Schema::table('menus', function (Blueprint $table) use ($baseLocale) {
                $table->string('locale', 10)->default($baseLocale)->index()->after('id');
            });

            $this->dropUniqueIfExists('menus', 'menus_slug_unique');
            Schema::table('menus', function (Blueprint $table) {
                $table->unique(['locale', 'slug']);
                $table->unique(['locale', 'location']);
            });
        }

        if (! Schema::hasColumn('menu_items', 'translation_status')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->string('translation_status', 30)->nullable()->index()->after('target');
            });
        }

        $this->backfillBaseLocale($baseLocale);
        $this->backfillHomepageGroup();

        DB::table('settings')->where('key', 'homepage_slug')->delete();
    }

    private function backfillBaseLocale(string $baseLocale): void
    {
        if (! Schema::hasTable('locales') || DB::table('locales')->exists()) {
            return;
        }

        // A fresh install seeds locales via LocaleSeeder after migrating; only
        // pre-existing installs (which already have content) need a backfill.
        if (! Schema::hasTable('pages') || ! DB::table('pages')->exists()) {
            return;
        }

        $catalog = config('cms.locales.catalog', []);

        DB::table('locales')->insert([
            'code' => $baseLocale,
            'name' => $catalog[$baseLocale] ?? strtoupper($baseLocale),
            'is_base' => true,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function backfillHomepageGroup(): void
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('pages', 'translation_group_id')) {
            return;
        }

        $existing = DB::table('settings')->where('key', 'homepage_translation_group_id')->first();
        if ($existing && $existing->value) {
            return;
        }

        $homepageSlug = optional(DB::table('settings')->where('key', 'homepage_slug')->first())->value ?: 'inicio';
        $homepage = DB::table('pages')->where('slug', $homepageSlug)->first();

        if (! $homepage || empty($homepage->translation_group_id)) {
            return;
        }

        if ($existing) {
            DB::table('settings')
                ->where('key', 'homepage_translation_group_id')
                ->update(['value' => $homepage->translation_group_id, 'updated_at' => now()]);

            return;
        }

        DB::table('settings')->insert([
            'key' => 'homepage_translation_group_id',
            'value' => $homepage->translation_group_id,
            'type' => 'string',
            'group' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }

    private function upgradeContentTable(string $tableName, string $baseLocale, string $oldUniqueName, array $newUniqueColumns): void
    {
        if (! Schema::hasColumn($tableName, 'locale')) {
            Schema::table($tableName, function (Blueprint $table) use ($baseLocale) {
                $table->string('locale', 10)->default($baseLocale)->index()->after('id');
                $table->uuid('translation_group_id')->nullable()->index()->after('locale');
                $table->string('translation_status', 30)->nullable()->index()->after('translation_group_id');
                $table->string('source_fingerprint', 64)->nullable()->after('translation_status');
            });

            DB::table($tableName)
                ->whereNull('translation_group_id')
                ->orderBy('id')
                ->get()
                ->each(function ($row) use ($tableName) {
                    DB::table($tableName)
                        ->where('id', $row->id)
                        ->update(['translation_group_id' => (string) Str::uuid()]);
                });

            $this->dropUniqueIfExists($tableName, $oldUniqueName);

            Schema::table($tableName, function (Blueprint $table) use ($newUniqueColumns) {
                $table->unique($newUniqueColumns);
            });
        }
    }

    private function dropUniqueIfExists(string $tableName, string $indexName): void
    {
        try {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        } catch (Throwable) {
            //
        }
    }
};
