<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    public function run(?string $baseLocale = null): void
    {
        $baseLocale = $baseLocale ?: config('cms.locales.default', 'es');
        $catalog = Locale::catalog();

        Locale::updateOrCreate(
            ['code' => $baseLocale],
            [
                'name' => $catalog[$baseLocale] ?? strtoupper($baseLocale),
                'is_base' => true,
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 0,
            ]
        );
    }
}
