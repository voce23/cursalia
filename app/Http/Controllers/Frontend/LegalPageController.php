<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\View\View;

/**
 * Páginas legales de Cursalia.
 *
 * Ahora editables desde admin: el contenido vive en custom_pages (slug
 * "legal/{slug}"). Si el admin no las ha creado, este controlador hace
 * fallback al HTML por defecto incluido aquí abajo (para que el sitio
 * NUNCA se vea roto, ni recién descargado).
 */
class LegalPageController extends Controller
{
    /** Páginas válidas con sus textos de fallback (si no están en BD aún). */
    private function defaults(): array
    {
        return [
            'privacy' => [
                'title' => 'Política de privacidad',
                'intro' => 'Cómo recopilamos, usamos y protegemos tus datos personales.',
                'body' => $this->privacyBody(),
            ],
            'terms' => [
                'title' => 'Términos y condiciones',
                'intro' => 'Las reglas básicas para usar la plataforma como estudiante o instructor.',
                'body' => $this->termsBody(),
            ],
            'data-deletion' => [
                'title' => 'Eliminación de datos',
                'intro' => 'Cómo puedes eliminar tu cuenta y todos tus datos personales.',
                'body' => $this->dataDeletionBody(),
            ],
            'refunds' => [
                'title' => 'Política de reembolsos',
                'intro' => 'Condiciones para solicitar el reembolso de un curso adquirido.',
                'body' => $this->refundsBody(),
            ],
        ];
    }

    public function show(string $slug): View
    {
        $defaults = $this->defaults();
        abort_unless(isset($defaults[$slug]), 404);

        // Intentar leer la versión editada en custom_pages.
        $page = CustomPage::query()
            ->where('slug', "legal/{$slug}")
            ->where('status', true)
            ->first();

        $data = $defaults[$slug];

        return view('frontend.pages.legal', [
            'title' => $page?->title ?: $data['title'],
            'intro' => $page?->seo_description ?: $data['intro'],
            'body' => $page?->description ?: $data['body'],
            'updated' => $page?->updated_at?->translatedFormat('F \d\e Y'),
        ]);
    }

    public function privacyBody(): string
    {
        return <<<'HTML'
<p>Esta política explica qué datos recogemos cuando usas nuestra plataforma, para qué los usamos y qué derechos tienes sobre ellos.</p>
<h2>1. Qué datos recogemos</h2>
<ul>
  <li><strong>Datos que tú nos das</strong>: nombre, correo, foto de perfil, biografía si te registras como instructor, y cualquier información que añadas en tu perfil.</li>
  <li><strong>Datos de uso</strong>: cursos en los que te inscribes, lecciones completadas, reseñas, comentarios.</li>
  <li><strong>Datos técnicos</strong>: dirección IP, tipo de dispositivo y navegador, cookies de sesión y de preferencias.</li>
</ul>
<h2>2. Para qué los usamos</h2>
<ul>
  <li>Darte acceso a tus cursos y guardar tu progreso.</li>
  <li>Enviarte notificaciones sobre tu actividad (nuevos cursos, recordatorios).</li>
  <li>Mejorar la plataforma mediante estadísticas anónimas.</li>
  <li>Cumplir con obligaciones legales (facturación, fraude).</li>
</ul>
<h2>3. Con quién los compartimos</h2>
<p>No vendemos tus datos. Solo los compartimos con proveedores estrictamente necesarios para operar el servicio.</p>
<h2>4. Tus derechos</h2>
<p>En cualquier momento puedes acceder, corregir o eliminar tus datos desde tu perfil, u oponerte al envío de comunicaciones de marketing.</p>
HTML;
    }

    public function termsBody(): string
    {
        return <<<'HTML'
<p>Al usar esta plataforma aceptas estos términos. Si no estás de acuerdo, por favor no la uses.</p>
<h2>1. Tu cuenta</h2>
<ul>
  <li>Debes tener al menos 16 años para crear una cuenta.</li>
  <li>Tu cuenta es personal e intransferible.</li>
  <li>Eres responsable de la actividad realizada con tu cuenta.</li>
</ul>
<h2>2. Uso correcto</h2>
<p>Está prohibido subir contenido ilegal, hacer scraping masivo, o suplantar a otra persona.</p>
<h2>3. Propiedad intelectual</h2>
<p>La marca, el diseño y el código son nuestros. El contenido de cada curso pertenece a su autor.</p>
HTML;
    }

    public function dataDeletionBody(): string
    {
        return <<<'HTML'
<p>Tienes derecho a eliminar tu cuenta y todos los datos personales asociados en cualquier momento.</p>
<h2>Qué se elimina</h2>
<ul>
  <li>Tu perfil completo, inscripciones, progreso, reseñas y comentarios.</li>
</ul>
<h2>Cómo solicitarlo</h2>
<p>Desde tu perfil: <strong>Mi perfil → Configuración → Eliminar cuenta</strong>.</p>
<p>O por correo: escríbenos desde el email asociado a tu cuenta con el asunto "Eliminación de datos". Procesamos en máximo 30 días.</p>
HTML;
    }

    public function refundsBody(): string
    {
        return <<<'HTML'
<p>Esta política aplica a los cursos de pago.</p>
<h2>Garantía de 14 días</h2>
<p>Tienes <strong>14 días naturales</strong> desde la compra para solicitar el reembolso, siempre que no hayas completado más del <strong>25% de las lecciones</strong>.</p>
<h2>Cómo solicitarlo</h2>
<p>Desde <strong>Mi perfil → Compras → Solicitar reembolso</strong>. Te devolvemos el importe al mismo método de pago en 5–10 días hábiles.</p>
<h2>Cursos gratuitos</h2>
<p>No tienen política de reembolso por motivos obvios.</p>
HTML;
    }
}
