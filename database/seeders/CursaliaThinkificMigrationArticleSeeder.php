<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Artículo satélite #3 — Cómo migrar de Thinkific a tu propio dominio en 7 días.
 *
 * Objetivo SEO: keyword muy específica, baja competencia, intent comercial alto.
 *   - "migrar de Thinkific"
 *   - "alternativa a Thinkific"
 *   - "Thinkific dominio propio"
 *   - "exportar curso Thinkific"
 *   - "cancelar Thinkific"
 *
 * Estrategia: guía PRÁCTICA día a día. La gente busca "cómo + acción + tiempo"
 * y este artículo está perfectamente alineado con esa intención.
 * Cierra el cluster de comparativas y refuerza autoridad temática.
 */
class CursaliaThinkificMigrationArticleSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        $category = BlogCategory::firstWhere('slug', 'comparativas')
            ?? BlogCategory::create([
                'name' => 'Comparativas',
                'slug' => 'comparativas',
                'color' => '#FB7185',
                'status' => true,
            ]);

        $admin = Admin::orderBy('id')->first();

        $heroPath = 'blog/thinkific-migracion-hero.svg';
        Storage::disk('public')->put($heroPath, $this->buildHeroSvg());

        Blog::updateOrCreate(
            ['slug' => 'migrar-de-thinkific-a-tu-propio-dominio-en-7-dias'],
            [
                'admin_id' => $admin?->id,
                'blog_category_id' => $category->id,
                'title' => 'Cómo migrar de Thinkific a tu propio dominio en 7 días (sin perder alumnos)',
                'thumbnail' => $heroPath,
                'summary' => 'Plan día a día para salir de Thinkific con tus cursos, tus alumnos y tu lista intactos. Checklist completo, redirecciones DNS, exportación de datos y trampas que nadie te cuenta.',
                'content' => $this->buildContent(),
                'meta_title' => 'Migrar de Thinkific a dominio propio · plan de 7 días (2026)',
                'meta_description' => 'Plan paso a paso para salir de Thinkific en 7 días sin perder alumnos. Backup, redirecciones DNS, migración de contenido y comunicación al alumno.',
                'faq' => $this->buildFaq(),
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $this->command->info('  ✓ Artículo satélite #3 "Migrar de Thinkific" publicado.');
    }

    private function buildContent(): string
    {
        return <<<'HTML'
<p>Si llevas tiempo en <strong>Thinkific</strong> y te has dado cuenta de tres cosas, este artículo es para ti:</p>

<ol>
    <li>Cada mes pagas entre <strong>49 y 149$ de mensualidad</strong>, y la cuenta a fin de año es brutal.</li>
    <li>Tu academia se ve <em>parecida</em> a las otras 75.000 academias en Thinkific. Tu marca pesa cero.</li>
    <li>Tu lista de alumnos vive dentro de Thinkific. Si decides irte, sacarla es un dolor.</li>
</ol>

<p>La buena noticia: <strong>migrar fuera de Thinkific es factible en 7 días</strong> si lo haces ordenado. La mala: si lo haces mal, pierdes parte del tráfico orgánico (SEO), confundes a tus alumnos y rompes los enlaces que tenías compartidos.</p>

<p>Esta es la guía día a día que yo seguiría en 2026. Sin pasos opcionales, sin paja.</p>

<div class="learn-box">
    <p><i class="fa-solid fa-bullseye"></i> Lo que vas a aprender</p>
    <ul>
        <li>El checklist exacto antes de cancelar Thinkific (lo que NO puedes olvidar)</li>
        <li>El plan día a día para los 7 días de migración</li>
        <li>Cómo exportar tus cursos sin perder datos clave</li>
        <li>Cómo hacer las redirecciones 301 para no perder SEO</li>
        <li>Las 4 trampas más comunes que ahuyentan alumnos durante la migración</li>
    </ul>
</div>

<h2 id="cuanto-cuesta-thinkific">Antes de nada: ¿cuánto te cuesta Thinkific de verdad?</h2>

<p>Para que el esfuerzo valga la pena, hagamos primero la cuenta. Thinkific tiene 4 planes:</p>

<table>
    <thead>
        <tr>
            <th>Plan</th>
            <th>Coste mensual</th>
            <th>Coste anual</th>
            <th>Funcionalidad clave</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Free</td><td>0$</td><td>0$</td><td>1 curso, marca Thinkific, sin embudo</td></tr>
        <tr><td>Basic</td><td>49$</td><td>588$</td><td>Cursos ilimitados, sin marketing avanzado</td></tr>
        <tr><td>Pro</td><td>99$</td><td>1.188$</td><td>Membresías, certificados, comunidades</td></tr>
        <tr><td>Premier</td><td>199$</td><td>2.388$</td><td>Multi-instructor, API, white-label</td></tr>
    </tbody>
</table>

<p>Lo que muchos no calculan: <strong>Thinkific no cobra comisión por venta</strong> (a diferencia de Hotmart), pero la mensualidad fija sí o sí la pagas, vendas o no. Y si quieres "tu propio dominio" sin la URL <code>tu-academia.thinkific.com</code>, necesitas mínimo plan Basic (49$/mes).</p>

<div class="callout callout-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <p><strong>La trampa silenciosa:</strong> el plan Basic NO incluye marketing avanzado ni A/B testing. Para hacer crecer tu academia te empujan al Pro (99$/mes = 1.188$/año). En 3 años son 3.564$ de mensualidades.</p>
</div>

<p>Comparado con una plataforma propia que cuesta ~60€/año TOTAL, la diferencia en 3 años son <strong>~3.000€ que podrías estar reinvirtiendo en publicidad, contenidos o tu sueldo</strong>.</p>

<h2 id="checklist-antes">Checklist antes de empezar la migración</h2>

<p>Esto lo haces el "Día 0", antes de tocar nada. Si te saltas un punto, lo pagas.</p>

<ul>
    <li>☐ <strong>Decidir tu nueva plataforma.</strong> Cursalia, WordPress + LearnDash, TutorLMS, Moodle. (Te dejé una <a href="/blog/lms-open-source-espanol-2026-comparativa">comparativa honesta de 7 opciones</a>).</li>
    <li>☐ <strong>Comprar tu dominio propio</strong> si todavía no lo tienes. Recomendado: Namecheap o Cloudflare Registrar. ~12€/año.</li>
    <li>☐ <strong>Contratar hosting.</strong> Para empezar basta un compartido decente (Hostinger, SiteGround, Webempresa) por 50-80€/año.</li>
    <li>☐ <strong>Avisar a tu equipo</strong> si lo tienes (administradores, instructores invitados).</li>
    <li>☐ <strong>Reservar 7 días seguidos</strong> en tu calendario. La migración funciona si no te interrumpes a mitad.</li>
    <li>☐ <strong>Tener al día tus credenciales de Thinkific</strong> (admin), del registrador del dominio y del nuevo hosting.</li>
</ul>

<hr>

<h2 id="dia-1">Día 1 · Backup completo de Thinkific</h2>

<p>Tu objetivo de hoy: <strong>tener todo lo que vive en Thinkific descargado a tu disco</strong>. Sin esto, no migras nada.</p>

<h3 id="contenido-cursos">1. Exporta tus cursos (vídeos, PDFs, materiales)</h3>

<ol>
    <li>Admin Thinkific → <strong>Manage Learning Content</strong> → cada curso → cada lección.</li>
    <li>Descarga uno por uno los vídeos (clic derecho → "Guardar como…"). Los PDFs y archivos descargables están en la pestaña "Files" del curso.</li>
    <li>Organízalos en carpetas locales: <code>/cursos/curso-X/leccion-1/video.mp4</code>. Es tedioso pero crítico.</li>
</ol>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Tip:</strong> si tienes muchos vídeos, considera usar una extensión de navegador tipo "Video DownloadHelper" para Chrome o Firefox. Acelera la descarga masiva.</p>
</div>

<h3 id="alumnos-lista">2. Exporta tu lista de alumnos</h3>

<ol>
    <li>Admin Thinkific → <strong>Support Your Students</strong> → <strong>Users</strong>.</li>
    <li>Pulsa el botón <strong>"Export"</strong> arriba a la derecha → genera un CSV con todos.</li>
    <li>Guarda ese CSV en tu disco con nombre claro: <code>alumnos-thinkific-YYYY-MM-DD.csv</code>.</li>
</ol>

<p>El CSV te trae: nombre, email, fecha de registro, cursos inscritos, último login. <strong>NO trae</strong>: las contraseñas (lógico, son hash), ni el progreso detallado dentro de cada lección.</p>

<h3 id="ventas-historial">3. Exporta el historial de ventas</h3>

<ol>
    <li>Admin Thinkific → <strong>Settings</strong> → <strong>Payment & Tax</strong> → <strong>Orders</strong>.</li>
    <li>Descarga el CSV de los últimos 12-24 meses. Lo necesitas para tu contabilidad (declaración trimestral, anual).</li>
    <li>Guárdalo encriptado: contiene datos personales y de pago.</li>
</ol>

<h3 id="emails-disenos">4. Captura emails automatizados y diseños</h3>

<p>Esto se olvida y luego duele: dentro de Thinkific tienes secuencias de emails automáticas (bienvenida, recordatorio de lección, post-compra). <strong>Captúralos como texto</strong> en un documento. Vas a recrearlos en tu nuevo sistema.</p>

<hr>

<h2 id="dia-2">Día 2 · Configurar dominio + hosting</h2>

<h3 id="dominio">1. Apuntar el dominio al nuevo hosting</h3>

<p>Si compraste el dominio el Día 0, hoy lo apuntas:</p>

<ol>
    <li>Entra en tu registrador (Namecheap, Cloudflare).</li>
    <li>Modifica los <strong>DNS nameservers</strong> a los que te dé tu nuevo hosting (algo tipo <code>ns1.tuhosting.com</code>).</li>
    <li>Espera <strong>2-24 horas</strong> hasta que la propagación DNS termine. Verifica con <a href="https://dnschecker.org" target="_blank" rel="noopener">dnschecker.org</a>.</li>
</ol>

<div class="callout callout-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <p><strong>NO cambies todavía el dominio de Thinkific.</strong> Sigue apuntando ahí mientras montas el nuevo en paralelo. Lo cambias el Día 7.</p>
</div>

<h3 id="hosting">2. Instalar el LMS nuevo</h3>

<p>Según la plataforma que elegiste el Día 0:</p>

<ul>
    <li><strong>Cursalia:</strong> sigues el <a href="/blog?category=curso-cursalia">curso gratis del blog</a>, las primeras 3 lecciones cubren la instalación.</li>
    <li><strong>WordPress + LearnDash/TutorLMS:</strong> Hostinger y SiteGround tienen instaladores 1-click. 1 hora.</li>
    <li><strong>Moodle:</strong> también instalador 1-click en hostings tipo Webempresa. 2-3 horas con configuración inicial.</li>
</ul>

<p>Al final del Día 2 tienes: tu nueva academia VIVA en un dominio temporal (ej: <code>nueva.tudominio.com</code> o IP directa). Sigue Thinkific intacto.</p>

<hr>

<h2 id="dia-3">Día 3 · Subir el primer curso al nuevo sistema</h2>

<p>Hoy haces UN curso completo (el más importante o el más vendido). Esto te permite:</p>

<ul>
    <li>Detectar problemas del nuevo sistema antes de migrar todo.</li>
    <li>Calibrar cuánto tarda subir 1 curso → estimar el resto.</li>
    <li>Tener un ejemplo funcionando que enseñar a alumnos beta.</li>
</ul>

<p>Pasos:</p>

<ol>
    <li>Crea la categoría/sección del curso.</li>
    <li>Sube portada, descripción, precio (o "gratis" si vas a regalar acceso temporal).</li>
    <li>Crea las lecciones una a una. Sube los vídeos a tu nuevo player (o a Vimeo/YouTube unlisted si tu sistema no aloja vídeos).</li>
    <li>Adjunta PDFs y materiales descargables.</li>
    <li>Configura el "drip content" (libera lección 2 a los 3 días, lección 3 a la semana…) si lo usabas en Thinkific.</li>
</ol>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Mejor práctica:</strong> graba un vídeo de 1-2 minutos de bienvenida explicando que estás migrando. Lo subes como primera lección "intro". Eso genera confianza el día que invites a tus alumnos.</p>
</div>

<hr>

<h2 id="dia-4">Día 4 · Subir el resto de cursos</h2>

<p>Repite el Día 3 para los cursos restantes. Si tienes 5-10 cursos, hoy te lleva la jornada entera. Si son más, divide en Día 4 y Día 5 parcial.</p>

<p>Trucos para ir más rápido:</p>

<ul>
    <li>Usa la misma plantilla de descripción/portada de un curso a otro, cambiando solo los datos.</li>
    <li>Si tu LMS soporta importación masiva (CSV), hazlo así para listas de lecciones.</li>
    <li>No te obsesiones con la perfección visual hoy. Subes contenido. La estética la pules después.</li>
</ul>

<hr>

<h2 id="dia-5">Día 5 · Importar alumnos + email de aviso</h2>

<h3 id="importar-alumnos">1. Importar la lista de alumnos al nuevo sistema</h3>

<p>Carga el CSV que descargaste el Día 1. Cada LMS tiene su propia herramienta de importación:</p>

<ul>
    <li>Cursalia: <code>php artisan students:import alumnos.csv</code> (ver curso gratis para más detalle).</li>
    <li>WordPress + plugin: usa "Users Import Export WP" o equivalente.</li>
    <li>Moodle: <strong>Site administration → Users → Upload users</strong>.</li>
</ul>

<p><strong>Tus alumnos no tendrán contraseña en el nuevo sistema</strong> (los hashes son distintos). La solución estándar es: al importarlos generas un token de "primera vez" y en el email del Día 5 les dices que pulsen el botón para crear contraseña nueva.</p>

<h3 id="email-aviso">2. Email de aviso a tus alumnos</h3>

<p>Plantilla que te recomiendo, copia y adapta:</p>

<div class="callout callout-quote">
    <i class="fa-solid fa-envelope-open-text"></i>
    <p><strong>Asunto:</strong> [Importante] La academia se muda — nuevo acceso adjunto<br><br>
    Hola [Nombre],<br><br>
    Te escribo para avisarte que en los próximos días <strong>cambiamos la academia de plataforma</strong>. La estoy migrando a mi propio dominio (<a href="#">tudominio.com</a>) para mejorar tu experiencia, quitar limitaciones de Thinkific y bajar costes.<br><br>
    <strong>Qué tienes que hacer:</strong><br>
    1. Pulsa este botón para crear tu nueva contraseña: [BOTÓN]<br>
    2. Entra y verifica que ves tu curso. Avísame si falta algo.<br>
    3. A partir del [fecha], el acceso por Thinkific dejará de funcionar.<br><br>
    Tu progreso, tus certificados y tus accesos se mantienen. Si algo no funciona, escríbeme directamente a [email].<br><br>
    Gracias por seguir confiando.<br>
    [Tu nombre]</p>
</div>

<p>Programa el envío del email para mañana (Día 6) y dale a los alumnos <strong>al menos 2 días</strong> para que prueben el nuevo entorno antes de cerrar Thinkific.</p>

<hr>

<h2 id="dia-6">Día 6 · Redirecciones DNS + comunicación</h2>

<h3 id="dns-cambio">1. Apuntar el dominio principal al nuevo hosting</h3>

<p>Hoy es el día que cambias el DNS principal. Antes lo tenías apuntando a Thinkific, ahora lo apuntas a tu nuevo hosting.</p>

<ul>
    <li>Entra al registrador del dominio.</li>
    <li>Cambia los <strong>nameservers</strong> a los del nuevo hosting (los que te dieron).</li>
    <li>Espera 2-24 horas a que propague.</li>
</ul>

<h3 id="redirecciones-seo">2. Redirecciones 301 para no perder SEO</h3>

<p>Si Thinkific te genera URLs tipo <code>tudominio.com/courses/curso-x</code> y en el nuevo sistema son <code>tudominio.com/cursos/curso-x</code>, configura <strong>redirecciones 301</strong> en el nuevo hosting:</p>

<pre><code class="language-bash"># En tu .htaccess (Apache) o nginx.conf (Nginx)
Redirect 301 /courses/curso-x /cursos/curso-x
Redirect 301 /courses/curso-y /cursos/curso-y</code></pre>

<p>Esto le dice a Google "este contenido se ha movido permanentemente". Mantienes el ranking.</p>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Si tu dominio era <code>academia.thinkific.com</code></strong> y NUNCA tuviste dominio propio, no puedes redireccionar (no controlas ese subdominio). En ese caso pierdes SEO. Por eso es importante haber tenido dominio propio aunque fuera apuntando a Thinkific.</p>
</div>

<h3 id="envia-email">3. Envía el email de aviso</h3>

<p>Lo programaste ayer. Hoy se envía. Vigila tu bandeja de respuestas para resolver dudas rápidamente.</p>

<hr>

<h2 id="dia-7">Día 7 · Verificar todo y cancelar Thinkific</h2>

<h3 id="verificar-checklist">1. Checklist de verificación final</h3>

<ul>
    <li>☐ Mi dominio carga en el nuevo sistema (no en Thinkific).</li>
    <li>☐ Los alumnos pueden crear contraseña con el botón del email.</li>
    <li>☐ Los cursos están todos visibles para los alumnos correctos.</li>
    <li>☐ Las redirecciones 301 funcionan (prueba con varias URLs viejas).</li>
    <li>☐ El proceso de compra para nuevos alumnos funciona (haz una compra de prueba con un email distinto).</li>
    <li>☐ Tienes copia local de todo (vídeos, CSVs, emails).</li>
</ul>

<h3 id="cancela-thinkific">2. Cancelar Thinkific</h3>

<p>Solo cuando los 6 puntos anteriores están marcados:</p>

<ol>
    <li>Admin Thinkific → <strong>Settings</strong> → <strong>Site Settings</strong> → <strong>Cancel Account</strong>.</li>
    <li>Confirma. Tu acceso se mantiene hasta el fin del periodo de facturación pagado.</li>
    <li>Descarga las facturas finales para tu contabilidad.</li>
</ol>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Recomendación cobarde pero útil:</strong> mantén Thinkific abierto 30 días extra después de la migración exitosa. Si descubres que olvidaste algo, lo recuperas. Te cuesta 49-149$ extra pero es seguro de vida.</p>
</div>

<hr>

<h2 id="trampas-comunes">Las 4 trampas más comunes (y cómo evitarlas)</h2>

<h3 id="trampa-1">Trampa 1 · Hacer la migración en el peor momento</h3>

<p>Si tu academia tiene picos estacionales (ej: enero por propósitos de año nuevo, septiembre por vuelta al cole), <strong>NO migres en pico</strong>. Espera a un mes "valle" donde el daño sea mínimo si algo falla.</p>

<h3 id="trampa-2">Trampa 2 · Olvidar las integraciones</h3>

<p>Si Thinkific estaba conectado con <strong>Mailchimp, ConvertKit, ActiveCampaign, Zapier</strong>… cuando migres, esas integraciones se rompen. Lista TODAS antes de empezar y planifica recrearlas en el nuevo sistema.</p>

<h3 id="trampa-3">Trampa 3 · Confiar 100% en el CSV de alumnos</h3>

<p>El CSV exportado puede tener <strong>encoding raro</strong> (acentos, eñes mal codificadas). Antes de importar, ábrelo en un editor y verifica que "José" no se ha convertido en "JosÃ©". Si pasó, reabres con encoding UTF-8.</p>

<h3 id="trampa-4">Trampa 4 · No avisar con suficiente antelación</h3>

<p>Mandar UN email un día antes del cambio = alumnos confundidos y soporte saturado. Lo correcto: <strong>aviso 7-14 días antes</strong>, recordatorio 3 días antes, email de "ya estás dentro" el día del cambio.</p>

<hr>

<h2 id="cuanto-cuesta-real">¿Cuánto te ahorras al año?</h2>

<p>Ejemplo numérico (porque las palabras se las lleva el viento):</p>

<table>
    <thead>
        <tr>
            <th>Concepto</th>
            <th>Thinkific Pro</th>
            <th>Dominio propio (Cursalia o WP)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Plataforma</td><td>1.188€/año</td><td>0€</td></tr>
        <tr><td>Dominio</td><td>(incluido)</td><td>12€/año</td></tr>
        <tr><td>Hosting</td><td>(incluido)</td><td>50€/año</td></tr>
        <tr><td>Plugin LMS (si WP + LearnDash)</td><td>—</td><td>199$ ≈ 185€/año</td></tr>
        <tr><td>Email (free tier suficiente)</td><td>(incluido)</td><td>0€</td></tr>
        <tr><td><strong>Total anual</strong></td><td><strong>1.188€</strong></td><td><strong>62€ (Cursalia) o 247€ (WP+LD)</strong></td></tr>
        <tr><td><strong>Ahorro anual</strong></td><td>—</td><td><strong>~1.126€ o ~940€</strong></td></tr>
    </tbody>
</table>

<p>En 3 años: <strong>~3.380€ o ~2.820€</strong>. Esa es tu motivación.</p>

<h3 id="proximos-pasos">¿Por dónde sigo?</h3>

<div class="callout callout-tip">
    <i class="fa-solid fa-graduation-cap"></i>
    <p><strong>Te falta elegir LMS o ya lo tienes claro?</strong></p>
    <p>Si todavía no decidiste a qué te migras: te dejé una <a href="/blog/lms-open-source-espanol-2026-comparativa">comparativa honesta de 7 LMS open source en español</a>.</p>
</div>

<div class="callout callout-info">
    <i class="fa-solid fa-coins"></i>
    <p><strong>¿También usaste Hotmart o estás considerándolo?</strong></p>
    <p>Las cuentas de Hotmart son distintas y peores en algunos aspectos. <a href="/blog/hotmart-vs-tu-propia-plataforma-de-cursos">Lee la cuenta real aquí</a>.</p>
</div>

<div class="callout callout-tip">
    <i class="fa-solid fa-rocket"></i>
    <p><strong>Curso gratis · 14 lecciones · Monta tu academia paso a paso</strong></p>
    <p>Si decides usar Cursalia, te llevo de la mano desde la instalación hasta tener cursos online. <a href="/blog?category=curso-cursalia">Empezar el curso →</a></p>
</div>

<h3 id="resumen">📚 Resumen en 5 puntos</h3>

<ol>
    <li><strong>Migrar de Thinkific en 7 días es viable</strong> si tienes plan estructurado. Cancelarlo sin plan = pérdida de alumnos garantizada.</li>
    <li><strong>Día 1 = backup completo de TODO</strong> (cursos, alumnos, ventas, emails). Sin esto no hay migración limpia.</li>
    <li><strong>Días 2-5 = montar el nuevo sistema en paralelo</strong>, sin tocar Thinkific. Tu academia vieja sigue viva mientras pruebas la nueva.</li>
    <li><strong>Día 6 = redirecciones 301 y email de aviso</strong>. Sin las redirecciones pierdes SEO; sin el email pierdes alumnos confundidos.</li>
    <li><strong>Día 7 = verificar, cancelar y mantener Thinkific 30 días extra de seguro</strong>. El ahorro real: 940-1.126€/año. En 3 años, casi 3.000€.</li>
</ol>
HTML;
    }

    private function buildFaq(): array
    {
        return [
            [
                'q' => '¿Puedo migrar de Thinkific sin perder a mis alumnos actuales?',
                'a' => 'Sí, pero el proceso es manual. Exportas la lista desde Thinkific (Settings > Support Your Students > Users > Export), la importas en el nuevo sistema y envías un email pidiéndoles que creen contraseña nueva con un botón de activación. Si comunicas con 7-14 días de antelación, mantienes al 90% de alumnos.',
            ],
            [
                'q' => '¿Pierdo el SEO si cambio de Thinkific a otra plataforma?',
                'a' => 'Solo si NO tenías dominio propio. Si usabas un dominio propio apuntando a Thinkific (ej: cursos.tudominio.com), basta con configurar redirecciones 301 de las URLs viejas a las nuevas y mantienes el ranking. Si usabas el subdominio gratuito tu-academia.thinkific.com, ese SEO se pierde porque no controlas la redirección.',
            ],
            [
                'q' => '¿Qué hago con los pagos recurrentes (suscripciones, membresías) de Thinkific al migrar?',
                'a' => 'Esto requiere coordinación. Si tienes alumnos con suscripción activa, NO canceles Thinkific abruptamente. Avisa con 30-60 días: dales acceso al nuevo sistema en paralelo, reconfigura la suscripción en tu nueva pasarela (Stripe, PayPal) y solo cuando hayan migrado, cancela Thinkific. Si Thinkific era quien cobraba con su propia pasarela, los alumnos tendrán que volver a meter datos de pago.',
            ],
            [
                'q' => '¿Cuánto dura realmente la migración de Thinkific?',
                'a' => 'Para una academia con 5-10 cursos: 7 días si lo haces a tiempo completo, 2-3 semanas si lo combinas con tu trabajo diario. Lo que más tiempo lleva es subir vídeos al nuevo sistema (si tienes 100 horas de contenido, son varios días de subida solo por el ancho de banda).',
            ],
            [
                'q' => '¿Vale la pena migrar de Thinkific si solo tengo 1-2 cursos pequeños?',
                'a' => 'Depende de tu volumen. Si vendes menos de 5-10 cursos al año, Thinkific Basic (49$/mes = 588$/año) es difícil de amortizar pero te ahorra trabajo técnico. Si vendes más de 20 al año, el ahorro de migrar a una plataforma propia (~60€/año vs 1.188€/año del Pro) compensa con creces el tiempo de migración.',
            ],
            [
                'q' => '¿Qué pasa con los certificados que ya emití desde Thinkific?',
                'a' => 'Los certificados PDF que tus alumnos ya descargaron son válidos para siempre — son archivos en su poder. Lo que se pierde es la posibilidad de re-emitirlos desde Thinkific tras cancelar la cuenta. Recomendación: descarga TODOS los certificados generados antes de cancelar, y guárdalos en Drive o S3. Si tu nuevo LMS soporta certificados (Cursalia los tendrá en PRO; Moodle y Chamilo los tienen de fábrica), regeneras los nuevos desde ahí.',
            ],
        ];
    }

    private function buildHeroSvg(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" role="img" aria-label="Migrar de Thinkific a tu propio dominio en 7 días">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FFF5F0"/>
      <stop offset="1" stop-color="#F0FDF4"/>
    </linearGradient>
    <linearGradient id="coral" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FB7185"/>
      <stop offset="1" stop-color="#E11D48"/>
    </linearGradient>
    <linearGradient id="brand" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#10B981"/>
      <stop offset="1" stop-color="#047857"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <circle cx="180" cy="120" r="180" fill="#FB7185" opacity="0.08"/>
  <circle cx="1020" cy="510" r="220" fill="#10B981" opacity="0.10"/>

  <!-- Etiqueta -->
  <g transform="translate(420,55)">
    <rect width="360" height="38" rx="19" fill="#fff" stroke="#FB7185" stroke-width="2"/>
    <text x="180" y="25" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="13" font-weight="800" fill="#E11D48" letter-spacing="2">GUÍA PRÁCTICA · DÍA A DÍA</text>
  </g>

  <!-- Título -->
  <text x="600" y="155" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="44" font-weight="900" fill="#1F2933">Cómo migrar de Thinkific</text>
  <text x="600" y="205" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="38" font-weight="900" fill="#10B981">a tu propio dominio en 7 días</text>
  <text x="600" y="245" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="16" fill="#6B7280">…sin perder a tus alumnos ni tu SEO</text>

  <!-- Diagrama de migración -->
  <g transform="translate(150,310)">
    <!-- Thinkific (origen) -->
    <rect width="220" height="100" rx="20" fill="url(#coral)" opacity="0.95"/>
    <text x="110" y="40" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="24" font-weight="800" fill="#fff">Thinkific</text>
    <text x="110" y="65" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="13" fill="#fff" opacity="0.85">$49-199 / mes</text>
    <text x="110" y="83" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#fff" opacity="0.7">marca diluida · alquilado</text>
  </g>

  <!-- Flecha + 7 días -->
  <g transform="translate(390,330)">
    <path d="M0 30 L420 30" stroke="#1F2933" stroke-width="3" fill="none" stroke-dasharray="8 6"/>
    <polygon points="420,30 408,22 408,38" fill="#1F2933"/>
    <g transform="translate(140,0)">
      <rect width="140" height="60" rx="14" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="70" y="25" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="12" font-weight="700" fill="#6B7280">PLAN</text>
      <text x="70" y="47" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="22" font-weight="900" fill="#1F2933">7 días</text>
    </g>
  </g>

  <!-- Tu dominio (destino) -->
  <g transform="translate(830,310)">
    <rect width="220" height="100" rx="20" fill="url(#brand)" opacity="0.95"/>
    <text x="110" y="38" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#fff">Tu dominio</text>
    <text x="110" y="62" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="13" fill="#fff" opacity="0.85">~62€ / año</text>
    <text x="110" y="83" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#fff" opacity="0.7">tu marca · 100% tuyo</text>
  </g>

  <!-- Pasos clave -->
  <g transform="translate(200,460)" font-family="Inter,Arial,sans-serif">
    <g>
      <circle cx="20" cy="20" r="18" fill="#fff" stroke="#FB7185" stroke-width="2"/>
      <text x="20" y="26" text-anchor="middle" font-size="13" font-weight="800" fill="#1F2933">1</text>
      <text x="50" y="25" font-size="13" font-weight="600" fill="#1F2933">Backup completo</text>
    </g>
    <g transform="translate(220,0)">
      <circle cx="20" cy="20" r="18" fill="#fff" stroke="#FB7185" stroke-width="2"/>
      <text x="20" y="26" text-anchor="middle" font-size="13" font-weight="800" fill="#1F2933">3</text>
      <text x="50" y="25" font-size="13" font-weight="600" fill="#1F2933">Subir cursos</text>
    </g>
    <g transform="translate(420,0)">
      <circle cx="20" cy="20" r="18" fill="#fff" stroke="#10B981" stroke-width="2"/>
      <text x="20" y="26" text-anchor="middle" font-size="13" font-weight="800" fill="#1F2933">5</text>
      <text x="50" y="25" font-size="13" font-weight="600" fill="#1F2933">Avisar alumnos</text>
    </g>
    <g transform="translate(620,0)">
      <circle cx="20" cy="20" r="18" fill="#fff" stroke="#10B981" stroke-width="2"/>
      <text x="20" y="26" text-anchor="middle" font-size="13" font-weight="800" fill="#1F2933">7</text>
      <text x="50" y="25" font-size="13" font-weight="600" fill="#1F2933">Cancelar Thinkific</text>
    </g>
  </g>

  <!-- Marca -->
  <g transform="translate(540,560)" opacity="0.6">
    <rect x="0" y="0" width="120" height="32" rx="16" fill="#1F2933"/>
    <text x="60" y="22" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="14" font-weight="700" fill="#fff">cursalia.com</text>
  </g>
</svg>
SVG;
    }
}
