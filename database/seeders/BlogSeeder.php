<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first admin or create one
        $admin = Admin::first();
        
        if (!$admin) {
            $admin = Admin::create([
                'name' => 'Admin',
                'email' => 'admin@lmsl13.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Create blog categories
        $categories = [
            [
                'name' => 'Tutoriales',
                'slug' => 'tutoriales',
                'color' => '#4f46e5',
            ],
            [
                'name' => 'Noticias',
                'slug' => 'noticias',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Tips & Trucos',
                'slug' => 'tips-trucos',
                'color' => '#ec4899',
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $catData) {
            $categoryModels[] = BlogCategory::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'color' => $catData['color'],
                    'status' => true,
                ]
            );
        }

        // Create blog articles
        $images = ['blog/blog_69e966e77e417.webp', 'blog/blog_69e96ade7c14f.webp'];
        $imageIndex = 0;
        
        $articles = [
            [
                'title' => 'Cómo empezar con nuestro LMS',
                'summary' => 'Una guía completa para nuevos usuarios del sistema de gestión de aprendizaje.',
                'content' => '<h2>Introducción</h2><p>Bienvenido a nuestro LMS. Este sistema está diseñado para facilitar el aprendizaje en línea.</p><h2>Primeros pasos</h2><p>Para comenzar, asegúrate de crear tu cuenta y completar tu perfil.</p>',
                'category_id' => $categoryModels[0]->id,
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Nuevas características en la versión 13',
                'summary' => 'Descubre las mejoras y características nuevas que hemos agregado en la última versión.',
                'content' => '<h2>Mejoras en la Interfaz</h2><p>La interfaz ha sido rediseñada para mejorar la experiencia del usuario.</p><h2>Nuevas Funcionalidades</h2><p>Se han agregado nuevas herramientas de análisis y reportes.</p>',
                'category_id' => $categoryModels[1]->id,
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => '5 Tips para maximizar tu productividad',
                'summary' => 'Aprende cinco estrategias efectivas para optimizar tu tiempo de estudio en el LMS.',
                'content' => '<h2>Tip 1: Organización</h2><p>Mantén tus cursos organizados en carpetas.</p><h2>Tip 2: Horarios</h2><p>Establece horarios fijos para estudiar.</p><h2>Tip 3: Objetivos</h2><p>Define objetivos claros para cada lección.</p>',
                'category_id' => $categoryModels[2]->id,
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Certificaciones disponibles',
                'summary' => 'Información sobre los programas de certificación que ofrecemos.',
                'content' => '<h2>Certificados Profesionales</h2><p>Al completar nuestros programas, recibirás un certificado reconocido.</p><h2>Validación</h2><p>Todos los certificados son verificables en línea.</p>',
                'category_id' => $categoryModels[0]->id,
                'status' => 'published',
                'published_at' => now(),
            ],
        ];

        foreach ($articles as $articleData) {
            $image = $images[$imageIndex % count($images)];
            $imageIndex++;
            
            Blog::firstOrCreate(
                ['title' => $articleData['title']],
                [
                    'slug' => Str::slug($articleData['title']),
                    'admin_id' => $admin->id,
                    'blog_category_id' => $articleData['category_id'],
                    'summary' => $articleData['summary'],
                    'content' => $articleData['content'],
                    'status' => $articleData['status'],
                    'published_at' => $articleData['published_at'],
                    'thumbnail' => $image,
                ]
            );
        }
    }
}
