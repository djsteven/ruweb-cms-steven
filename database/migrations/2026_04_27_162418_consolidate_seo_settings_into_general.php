<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'site_description')->delete();
        DB::table('settings')->where('key', 'default_meta_title')->delete();

        DB::table('settings')
            ->where('key', 'default_meta_description')
            ->update([
                'key'        => 'site_description',
                'group'      => 'general',
                'updated_at' => now(),
            ]);

        DB::table('settings')
            ->where('key', 'default_social_image')
            ->update([
                'group'      => 'general',
                'updated_at' => now(),
            ]);

        \App\Models\Setting::clearCache();
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('key', 'default_social_image')
            ->update([
                'group'      => 'seo',
                'updated_at' => now(),
            ]);

        DB::table('settings')
            ->where('key', 'site_description')
            ->update([
                'key'        => 'default_meta_description',
                'group'      => 'seo',
                'updated_at' => now(),
            ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'default_meta_title'],
            [
                'value'      => null,
                'type'       => 'string',
                'group'      => 'seo',
                'options'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'site_description'],
            [
                'value'      => 'Un CMS ligero y extensible para comenzar rápido.',
                'type'       => 'text',
                'group'      => 'general',
                'options'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        \App\Models\Setting::clearCache();
    }
};
