<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->whereIn('key', [
                'footer_text',
                'social_facebook',
                'social_twitter',
                'social_instagram',
            ])
            ->delete();
    }

    public function down(): void
    {
        $now = now();

        DB::table('settings')->upsert([
            [
                'key' => 'footer_text',
                'value' => 'Desarrollado con Rüweb',
                'type' => 'text',
                'group' => 'general',
                'options' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'options' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'options' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'options' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['key'], ['value', 'type', 'group', 'options', 'updated_at']);
    }
};
