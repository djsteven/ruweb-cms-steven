<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Locale;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'inicio', 'locale' => Locale::baseCode()],
            [
                'title' => 'Inicio',
                'template_key' => 'home',
                'content_json' => [
                    'sections' => [
                        'hero' => [
                            'is_visible' => true,
                            'heading' => 'Bienvenido a Rüweb',
                            'body' => 'Este es un starter CMS rápido, flexible y extensible.',
                        ],
                        'features' => [
                            'is_visible' => true,
                            'heading' => 'Listo para editar',
                            'body' => 'Personalizá esta portada desde el panel de administración.',
                        ],
                        'cta' => [
                            'is_visible' => true,
                            'heading' => 'Empezá ahora',
                            'body' => 'Creá páginas, posts y ajustá la home en Settings.',
                        ],
                    ],
                ],
                'status' => 'published',
                'published_at' => now(),
                'created_by' => null,
                'updated_by' => null,
            ]
        );

        $page->source_fingerprint = $page->translatableFingerprint();
        $page->save();

        Setting::set('homepage_translation_group_id', $page->translation_group_id);
    }
}
