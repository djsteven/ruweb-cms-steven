<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class GoogleReviewsSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'     => 'serpapi_key',
                'value'   => null,
                'type'    => 'password',
                'group'   => 'general',
                'options' => null,
            ],
            [
                'key'     => 'google_place_id',
                'value'   => null,
                'type'    => 'string',
                'group'   => 'general',
                'options' => null,
            ],
        ];

        foreach ($settings as $data) {
            if (! Setting::where('key', $data['key'])->exists()) {
                Setting::create($data);
            }
        }

        // Eliminar claves antiguas de la Places API (ya no se usan)
        Setting::whereIn('key', ['google_places_api_key'])->delete();
    }
}
