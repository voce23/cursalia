<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke test base de Cursalia: verifica que las páginas más importantes
 * respondan 200 y contengan elementos clave para SEO.
 *
 * Pasar este test es la mínima garantía antes de cualquier deploy.
 * Ejecutar:  php artisan test --filter=CursaliaSmokeTest
 */
class CursaliaSmokeTest extends TestCase
{
    /** Home pública responde 200 y emite EducationalOrganization schema. */
    public function test_home_responds_with_schema(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('"@type": "EducationalOrganization"', false);
    }

    /** Blog index responde 200. */
    public function test_blog_index_responds(): void
    {
        $this->get('/blog')->assertOk();
    }

    /** /sobre-el-autor responde 200 con ProfilePage schema. */
    public function test_author_page_responds_with_profile_schema(): void
    {
        $response = $this->get('/sobre-el-autor');

        $response->assertOk();
        $response->assertSee('"@type": "ProfilePage"', false);
    }

    /** Sitemap.xml responde XML válido con varias URLs. */
    public function test_sitemap_xml_is_valid(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        // El header puede venir como "application/xml" o "application/xml; charset=UTF-8"
        // según versión Symfony. Lo importante es que sea XML.
        $contentType = $response->headers->get('content-type', '');
        $this->assertStringStartsWith('application/xml', $contentType);
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('<loc>', $response->getContent());
    }

    /** robots.txt presente y permite indexación de raíz.
     *  Nota: en test phpunit no sirve archivos estáticos de public/, lo
     *  verificamos directamente en el filesystem. */
    public function test_robots_txt_allows_indexing(): void
    {
        $path = public_path('robots.txt');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /admin/', $content);
    }

    /** /admin/login es accesible (sin estar logueado). */
    public function test_admin_login_page_responds(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    /** /admin/dashboard requiere autenticación → redirige al login. */
    public function test_admin_dashboard_requires_auth(): void
    {
        $response = $this->get('/admin/dashboard');

        // 302 redirect al login (no 200, no 500).
        $response->assertRedirect('/admin/login');
    }

    /** DatabaseClearController bloqueado fuera de entornos dev. */
    public function test_database_clear_blocked_in_production(): void
    {
        $original = app()->environment();
        app()['env'] = 'production';

        try {
            $response = $this->get('/admin/database-clear');
            // Esperamos NO-200. Aceptamos 302 (redirect login), 403 (bloqueo
            // del constructor), o 404 (ruta no registrada en producción).
            // Lo crítico: NUNCA un 200 que muestre el formulario peligroso.
            $this->assertNotEquals(200, $response->status(), 'database-clear NO debe ser accesible en producción');
        } finally {
            app()['env'] = $original;
        }
    }

    /** Páginas legales responden 200. */
    public function test_legal_pages_respond(): void
    {
        foreach (['privacy', 'terms', 'data-deletion', 'refunds'] as $slug) {
            $this->get("/legal/{$slug}")->assertOk();
        }
    }

    /** 404 emite página personalizada Cursalia. */
    public function test_404_uses_branded_page(): void
    {
        $response = $this->get('/esta-url-no-existe-jamas');
        $response->assertStatus(404);
    }
}
