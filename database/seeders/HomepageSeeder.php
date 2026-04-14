<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'inicio'],
            [
                'title' => 'Inicio',
                'template_key' => 'home',
                'content_json' => [
                    'sections' => [
                        'hero' => [
                            'is_visible' => true,
                            'heading' => 'Bienvenido a FlaxtCMS',
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
    }
}
