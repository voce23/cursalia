<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Artículo satélite #1 — Hotmart vs tu propia plataforma.
 *
 * Objetivo SEO: capturar las búsquedas de gente que considera Hotmart
 * o que ya está dentro y busca alternativa. Keyword intent comercial alto:
 *   - "alternativa a Hotmart"
 *   - "Hotmart comisión"
 *   - "Hotmart vs Thinkific"
 *   - "tener mi propia plataforma de cursos"
 *
 * Estrategia: artículo hub (~2.000 palabras) que enlaza al Curso Cursalia.
 * Categoría: Comparativas (nueva, color coral para diferenciarla del curso verde).
 */
class CursaliaHotmartArticleSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        // 1) Categoría "Comparativas" (si no existe)
        $category = BlogCategory::updateOrCreate(
            ['slug' => 'comparativas'],
            [
                'name'   => 'Comparativas',
                'color'  => '#FB7185', // coral Cursalia
                'status' => true,
            ]
        );

        // 2) Autor
        $admin = Admin::first();

        // 3) SVG hero
        $heroPath = 'blog/hotmart-vs-propia-plataforma-hero.svg';
        Storage::disk('public')->put($heroPath, $this->buildHeroSvg());

        // 4) Artículo
        Blog::updateOrCreate(
            ['slug' => 'hotmart-vs-tu-propia-plataforma-de-cursos'],
            [
                'admin_id'         => $admin?->id,
                'blog_category_id' => $category->id,
                'title'            => 'Hotmart vs tu propia plataforma de cursos: la cuenta real que nadie te muestra',
                'thumbnail'        => $heroPath,
                'summary'          => 'Cuánto te cuesta REALMENTE Hotmart al año frente a tener tu propia plataforma. Comisiones, mensualidades, control, marca. Análisis honesto con números, sin humo.',
                'content'          => $this->buildContent(),
                'meta_title'       => 'Hotmart vs tu propia plataforma de cursos · cuenta real 2026',
                'meta_description' => 'Hotmart cobra 9,9% + IVA de cada venta. Tu propia plataforma cuesta ~$100/año total. La cuenta real con números, ventajas y trampas de cada modelo.',
                'faq'              => $this->buildFaq(),
                'status'           => 'published',
                'published_at'     => now(),
            ]
        );

        $this->command->info('  ✓ Artículo satélite "Hotmart vs tu propia plataforma" publicado.');
    }

    private function buildContent(): string
    {
        return <<<'HTML'
<p>Si estás leyendo esto probablemente te ronda la idea: <strong>tengo conocimiento que venden, ¿cómo lo monetizo online?</strong> Y la respuesta automática que da el 95% de gente que te rodea es <em>"súbelo a Hotmart"</em>.</p>

<p>Yo no te voy a decir que Hotmart sea malo. Es una herramienta válida, ha lanzado carreras y para muchos creadores fue su primer ingreso recurrente. Pero hay una cuenta que casi nadie hace antes de subirse al barco. Vamos a hacerla juntos, con números reales.</p>

<div class="learn-box">
    <p><i class="fa-solid fa-bullseye"></i> Lo que vas a aprender</p>
    <ul>
        <li>Cuánto cobra Hotmart de verdad (no solo la comisión visible)</li>
        <li>Cuánto cuesta de verdad tener tu propia plataforma</li>
        <li>El punto de equilibrio: a partir de qué facturación te conviene una u otra</li>
        <li>Tres trampas que nadie te cuenta sobre Hotmart</li>
        <li>Cómo es la transición si ya estás dentro y quieres salir</li>
    </ul>
</div>

<h2 id="cuanto-cobra-hotmart">¿Cuánto cobra Hotmart de verdad?</h2>

<p>La cifra que ves en su web es <strong>9,9% + 1$ por venta</strong>. Eso es lo que cobran en su plan más bajo (Hotmart Sparkle). Suena razonable hasta que multiplicas.</p>

<p>Pero el coste real es más alto. Vamos por capas:</p>

<h3 id="comisiones-reales">Las comisiones reales</h3>

<table>
    <thead>
        <tr>
            <th>Concepto</th>
            <th>% sobre venta</th>
            <th>En una venta de 100€</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Comisión Hotmart Sparkle</td><td>9,9%</td><td>9,90€</td></tr>
        <tr><td>Tarifa fija por transacción</td><td>~1€</td><td>1,00€</td></tr>
        <tr><td>IVA sobre la comisión (España)</td><td>21% de 9,9%</td><td>2,29€</td></tr>
        <tr><td>Pasarela de pago internacional (si pagan en otra moneda)</td><td>~2%</td><td>2,00€</td></tr>
        <tr><td><strong>Total Hotmart por venta de 100€</strong></td><td><strong>~15,2%</strong></td><td><strong>15,19€</strong></td></tr>
    </tbody>
</table>

<div class="callout callout-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <p><strong>Ojo con el cambio de divisa.</strong> Si vendes en EUR y Hotmart paga en BRL o USD, hay una conversión por debajo del cambio interbancario. En 2024 muchos creadores españoles reportaron pérdidas del 3-5% adicionales por este concepto.</p>
</div>

<h3 id="extras">Y los extras que no aparecen al principio</h3>

<ul>
    <li><strong>Hotmart Sparkle</strong> (gratis) tiene checkout básico, sin embudo, sin pixel personalizado, sin order bumps.</li>
    <li><strong>Hotmart Pro</strong> empieza a <strong>~9€/mes</strong> para tener afiliados, pixel y embudos.</li>
    <li><strong>Hotmart Sparkle</strong> NO permite poner tu pixel de Facebook. Esto es crítico: significa que no puedes hacer retargeting de tu propio tráfico. Estás "ciego".</li>
    <li>Si quieres tu propio dominio (no <code>tu-curso.hotmart.com</code>), necesitas plan superior.</li>
</ul>

<h2 id="cuanto-cuesta-propia">¿Cuánto cuesta tener tu propia plataforma?</h2>

<p>Aquí viene la parte sorprendente. Vamos a calcular el escenario realista de alguien que se monta su academia propia con un LMS open source como <a href="/blog?category=curso-cursalia">Cursalia</a> (la mía, soy transparente), WordPress + LearnDash, o cualquier otro stack autohospedado.</p>

<table>
    <thead>
        <tr>
            <th>Concepto</th>
            <th>Coste anual</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Dominio propio (.com)</td><td>~12€</td><td>Una vez al año en Namecheap/Cloudflare</td></tr>
        <tr><td>Hosting compartido decente</td><td>~50€</td><td>Hostinger, SiteGround, Webempresa nivel básico</td></tr>
        <tr><td>Stripe / PayPal (procesamiento)</td><td>1,4% + 0,25€ por venta</td><td>Sin IVA sobre la comisión en España</td></tr>
        <tr><td>Email transaccional (Mailgun, Resend)</td><td>0€ hasta 3.000/mes</td><td>Free tier suficiente para empezar</td></tr>
        <tr><td><strong>Total fijo anual</strong></td><td><strong>~62€</strong></td><td>+ 1,4% por venta (vs 15% Hotmart)</td></tr>
    </tbody>
</table>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Comparativa directa en una venta de 100€:</strong></p>
    <p>Hotmart: te quedas con <strong>~85€</strong>.<br>Plataforma propia con Stripe: te quedas con <strong>~98€</strong>.<br>Diferencia: <strong>13€ por venta</strong>. En 100 ventas al año, son 1.300€ que te ahorras. En 500 ventas, 6.500€.</p>
</div>

<h2 id="punto-equilibrio">El punto de equilibrio: ¿a partir de cuándo te conviene?</h2>

<p>La cuenta es honesta: <strong>si vas a vender menos de 10 cursos al año, Hotmart probablemente te compense</strong> por la simpleza. Si vas a vender más, la matemática se vuelve brutal en tu contra.</p>

<p>Hagamos los escenarios:</p>

<h3 id="escenario-1">Escenario 1: vendes 10 cursos al año a 100€</h3>

<ul>
    <li>Ingreso bruto: 1.000€</li>
    <li>Con Hotmart te quedas con ~850€</li>
    <li>Con plataforma propia: ~980€ (1.000€ - 14€ comisiones - 6€ hosting prorrateado)</li>
    <li><strong>Ahorro anual: 130€</strong> · No vale la pena el esfuerzo técnico.</li>
</ul>

<h3 id="escenario-2">Escenario 2: vendes 100 cursos al año a 100€</h3>

<ul>
    <li>Ingreso bruto: 10.000€</li>
    <li>Con Hotmart te quedas con ~8.500€</li>
    <li>Con plataforma propia: ~9.800€</li>
    <li><strong>Ahorro anual: 1.300€</strong> · Empieza a justificar el setup.</li>
</ul>

<h3 id="escenario-3">Escenario 3: vendes 500 cursos al año a 100€ (creador establecido)</h3>

<ul>
    <li>Ingreso bruto: 50.000€</li>
    <li>Con Hotmart te quedas con ~42.500€</li>
    <li>Con plataforma propia: ~49.200€</li>
    <li><strong>Ahorro anual: 6.700€</strong> · Aquí Hotmart te está robando un sueldo mínimo entero al año.</li>
</ul>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Punto crítico:</strong> en torno a los 50 cursos vendidos al año el coste técnico de tu plataforma propia ya se paga sola, y a partir de ahí cada nueva venta es ganancia limpia.</p>
</div>

<h2 id="trampas">Tres trampas de Hotmart que nadie te cuenta</h2>

<h3 id="trampa-1">Trampa 1 · Tu lista de alumnos NO es tuya</h3>

<p>Cuando alguien se inscribe en tu curso por Hotmart, los datos del comprador (email, nombre, teléfono) son <strong>compartidos contigo</strong>, pero el "owner" del dato a efectos legales y comerciales es Hotmart. Esto significa:</p>

<ul>
    <li>Hotmart puede usar esa lista para promocionar otros cursos de su catálogo. Tu alumno comprará a la competencia <em>antes</em> que a ti.</li>
    <li>Si decides irte de la plataforma, exportar la lista es engorroso y, en algunos países, legalmente discutible.</li>
    <li>Hotmart envía emails a tus alumnos firmados por <em>Hotmart</em>, no por ti. Tu marca se diluye.</li>
</ul>

<h3 id="trampa-2">Trampa 2 · Las "afiliaciones" parecen gratis y no lo son</h3>

<p>Hotmart te vende como ventaja que cualquier afiliado puede vender tu curso. Suena bien hasta que entiendes la cuenta:</p>

<ul>
    <li>Tú pones la comisión al afiliado (digamos 50%).</li>
    <li>Hotmart sigue cobrando su 9,9% sobre el bruto.</li>
    <li>Resultado: <strong>de 100€ vendidos, tú recibes ~40€</strong>. El afiliado se lleva 50, Hotmart 10.</li>
</ul>

<p>Es decir, escalas en volumen pero pierdes en margen. Si tu curso vale más por su contenido que por el volumen, las afiliaciones masivas son tu peor amigo.</p>

<h3 id="trampa-3">Trampa 3 · Tu academia se ve como cientos de otras</h3>

<p>El checkout de Hotmart, el panel del alumno, el logo de "Powered by Hotmart"… todo grita "esto es un curso más". El cliente que paga 200€ por tu formación se pregunta inconscientemente: <em>"¿es tan bueno como dice si lo aloja la misma plataforma que el curso de 9€ de mi cuñado?"</em></p>

<div class="callout callout-quote">
    <i class="fa-solid fa-quote-left"></i>
    <p><strong>La marca se construye en los detalles</strong> que el cliente ve cada día. Tu propio dominio, tus propios colores, tu propio tono de voz. No alquilas eso. Lo construyes.</p>
</div>

<h2 id="cuando-quedarse">¿Cuándo SÍ vale Hotmart?</h2>

<p>Honestidad ante todo: hay casos donde Hotmart es la decisión correcta.</p>

<ul>
    <li><strong>Estás validando.</strong> Aún no sabes si tu curso vende. Hotmart te da una infraestructura plug-and-play para hacer la primera venta en 48h.</li>
    <li><strong>Vendes esporádicamente.</strong> Menos de 1 curso al mes. La gestión técnica no la quieres ni gratis.</li>
    <li><strong>Vives de las afiliaciones de OTROS.</strong> Si tu modelo es promocionar cursos ajenos, Hotmart es el mercado para encontrarlos.</li>
    <li><strong>Tu nicho está concentrado en Brasil/LATAM y depende del prestigio de la plataforma.</strong> En esos mercados Hotmart todavía es marca de garantía.</li>
</ul>

<h2 id="como-migrar">Cómo es la transición si ya estás en Hotmart y quieres salir</h2>

<p>Si llevas tiempo en Hotmart y vienes pensando en migrar, esto es lo que te conviene saber antes:</p>

<ol>
    <li><strong>Exporta tu lista de alumnos</strong> (panel Hotmart → "Mis alumnos" → CSV) antes de cancelar nada. Es tu activo más valioso.</li>
    <li><strong>Monta tu plataforma propia EN PARALELO</strong>, sin tocar Hotmart todavía. Necesitas al menos 1 mes de pruebas con tu nuevo entorno.</li>
    <li><strong>Anuncia la migración a tus alumnos con 30 días de antelación.</strong> Dales acceso al nuevo entorno sin coste, para que vean que no es un downgrade.</li>
    <li><strong>Mantén Hotmart abierto los primeros 60 días.</strong> Tu tráfico de afiliados puede tardar en redirigir.</li>
    <li><strong>Cuando todo esté estable, cierra Hotmart formalmente.</strong> No te olvides de descargar el historial fiscal de los últimos 5 años antes.</li>
</ol>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Si no sabes por dónde empezar a montar tu plataforma:</strong> en este blog tengo un <a href="/blog?category=curso-cursalia">curso gratis de 14 lecciones</a> donde te llevo de la mano. Las 13 primeras no requieren saber programar.</p>
</div>

<hr>

<h2 id="conclusion">Conclusión: la cuenta de servilleta</h2>

<p>Hotmart es práctico al principio. Y por eso vale la pena al principio. Pero a partir del primer año de ventas regulares, te está costando entre 1.000€ y 7.000€ al año que podrías estar ahorrando.</p>

<p>Más importante todavía: te está costando <strong>tu marca</strong>, <strong>tu lista</strong> y <strong>tu independencia</strong>. Y esas tres cosas no salen en la factura. Pero las pierdes igual.</p>

<p>La buena noticia: montar tu propia plataforma en 2026 es más fácil que nunca. Hace 10 años necesitabas un equipo técnico. Hoy hay herramientas open source en español que te lo dan hecho. Solo tienes que aprender a configurarlas.</p>

<h3 id="proximos-pasos">¿Por dónde sigo?</h3>

<p>Si esto te ha resonado, te dejo dos caminos:</p>

<div class="callout callout-tip">
    <i class="fa-solid fa-graduation-cap"></i>
    <p><strong>Curso gratis · 14 lecciones · Construye tu academia online sin Hotmart</strong></p>
    <p>De cero a producción en menos de 4 semanas, sin programar. <a href="/blog?category=curso-cursalia">Empezar el curso →</a></p>
</div>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Lección 0 — El porqué de todo esto</strong></p>
    <p>Si lo que más te resonó es el ángulo "tu academia, tu marca", esta lección es la introducción perfecta. <a href="/blog/lec-00-construye-tu-propia-academia-online">Leer Lección 0 →</a></p>
</div>

<h3 id="resumen">📚 Resumen en 5 puntos</h3>

<ol>
    <li><strong>Hotmart cobra ~15% real</strong> por venta (no 9,9% como anuncia), una vez sumas comisiones, IVA y conversión de divisa.</li>
    <li><strong>Tu propia plataforma cuesta ~62€/año fijos</strong> + 1,4% por venta. A partir de 50 ventas anuales ya te conviene.</li>
    <li><strong>Tu lista de alumnos en Hotmart no es totalmente tuya.</strong> Pierdes control de retargeting, branding y comunicación directa.</li>
    <li><strong>Las afiliaciones de Hotmart suenan a regalo y son cara.</strong> Sumadas a la comisión, te dejan con ~40% del precio bruto.</li>
    <li><strong>Migrar es factible</strong> si lo haces gradual: monta tu plataforma propia, exporta tu lista, comunica con 30 días, cierra Hotmart al cabo de 60.</li>
</ol>
HTML;
    }

    private function buildFaq(): array
    {
        return [
            [
                'q' => '¿Cuánto cobra Hotmart por cada venta exactamente?',
                'a' => 'La comisión oficial es 9,9% + 1€ por transacción en el plan Sparkle (gratis). Pero al sumar el IVA sobre la comisión, la pasarela internacional y la conversión de divisa cuando vendes en EUR, el coste real ronda el 15% sobre cada venta.',
            ],
            [
                'q' => '¿Es legal tener mi propia plataforma de cursos en España / LATAM?',
                'a' => 'Sí, es totalmente legal. Solo necesitas estar dado de alta como autónomo o tener una sociedad para facturar las ventas. La plataforma técnica (el LMS) no requiere ninguna licencia especial.',
            ],
            [
                'q' => '¿Puedo migrar mis alumnos actuales de Hotmart a mi propia plataforma?',
                'a' => 'Sí, pero gradualmente. Lo recomendable es: 1) exportar tu lista de alumnos desde Hotmart, 2) montar tu plataforma propia en paralelo, 3) dar acceso gratis a tus alumnos actuales al nuevo entorno durante 30-60 días, 4) cerrar Hotmart cuando el flujo sea estable.',
            ],
            [
                'q' => '¿Necesito saber programar para tener mi propia plataforma?',
                'a' => 'No. Sistemas LMS modernos open source como Cursalia están diseñados para que el dueño del sitio no escriba código. Cambias colores, logo, precios y cursos desde un panel admin. Solo necesitas seguir un tutorial paso a paso para la instalación inicial.',
            ],
            [
                'q' => '¿Cuál es la mejor alternativa a Hotmart en español?',
                'a' => 'Depende de tu presupuesto y nivel técnico: si vendes mucho y no quieres lío técnico, Thinkific o Teachable (99-149$/mes). Si quieres pagar menos y tener control total, un LMS open source autohospedado como Cursalia (~60€/año total). Si prefieres mantenerte en marketplace, Kajabi y EduZZ son las alternativas más comparables a Hotmart.',
            ],
            [
                'q' => '¿Qué pasa con mis cursos si decido salir de Hotmart?',
                'a' => 'Tus contenidos (vídeos, PDF, materiales) son tuyos: los puedes descargar y subir a tu nueva plataforma. Lo que NO te llevas es la URL pública del curso (queda rota) ni el historial de ventas dentro de Hotmart. Por eso es importante exportar la lista de alumnos antes de cancelar.',
            ],
        ];
    }

    private function buildHeroSvg(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" role="img" aria-label="Hotmart vs tu propia plataforma">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FFF5F0"/>
      <stop offset="1" stop-color="#FEE4DA"/>
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
  <!-- Halo decorativo -->
  <circle cx="200" cy="120" r="180" fill="#FB7185" opacity="0.08"/>
  <circle cx="1000" cy="510" r="220" fill="#10B981" opacity="0.10"/>
  <!-- Caja izquierda: Hotmart -->
  <g transform="translate(120,180)">
    <rect width="380" height="270" rx="28" fill="url(#coral)" opacity="0.95"/>
    <text x="190" y="70" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="38" font-weight="800" fill="#fff">HOTMART</text>
    <text x="190" y="125" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="22" font-weight="600" fill="#fff">comisión real</text>
    <text x="190" y="200" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="78" font-weight="900" fill="#fff">~15%</text>
    <text x="190" y="240" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="16" fill="#fff" opacity="0.85">por venta</text>
  </g>
  <!-- VS -->
  <g transform="translate(540,290)">
    <circle cx="60" cy="30" r="55" fill="#fff" stroke="#1F2933" stroke-width="3"/>
    <text x="60" y="42" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="34" font-weight="900" fill="#1F2933">VS</text>
  </g>
  <!-- Caja derecha: Propia -->
  <g transform="translate(700,180)">
    <rect width="380" height="270" rx="28" fill="url(#brand)" opacity="0.95"/>
    <text x="190" y="70" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="32" font-weight="800" fill="#fff">PROPIA</text>
    <text x="190" y="125" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="22" font-weight="600" fill="#fff">coste real</text>
    <text x="190" y="200" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="60" font-weight="900" fill="#fff">~1,4%</text>
    <text x="190" y="240" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="16" fill="#fff" opacity="0.85">+ 62€/año fijos</text>
  </g>
  <!-- Título -->
  <text x="600" y="100" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="44" font-weight="900" fill="#1F2933">La cuenta real que nadie te muestra</text>
  <!-- Cursalia mark -->
  <g transform="translate(540,520)" opacity="0.6">
    <rect x="0" y="0" width="120" height="32" rx="16" fill="#1F2933"/>
    <text x="60" y="22" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="14" font-weight="700" fill="#fff">cursalia.com</text>
  </g>
</svg>
SVG;
    }
}
