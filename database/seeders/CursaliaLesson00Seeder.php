<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Publica la Lección 0 del curso del blog y su categoría "Curso Cursalia".
 * Genera también el SVG hero del artículo.
 */
class CursaliaLesson00Seeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        // 1) Categoría "Curso Cursalia"
        $category = BlogCategory::updateOrCreate(
            ['slug' => 'curso-cursalia'],
            [
                'name'   => 'Curso Cursalia',
                'color'  => '#10B981',
                'status' => true,
            ]
        );

        // 2) Autor: el admin principal
        $admin = Admin::first();

        // 3) SVG hero
        $heroPath = 'blog/leccion-00-hero.svg';
        Storage::disk('public')->put($heroPath, $this->buildHeroSvg());

        // 4) Blog post
        Blog::updateOrCreate(
            ['slug' => 'lec-00-construye-tu-propia-academia-online'],
            [
                'admin_id'         => $admin?->id,
                'blog_category_id' => $category->id,
                'title'            => '¿Y si construyes tu propia academia online? Bienvenido al curso',
                'thumbnail'        => $heroPath,
                'summary'          => 'Lección 0 del curso gratis para crear tu propia plataforma de cursos con Laravel 13. Sin pagar Hotmart. Sin programar como un loco.',
                'content'          => $this->buildContent(),
                'status'           => 'published',
                'published_at'     => now(),
            ]
        );

        $this->command->info('  ✓ Categoría "Curso Cursalia" + Lección 0 publicada.');
        $this->command->info('  → http://cursalia.test/blog/lec-00-construye-tu-propia-academia-online');
    }

    private function buildHeroSvg(): string
    {
        return <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#10B981"/>
      <stop offset="55%" stop-color="#059669"/>
      <stop offset="100%" stop-color="#047857"/>
    </linearGradient>
    <radialGradient id="r1" cx="0.85" cy="0.15" r="0.8">
      <stop offset="0%" stop-color="#FBBF24" stop-opacity="0.42"/>
      <stop offset="100%" stop-color="#FBBF24" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="r2" cx="0.1" cy="0.85" r="0.7">
      <stop offset="0%" stop-color="#FB7185" stop-opacity="0.32"/>
      <stop offset="100%" stop-color="#FB7185" stop-opacity="0"/>
    </radialGradient>
    <style>
      .title { font-family: 'Poppins', 'Inter', sans-serif; font-weight: 800; fill: #fff; }
      .sub   { font-family: 'Inter', sans-serif; font-weight: 500; fill: rgba(255,255,255,0.88); }
      .badge { font-family: 'Inter', sans-serif; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; fill: rgba(255,255,255,0.92); }
      .deco  { font-family: 'Poppins', sans-serif; font-weight: 800; fill: rgba(255,255,255,0.06); }
      .pack  { font-family: 'Poppins', 'Inter', sans-serif; fill: #fff; }
    </style>
  </defs>

  <rect width="1200" height="630" fill="url(#g)"/>
  <rect width="1200" height="630" fill="url(#r1)"/>
  <rect width="1200" height="630" fill="url(#r2)"/>

  <!-- L0 gigante decorativo MUCHO MÁS abajo y a la izquierda (sin colisión) -->
  <text x="-40" y="780" class="deco" font-size="640">L0</text>

  <!-- Badge -->
  <g transform="translate(75,80)">
    <rect width="280" height="38" rx="19" fill="rgba(255,255,255,0.18)"/>
    <text x="140" y="25" text-anchor="middle" class="badge" font-size="12">Curso Cursalia · Lección 0</text>
  </g>

  <!-- Título (3 líneas más estrechas, no se solapan con el pack) -->
  <text x="75" y="230" class="title" font-size="62">¿Y si construyes</text>
  <text x="75" y="300" class="title" font-size="62">tu propia</text>
  <text x="75" y="370" class="title" font-size="62">academia online?</text>

  <!-- Subtítulo -->
  <text x="75" y="450" class="sub" font-size="24">Bienvenido al curso. 14 lecciones gratis</text>
  <text x="75" y="482" class="sub" font-size="24">para crear tu propia plataforma con Laravel 13.</text>

  <!-- Pack de inicio · esquina inferior derecha, sin solaparse -->
  <g transform="translate(925,335) rotate(6)">
    <!-- Sombra suave -->
    <rect x="4" y="6" width="200" height="240" rx="22" fill="rgba(0,0,0,0.18)"/>
    <!-- Caja -->
    <rect width="200" height="240" rx="22" fill="rgba(255,255,255,0.16)" stroke="rgba(255,255,255,0.32)" stroke-width="2"/>
    <!-- Cinta superior (regalo) -->
    <rect x="0" y="60" width="200" height="14" fill="#FBBF24" opacity="0.85"/>
    <rect x="93" y="0" width="14" height="240" fill="#FBBF24" opacity="0.85"/>
    <!-- Moño -->
    <circle cx="100" cy="68" r="22" fill="#FBBF24"/>
    <text x="100" y="79" text-anchor="middle" class="pack" font-size="26">🎁</text>
    <!-- Texto -->
    <text x="100" y="138" text-anchor="middle" class="pack" font-size="18" font-weight="800">Pack de inicio</text>
    <text x="100" y="162" text-anchor="middle" class="pack" font-size="12" font-weight="500" opacity="0.85">PDF + ZIP + Discord</text>
    <!-- Botón -->
    <rect x="30" y="186" width="140" height="36" rx="18" fill="#0B0B1A"/>
    <text x="100" y="209" text-anchor="middle" class="pack" font-size="13" font-weight="700">Descargar gratis</text>
  </g>
</svg>
SVG;
    }

    private function buildContent(): string
    {
        return <<<'HTML'
<h2 id="vistazo-rapido-al-codigo">Un vistazo rapido al codigo (no te asustes)</h2>

<p>Antes de empezar te muestro como se ve por dentro Cursalia. <strong>No tienes que entenderlo</strong>, solo que veas que es <em>codigo limpio y legible</em>:</p>

<pre><code class="language-php">// app/Http/Controllers/Frontend/CoursePageController.php
public function home()
{
    $hero       = HeroSection::query()->first();
    $categories = CourseCategory::query()
        ->whereNull('parent_id')
        ->withCount('allCourses')
        ->take(10)
        ->get();

    return view('frontend.home.index', compact('hero', 'categories'));
}</code></pre>

<p>Y para subir tu academia al servidor solo correras estos comandos (te los explico paso a paso en la leccion 12):</p>

<pre><code class="language-bash">composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache</code></pre>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Si la sintaxis te parece chino</strong>: tranquilo. En las lecciones 1 a 13 no tendras que <em>escribir</em> codigo. Solo lo veras y copiaras cuando haga falta. La idea es que Cursalia te de el trabajo hecho.</p>
</div>

<h2 id="lo-que-tenemos-que-hablar">Lo que tenemos que hablar antes de empezar</h2>

<p>Si llegaste aquí es porque te ronda una idea: <strong>vender tus conocimientos online</strong>.</p>

<p>A lo mejor llevas años trabajando en algo (cocina, idiomas, marketing, programación, fitness, lo que sea) y te has dado cuenta de que <strong>lo que tú sabes, otros lo necesitan</strong>. Y están dispuestos a pagar por aprenderlo.</p>

<p>Así que abriste Google y empezaste a investigar. Encontraste lo de siempre:</p>

<ul>
    <li><strong>Hotmart</strong>: te cobra ~10% de cada venta + comisión de pasarela. Y tu academia se ve como cientos de otras.</li>
    <li><strong>Thinkific / Teachable</strong>: $99/mes mínimo si quieres algo decente. Al año son <strong>$1,188</strong> por <em>alquilar</em> tu sitio.</li>
    <li><strong>Kajabi</strong>: ~$149/mes. Lo mismo, pero más caro y con marca de "guru americano".</li>
    <li><strong>WordPress + LearnDash</strong>: $200/año el plugin + hosting + temas + horas configurando.</li>
</ul>

<p>Y en todos esos casos hay dos cosas en común:</p>

<ol>
    <li><strong>Tu academia NO es tuya.</strong> Pagas por usarla. El día que cierran (o suben el precio), te quedas en la calle.</li>
    <li><strong>Te ves igual que todos.</strong> El cliente que te paga lo sabe. Y le importa.</li>
</ol>

<div class="callout callout-quote">
    <i class="fa-solid fa-quote-left"></i>
    <p><strong>¿Y si te dijera que se puede hacer otra cosa?</strong></p>
    <p>Que se puede tener tu propia plataforma, con tu marca, tu dominio, tu base de alumnos, <strong>sin pagar mensualidades</strong>, sin depender de nadie. Que <strong>se puede empezar gratis</strong> y, cuando arranque, escalarla sin que un señor en California te cobre comisión.</p>
</div>

<p>Eso es lo que te voy a enseñar en este curso.</p>

<h2 id="que-vamos-a-construir">Qué vamos a construir juntos</h2>

<p>Vamos a montar <strong>tu academia online completa</strong>. Algo con:</p>

<div class="learn-box">
    <p><i class="fa-solid fa-bullseye"></i> Lo que tendrás al final</p>
    <ul>
        <li>Tu marca aplicada (logo, colores, tipografía)</li>
        <li>Catálogo de cursos con buscador, filtros y categorías</li>
        <li>Registro de alumnos que se inscriben gratis o pagan</li>
        <li>Cobro automático con Stripe y PayPal (en la Fase PRO)</li>
        <li>Panel de administración donde tú controlas todo</li>
        <li>Blog integrado para atraer tráfico vía Google</li>
        <li>Tu propio dominio (tuacademia.com)</li>
    </ul>
</div>

<p>Y lo mejor de todo: <strong>NO vas a pagar mensualidades nunca</strong>. Solo el hosting (~$50–$100 al año, no al mes) y el dominio (~$15 al año).</p>

<h2 id="la-trampa-que-nadie-cuenta">La trampa que casi nadie te cuenta</h2>

<p>Aquí viene la parte que la mayoría de los "gurúes" de internet no te dice:</p>

<div class="callout callout-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <p><strong>Construir una academia online NO es lo difícil.</strong></p>
    <p>Lo difícil es que la gente la encuentre y compre.</p>
</div>

<p>La buena noticia: este curso cubre las dos cosas.</p>

<ul>
    <li><strong>La primera mitad</strong> (lecciones 1 a 13, todas gratis): construyes técnicamente tu academia. Sin programar como un loco. Vas a estar funcionando en menos de 4 semanas.</li>
    <li><strong>La segunda mitad</strong> (lecciones 14 a 25, premium): aprendes a <strong>venderla</strong>. Pagos, cupones, marketplace, marketing y embudos.</li>
</ul>

<p>Pero no nos adelantemos. Empecemos por la base.</p>

<h2 id="quien-soy-yo">¿Quién soy yo y por qué deberías escucharme?</h2>

<p>Llevo años construyendo academias online para mí y para clientes. He visto:</p>

<ul>
    <li>A gente que pagó $5,000 por una plataforma WordPress que no usaba el 80% de las funciones.</li>
    <li>A profesionales talentosos atrapados en Hotmart porque "no sabían programar".</li>
    <li>A profesores jubilados que se rendían porque "esto no es para mí".</li>
</ul>

<p><strong>Y eso me frustra.</strong></p>

<p>Porque la verdad es que crear tu propia academia hoy es más fácil que nunca. Solo que <strong>nadie te lo explica con paciencia y en español</strong>.</p>

<p>Por eso construí <strong>Cursalia</strong>: un sistema de academia online <strong>gratis, abierto y editable</strong> desde un panel sencillo. Sin programar.</p>

<p>Y por eso escribo este curso. Para que en 14 semanas tengas algo tuyo, vivo, online.</p>

<h2 id="que-es-cursalia">¿Qué es Cursalia y por qué te la regalo?</h2>

<p>Cursalia es un <strong>LMS</strong> (Learning Management System, por sus siglas en inglés) que cualquiera puede descargar gratis y poner en su servidor.</p>

<p>Cursalia hace varias cosas que ningún sistema gratuito hace:</p>

<ol>
    <li><strong>Es editable desde un panel admin claro.</strong> Cambias colores, logo, textos, menús con un par de clicks. No necesitas tocar código.</li>
    <li><strong>Es responsive y SEO-friendly.</strong> Funciona en móvil y Google la indexa bien.</li>
    <li><strong>Está en español de verdad</strong> (no traducido automático).</li>
    <li><strong>Tiene blog integrado.</strong> Como este artículo: vive dentro de la propia plataforma.</li>
    <li><strong>Es libre.</strong> No paga "Powered by Cursalia" obligatorio. Es tuya.</li>
</ol>

<h3 id="por-que-la-regalo">¿Por qué la regalo? La pregunta justa.</h3>

<p>Aquí viene la honestidad: yo <strong>NO regalo todo</strong>. Cursalia FREE es gratis, sí. Pero existe <strong>Cursalia PRO</strong> (que tiene cobros automáticos, marketplace multi-instructor, certificados PDF y más) que es de pago.</p>

<p>Mi apuesta es simple:</p>

<ul>
    <li>Si te regalo la versión gratis y te ayuda a montar tu academia, <strong>cuando crezcas vas a querer PRO</strong>. Y me comprarás a mí, no a Thinkific.</li>
    <li>Y aunque nunca compres PRO, ya me hiciste un favor: usaste mi sistema, lo recomendaste, hablaste de él. <strong>Y mi reputación creció</strong>.</li>
</ul>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p>Es un modelo viejo y conocido: <strong>lo que regalas vale más que lo que cobras</strong>. Lo hace Linux, lo hace WordPress, lo hace cualquier empresa seria. Y a mí, simplemente, me gusta porque es honesto.</p>
</div>

<h2 id="lo-que-vas-a-tener">Lo que vas a tener al final de este curso</h2>

<p>Después de las 14 lecciones gratuitas (que iré publicando una por semana), tú tendrás:</p>

<ul>
    <li>Tu propia academia online, funcionando en tu dominio.</li>
    <li>Con tu marca aplicada: logo, colores, textos.</li>
    <li>Con tus primeros cursos cargados (mínimo 3).</li>
    <li>Con blog activo para atraer tráfico orgánico.</li>
    <li>Con panel admin que dominas.</li>
    <li>Lista para recibir alumnos.</li>
</ul>

<p>Lo único que NO tendrás aún: <strong>el sistema de pagos para cobrar automáticamente</strong>. Eso es Fase 2 (PRO).</p>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>En Fase 1 SÍ puedes empezar a generar ingresos:</strong></p>
    <p>Ofrecer cursos gratis para construir lista de alumnos, o cobrar manualmente (PayPal, Bizum, transferencia) y matricular tú al alumno desde el admin. Te lo cuento en la última lección.</p>
</div>

<h2 id="cuanto-tiempo">¿Cuánto tiempo necesitas?</h2>

<p>Honestidad otra vez:</p>

<ul>
    <li><strong>Si le dedicas 3 horas por semana</strong>: en 4 meses tienes tu academia lista.</li>
    <li><strong>Si le dedicas 1 hora al día</strong>: en 6 semanas la tienes funcionando.</li>
    <li><strong>Si te obsesionas y le metes 4 horas al día</strong>: en 2 semanas estás vendiendo cursos.</li>
</ul>

<p>No hay magia. Lo que hay es <strong>constancia</strong>. Y este curso está diseñado para que avances con esa constancia, sin abrumarte.</p>

<p>Cada lección que publico es <strong>autocontenida</strong>. Si en una semana no puedes leerla, la siguiente sigue ahí. Tu ritmo lo pones tú.</p>

<h2 id="que-necesitas">Lo que necesitas antes de empezar la Lección 1</h2>

<p>Para empezar a construir, vas a necesitar:</p>

<ol>
    <li>Una computadora con Windows, Mac o Linux. Cualquiera sirve.</li>
    <li>Una conexión a internet (decente, no necesitas fibra de campeonato).</li>
    <li>2–3 horas para la próxima lección.</li>
    <li><strong>Cero conocimientos de programación.</strong> Lo digo en serio. Cero.</li>
</ol>

<p>Eso es todo. Si tienes esas cuatro cosas, tienes lo que hace falta.</p>

<h2 id="la-pregunta">La pregunta importante</h2>

<p>Antes de pasar a la Lección 1, déjame preguntarte algo que solo tú puedes responder:</p>

<blockquote>
    <p><strong>¿Estás dispuesto a dedicarle 2 horas a la semana, durante 4 meses, a algo que puede cambiar tu vida los próximos 10 años?</strong></p>
</blockquote>

<p>Si la respuesta es sí, esto va a funcionar.</p>

<p>Si la respuesta es "depende de si veo resultados rápido", déjame ser honesto: <strong>este curso no es para ti</strong>. Las cosas que se hacen rápido se rompen rápido. Las que se hacen con paciencia, duran décadas.</p>

<p>Esta es la oportunidad de construir algo <strong>tuyo</strong>. Sin jefe, sin Hotmart cobrándote comisión, sin depender de un algoritmo de Instagram. <strong>Tu academia, tu marca, tu dinero</strong>.</p>

<h2 id="vamos">¿Vamos?</h2>

<p>Si me dices que sí, nos vemos en la <strong>Lección 1</strong>:</p>

<div class="callout callout-tip">
    <i class="fa-solid fa-arrow-right"></i>
    <p><strong>Próxima Lección 1 — Las herramientas que necesitas (e instálalas en 30 minutos)</strong></p>
    <p>Laragon, Composer, Node.js, Git y VS Code. Captura por captura. Se publica el próximo viernes a las 9:00.</p>
</div>

<p>Si me dices que no, no pasa nada. Pero sospecho que vas a volver.</p>

<hr>

<h3 id="resumen">📚 Resumen en 5 puntos</h3>

<ol>
    <li><strong>Las plataformas como Hotmart o Thinkific te cobran cada mes para alquilar tu propio negocio.</strong> Es injusto.</li>
    <li><strong>Construir tu propia academia hoy es más fácil que nunca.</strong> Solo nadie te lo explica con paciencia y en español.</li>
    <li><strong>Cursalia es un sistema gratis y editable</strong> desde panel admin que puedes descargar y poner en tu dominio.</li>
    <li><strong>Este curso tiene 2 fases</strong>: Fase 1 (14 lecciones gratis, construyes la academia) y Fase 2 (12 lecciones premium, aprendes a vender).</li>
    <li><strong>No necesitas saber programar</strong>. Necesitas paciencia, constancia y unas pocas horas a la semana.</li>
</ol>
HTML;
    }
}
