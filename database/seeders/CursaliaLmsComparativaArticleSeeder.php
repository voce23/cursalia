<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Artículo satélite #2 — LMS open source en español 2026: 7 alternativas comparadas.
 *
 * Objetivo SEO: listicle long-tail con baja competencia en español. Keywords:
 *   - "LMS open source español"
 *   - "Moodle alternativas"
 *   - "plataforma cursos open source"
 *   - "comparativa LMS"
 *   - "Chamilo vs Moodle"
 *
 * Estrategia: posiciona Cursalia DENTRO de una lista honesta de 7 opciones,
 * sin forzarlo a "ganador". El lector ve 7 opciones, entiende los trade-offs,
 * y decide. Si elige Cursalia, perfecto; si no, ha leído un buen artículo
 * que enlaza al curso.
 */
class CursaliaLmsComparativaArticleSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        // Categoría Comparativas (ya creada por el seeder de Hotmart).
        $category = BlogCategory::firstWhere('slug', 'comparativas')
            ?? BlogCategory::create([
                'name' => 'Comparativas',
                'slug' => 'comparativas',
                'color' => '#FB7185',
                'status' => true,
            ]);

        $admin = Admin::orderBy('id')->first();

        $heroPath = 'blog/lms-open-source-espanol-hero.svg';
        Storage::disk('public')->put($heroPath, $this->buildHeroSvg());

        Blog::updateOrCreate(
            ['slug' => 'lms-open-source-espanol-2026-comparativa'],
            [
                'admin_id' => $admin?->id,
                'blog_category_id' => $category->id,
                'title' => 'LMS open source en español 2026: 7 alternativas comparadas (con tabla real)',
                'thumbnail' => $heroPath,
                'summary' => 'Comparativa honesta de los 7 LMS open source más usados en español: Moodle, Chamilo, Open edX, LearnDash, TutorLMS, Forma LMS y Cursalia. Stack, coste real, dificultad y para quién es cada uno.',
                'content' => $this->buildContent(),
                'meta_title' => 'LMS open source en español 2026: 7 alternativas comparadas',
                'meta_description' => 'Moodle, Chamilo, Open edX, LearnDash, TutorLMS, Forma LMS y Cursalia. Comparativa honesta con stack, coste real, dificultad y a quién le sirve cada uno.',
                'faq' => $this->buildFaq(),
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $this->command->info('  ✓ Artículo satélite #2 "LMS open source comparativa" publicado.');
    }

    private function buildContent(): string
    {
        return <<<'HTML'
<p>Si has decidido montar tu propia academia online sin pagar mensualidades a Hotmart, Thinkific o Kajabi, la siguiente pregunta es <strong>¿qué LMS open source uso?</strong>. La respuesta corta: depende. La respuesta honesta: hay 7 opciones serias en 2026, cada una con su propio trade-off, y la "mejor" depende de cuántos alumnos vas a tener, qué stack manejas y cuánto tiempo quieres dedicarle a configurarlo.</p>

<p>He probado las 7 en los últimos años. Aquí tienes la comparativa real, sin marketing.</p>

<div class="learn-box">
    <p><i class="fa-solid fa-bullseye"></i> Lo que vas a aprender</p>
    <ul>
        <li>Las 7 opciones serias de LMS open source con soporte real en español</li>
        <li>Tabla comparativa con stack, coste real anual, curva de aprendizaje y comunidad</li>
        <li>Para quién es cada una (profesor solo, equipo pequeño, institución grande)</li>
        <li>Las trampas de cada opción que la web oficial no menciona</li>
        <li>Cuál elegiría yo según tu perfil concreto</li>
    </ul>
</div>

<h2 id="resumen-tabla">Resumen en una tabla</h2>

<p>Si tienes prisa, esto es lo importante:</p>

<table>
    <thead>
        <tr>
            <th>LMS</th>
            <th>Stack</th>
            <th>Dificultad</th>
            <th>Coste anual real</th>
            <th>Ideal para</th>
        </tr>
    </thead>
    <tbody>
        <tr><td><strong>Moodle</strong></td><td>PHP + MySQL</td><td>🟥 Alta</td><td>~120€ (hosting)</td><td>Universidades, instituciones</td></tr>
        <tr><td><strong>Chamilo</strong></td><td>PHP + MySQL</td><td>🟧 Media</td><td>~80€ (hosting)</td><td>Centros formativos pequeños</td></tr>
        <tr><td><strong>Open edX</strong></td><td>Python + Django</td><td>🟥 Muy alta</td><td>~600€ (servidor potente)</td><td>Cursos masivos tipo MOOC</td></tr>
        <tr><td><strong>LearnDash</strong></td><td>WordPress plugin</td><td>🟨 Baja-media</td><td>~280€ (plugin + hosting)</td><td>Marketing avanzado en WP</td></tr>
        <tr><td><strong>TutorLMS</strong></td><td>WordPress plugin</td><td>🟨 Baja-media</td><td>~250€ (Pro + hosting)</td><td>Marketplace multi-instructor</td></tr>
        <tr><td><strong>Forma LMS</strong></td><td>PHP + MySQL</td><td>🟧 Media</td><td>~80€ (hosting)</td><td>Formación corporativa</td></tr>
        <tr><td><strong>Cursalia</strong></td><td>Laravel + Tailwind</td><td>🟩 Baja</td><td>~62€ (hosting)</td><td>Creadores individuales en español</td></tr>
    </tbody>
</table>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p>El "coste anual real" incluye hosting típico (40-100€), licencias de plugins si aplica y dominio. No incluye tu tiempo de configuración inicial (eso varía mucho).</p>
</div>

<hr>

<h2 id="moodle">1 · Moodle — el gigante histórico</h2>

<p>Moodle es <strong>el LMS open source más usado del mundo</strong>: 250 millones de usuarios, presente en universidades de 200 países. Si has estudiado en cualquier facultad pública española en los últimos 15 años, has usado Moodle (aunque te lo hayan vendido con otro nombre).</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Funcionalidad pedagógica brutal: foros, wikis, talleres, evaluación entre pares, badges, learning analytics. No hay nada que no haga.</li>
    <li>Comunidad enorme en español y plugins para casi todo.</li>
    <li>Aguanta universidades enteras con 50.000 alumnos.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Panel admin del año 2008. Funcional pero feo. Cambiar el diseño es <em>una pesadilla</em> si no eres dev frontend.</li>
    <li>Curva de aprendizaje brutal para el admin: te encuentras 14 menús de configuración el primer día.</li>
    <li>Pensado para institución, no para creador individual. Si eres profesor solo, le sobra el 80% de las opciones.</li>
    <li>Cobros automáticos requieren plugin de pago externo (~80€ adicionales/año).</li>
</ul>

<p><strong>Para quién:</strong> universidades, academias con varios profesores y muchos alumnos, formación corporativa con LDAP/SSO.<br>
<strong>NO es para ti si:</strong> eres un creador solo que quiere vender 5-50 cursos al año. Te ahoga.</p>

<p><strong>Web oficial:</strong> <a href="https://moodle.org" target="_blank" rel="noopener">moodle.org</a></p>

<h2 id="chamilo">2 · Chamilo — el español original</h2>

<p>Chamilo es un fork de Dokeos nacido en 2010 con un objetivo claro: ser <strong>la alternativa europea a Moodle, más simple y con mejor diseño</strong>. Su comunidad activa está en España y Latinoamérica.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Interfaz más limpia que Moodle. Se parece más a un curso que a un panel de hospital.</li>
    <li>Foco real en español: documentación en castellano y comunidad activa en LATAM.</li>
    <li>Setup más rápido (1-2 horas vs 4-6 de Moodle).</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Comunidad mucho más pequeña que Moodle. Si rompes algo, hay menos foros donde encontrar la respuesta.</li>
    <li>Plugins limitados. Lo que no viene de fábrica te lo desarrollas tú.</li>
    <li>Diseño envejecido (2018 más o menos). Si quieres una landing moderna, hay que retocar bastante CSS.</li>
</ul>

<p><strong>Para quién:</strong> centros formativos pequeños y medianos en España/LATAM, ONGs educativas.<br>
<strong>NO es para ti si:</strong> quieres un marketplace multi-instructor con cobros automáticos.</p>

<p><strong>Web oficial:</strong> <a href="https://chamilo.org" target="_blank" rel="noopener">chamilo.org</a></p>

<h2 id="open-edx">3 · Open edX — el peso pesado del MOOC</h2>

<p>Open edX es la plataforma que mueve <strong>edX, MIT OpenCourseWare, Stanford Online, Harvard, Microsoft Learn, IBM SkillsBuild</strong> y otras 50 instituciones top mundiales. Es un monstruo técnico hecho en Python/Django, con arquitectura de microservicios.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Aguanta cursos con 200.000 alumnos a la vez sin pestañear.</li>
    <li>Sistema de evaluación auto-corregida muy potente (incluido autograding de código).</li>
    <li>Interfaz moderna y limpia.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Instalación brutal: Docker, Kubernetes, mínimo 8 GB RAM, días de setup.</li>
    <li>Documentación oficial: 80% en inglés. La traducida está incompleta.</li>
    <li>Coste de hosting: <em>mínimo</em> un VPS de 30-50€/mes. Total al año: ~600€.</li>
    <li>Personalizar diseño requiere conocer Django + Mako templates + SASS. No es plug-and-play.</li>
</ul>

<p><strong>Para quién:</strong> instituciones grandes con presupuesto, cursos masivos con miles de alumnos simultáneos.<br>
<strong>NO es para ti si:</strong> vas a tener menos de 1.000 alumnos. Es matar moscas a cañonazos.</p>

<p><strong>Web oficial:</strong> <a href="https://openedx.org" target="_blank" rel="noopener">openedx.org</a></p>

<h2 id="learndash">4 · LearnDash — el plugin estrella de WordPress</h2>

<p>LearnDash es el LMS de pago más vendido para WordPress. <strong>No es 100% open source</strong> (es un plugin de pago: ~199$/año), pero te lo incluyo porque vive sobre WordPress (que sí es open source) y es la opción más popular para creadores que ya manejan WP.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Si ya tienes blog WordPress: en 1 hora tienes academia funcionando.</li>
    <li>Integraciones automáticas con WooCommerce, Stripe, ConvertKit, ActiveCampaign, Zapier.</li>
    <li>Documentación brutal: vídeo-tutoriales para cada función.</li>
    <li>Drip content (libera lecciones cada semana) viene de fábrica.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li><strong>Es plugin de pago.</strong> 199$/año la licencia básica, 399$ Plus. Eso multiplica tu coste anual.</li>
    <li>Si dejas de pagar, dejas de recibir actualizaciones y soporte.</li>
    <li>Depende del ecosistema WordPress: si WP se rompe, tu academia se rompe.</li>
    <li>Tu rendimiento depende de la calidad del hosting WP (los compartidos baratos lo hacen ir lento).</li>
</ul>

<p><strong>Para quién:</strong> creadores que YA tienen blog WordPress con tráfico y quieren añadir academia sin migrar a otro stack.<br>
<strong>NO es para ti si:</strong> quieres independencia total o no quieres depender de un plugin de pago anual.</p>

<p><strong>Web oficial:</strong> <a href="https://www.learndash.com" target="_blank" rel="noopener">learndash.com</a></p>

<h2 id="tutorlms">5 · TutorLMS — el rival joven de LearnDash</h2>

<p>TutorLMS apareció en 2019 como alternativa "más bonita y moderna" a LearnDash. Tiene <strong>versión gratis con funcionalidad útil real</strong> (no es el típico free trampa) y versión Pro a partir de 199$/año.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>La versión <strong>FREE es usable</strong>: cursos, lecciones, quiz, certificados básicos.</li>
    <li>Soporte nativo para marketplace multi-instructor (con la Pro).</li>
    <li>UI moderna, parece de 2025.</li>
    <li>Constructor de cursos drag-and-drop visual.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Comunidad más pequeña que LearnDash → menos tutoriales en YouTube en español.</li>
    <li>La versión FREE no incluye drip content ni assignments.</li>
    <li>Mismo problema que LearnDash: dependes del ecosistema WordPress.</li>
</ul>

<p><strong>Para quién:</strong> creadores en WordPress que quieren explorar antes de pagar, o que quieren montar un marketplace de varios instructores.<br>
<strong>NO es para ti si:</strong> no quieres lidiar con WordPress.</p>

<p><strong>Web oficial:</strong> <a href="https://tutorlms.com" target="_blank" rel="noopener">tutorlms.com</a></p>

<h2 id="forma-lms">6 · Forma LMS — el olvidado de las empresas</h2>

<p>Forma LMS es un LMS open source con foco en <strong>formación corporativa</strong>: SCORM, certificados, reporting, integración con HRM. Nacido en Italia, comunidad pequeña pero estable.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Cumple SCORM 1.2 y 2004, AICC y Tin Can. Si vendes cursos a empresas que requieren certificación estándar, esto vale ORO.</li>
    <li>Reporting y analytics decentes de fábrica.</li>
    <li>Multi-empresa: una instalación, varias compañías cliente con sus propios admins.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Documentación escasa y mayoritariamente en inglés/italiano.</li>
    <li>Interfaz de 2015 sin retoques. Funcional pero antiestética.</li>
    <li>Pocos plugins. Lo que no viene, lo desarrollas.</li>
    <li>Comunidad muy pequeña → si te atascas, foros con poca actividad.</li>
</ul>

<p><strong>Para quién:</strong> formación corporativa B2B con requisitos SCORM, consultorías de RRHH.<br>
<strong>NO es para ti si:</strong> eres creador individual vendiendo cursos B2C.</p>

<p><strong>Web oficial:</strong> <a href="https://www.formalms.org" target="_blank" rel="noopener">formalms.org</a></p>

<h2 id="cursalia">7 · Cursalia — el nuevo, pensado para creadores en español</h2>

<p>Honestidad ante todo: <strong>Cursalia es mi proyecto</strong>, así que tómate esta sección con escepticismo y prueba el demo antes de creerte nada. Pero te explico por qué lo construí y para quién es.</p>

<p>Cursalia nació en 2026 porque ninguno de los 6 anteriores resolvía bien <strong>el caso del creador individual en español que quiere lanzar su academia sin pagar mensualidades y sin saber programar</strong>. Está hecho en Laravel 13 + Tailwind 4, con un panel admin pensado para no-técnicos.</p>

<p><strong>Pros honestos:</strong></p>
<ul>
    <li>Panel admin claro: cambias colores, logo, textos, navegación con clicks. Cero código.</li>
    <li>Diseño 2026: responsive, accesible, con tu marca de verdad.</li>
    <li>Español de origen (no traducción automática). Documentación nativa en castellano.</li>
    <li>Coste real más bajo: ~60€/año total (solo hosting + dominio).</li>
    <li>Blog integrado SEO-friendly desde el primer día.</li>
</ul>

<p><strong>Contras honestos:</strong></p>
<ul>
    <li>Proyecto joven: comunidad aún en construcción. Si rompes algo, tienes que escribirme directamente (lo cual también es un pro, según se mire).</li>
    <li>No tiene SCORM (no apunta a corporativo).</li>
    <li>Versión FREE no incluye cobros automáticos: para Stripe/PayPal hay que esperar a la versión PRO (a finales de 2026).</li>
    <li>Sin marketplace multi-instructor todavía (también PRO).</li>
</ul>

<p><strong>Para quién:</strong> profesores, terapeutas, consultores y profesionales en español que quieren su primera academia online sin pelearse con WordPress ni Moodle, y sin pagar Hotmart.<br>
<strong>NO es para ti si:</strong> necesitas SCORM, marketplace multi-instructor desde el día 1, o vas a tener cientos de miles de alumnos simultáneos.</p>

<p><strong>Web oficial:</strong> esta misma. Si te interesa, en este blog tengo un <a href="/blog?category=curso-cursalia">curso gratis de 14 lecciones</a> para montártelo paso a paso.</p>

<hr>

<h2 id="como-elegir">¿Cómo elijo según mi caso?</h2>

<p>Resumen rápido por perfil:</p>

<div class="callout callout-tip">
    <i class="fa-solid fa-user"></i>
    <p><strong>"Soy profesor solo, quiero vender mis primeros cursos en español"</strong><br>
    → Cursalia (si quieres independencia total) o TutorLMS Free (si ya manejas WordPress)</p>
</div>

<div class="callout callout-tip">
    <i class="fa-solid fa-users"></i>
    <p><strong>"Tengo academia con varios profesores y quiero marketplace"</strong><br>
    → TutorLMS Pro o LearnDash + plugins de marketplace</p>
</div>

<div class="callout callout-tip">
    <i class="fa-solid fa-building"></i>
    <p><strong>"Soy institución educativa formal (universidad, instituto)"</strong><br>
    → Moodle (sin discusión). Para muy escalado: Open edX</p>
</div>

<div class="callout callout-tip">
    <i class="fa-solid fa-briefcase"></i>
    <p><strong>"Vendo formación a empresas, necesito SCORM y certificaciones"</strong><br>
    → Forma LMS o Moodle con plugins corporativos</p>
</div>

<div class="callout callout-tip">
    <i class="fa-solid fa-language"></i>
    <p><strong>"Estoy en LATAM, prefiero algo con foco hispano"</strong><br>
    → Chamilo o Cursalia</p>
</div>

<h2 id="la-trampa-tipica">La trampa típica al elegir LMS</h2>

<p>La cagada que veo más repetida: <strong>elegir Moodle "porque lo usan las universidades"</strong> cuando tú eres un profesor solo.</p>

<p>Te encuentras con un panel de 14 menús, 50 plugins, y para cambiar el color del botón "Inscribirse" necesitas saber LESS, manejar plantillas Bootstrap antiguas y reiniciar el cache cada vez. Te frustras, lo dejas a medias, y vuelves a Hotmart "porque es más rápido".</p>

<div class="callout callout-danger">
    <i class="fa-solid fa-circle-exclamation"></i>
    <p><strong>Regla de oro:</strong> usa la herramienta proporcional a tu necesidad. Moodle es Excel y tú quieres hacer una lista de la compra. Una libreta basta.</p>
</div>

<hr>

<h2 id="conclusion">Conclusión: la pregunta correcta no es "cuál es el mejor"</h2>

<p>Lo he dicho 5 veces y lo repito: <strong>no hay un "mejor LMS"</strong>. Hay un mejor LMS <em>para tu caso</em>. La pregunta correcta es:</p>

<ul>
    <li>¿Cuántos alumnos voy a tener en el primer año? (10? 100? 10.000?)</li>
    <li>¿Sé programar o lo único que sé es usar Word? (esto define tu techo)</li>
    <li>¿Cuánto tiempo puedo dedicar al setup inicial? (4 horas? 4 días? 4 semanas?)</li>
    <li>¿Necesito SCORM o certificados oficiales? (B2B sí; B2C no)</li>
    <li>¿Quiero independencia total o me da igual depender de un plugin de pago?</li>
</ul>

<p>Responde esas 5 y la elección se hace casi sola.</p>

<h3 id="proximos-pasos">¿Por dónde sigo?</h3>

<p>Si después de leer esto te has dado cuenta de que <strong>Cursalia encaja con tu caso</strong>, te dejo dos pasos:</p>

<div class="callout callout-tip">
    <i class="fa-solid fa-graduation-cap"></i>
    <p><strong>Curso gratis · 14 lecciones · Construye tu academia online</strong></p>
    <p>De cero a producción en menos de 4 semanas, sin programar. <a href="/blog?category=curso-cursalia">Empezar el curso →</a></p>
</div>

<div class="callout callout-info">
    <i class="fa-solid fa-coins"></i>
    <p><strong>¿Vienes de Hotmart? Lee este artículo primero</strong></p>
    <p>Hotmart cobra ~15% real por venta. Tu propia plataforma cuesta 62€/año fijos. <a href="/blog/hotmart-vs-tu-propia-plataforma-de-cursos">Ver la cuenta real →</a></p>
</div>

<h3 id="resumen">📚 Resumen en 5 puntos</h3>

<ol>
    <li><strong>No hay "mejor LMS open source"</strong>. Hay 7 opciones serias en español y la mejor depende de tu caso: número de alumnos, stack, idioma, presupuesto y nivel técnico.</li>
    <li><strong>Moodle es exceso para creadores individuales.</strong> Está pensado para universidades. Si solo tienes 50 alumnos te ahoga.</li>
    <li><strong>WordPress + LearnDash/TutorLMS es la vía más rápida si ya tienes WP</strong>, pero suma 200-280€/año de licencias y dependes del ecosistema WP.</li>
    <li><strong>Open edX y Forma LMS son nicho:</strong> el primero para MOOCs masivos, el segundo para corporate con SCORM. No los uses si no encajas exactamente.</li>
    <li><strong>Chamilo y Cursalia son los más accesibles para hispanohablantes individuales</strong>: Chamilo si valoras tradición y comunidad establecida, Cursalia si quieres algo más moderno y simple.</li>
</ol>
HTML;
    }

    private function buildFaq(): array
    {
        return [
            [
                'q' => '¿Cuál es el mejor LMS open source en español en 2026?',
                'a' => 'Depende del caso. Para universidades e instituciones: Moodle. Para centros formativos pequeños en LATAM: Chamilo. Para creadores individuales que ya manejan WordPress: TutorLMS o LearnDash. Para creadores individuales en español que quieren independencia total y simplicidad: Cursalia. Para formación corporativa con SCORM: Forma LMS. Para cursos masivos tipo MOOC: Open edX.',
            ],
            [
                'q' => '¿Moodle es realmente gratis o tiene letra pequeña?',
                'a' => 'Moodle es 100% open source y gratis. Lo que pagas es el hosting (40-80€/año en un servidor compartido decente) y, si quieres cobros automáticos integrados, un plugin de pago como Moodle Pay o equivalente (~80€/año). No hay licencia oculta.',
            ],
            [
                'q' => '¿Es LearnDash open source?',
                'a' => 'No, LearnDash es un plugin de pago para WordPress (199$/año la licencia básica). WordPress sí es open source, pero LearnDash en sí no lo es. Si dejas de pagar, dejas de recibir actualizaciones y soporte aunque puedas seguir usando la versión que ya tienes instalada.',
            ],
            [
                'q' => '¿Cuál es el LMS open source más fácil de instalar para alguien sin conocimientos técnicos?',
                'a' => 'Por curva de instalación, de más fácil a más difícil: Cursalia y TutorLMS (instalación en 30-60 min con tutorial), Chamilo (1-2 horas), LearnDash (1 hora si ya tienes WordPress), Moodle y Forma LMS (4-6 horas la primera vez), Open edX (días, requiere Docker/Kubernetes).',
            ],
            [
                'q' => '¿Puedo migrar mis cursos de una plataforma open source a otra?',
                'a' => 'Sí, pero con esfuerzo. La mayoría soportan importación de SCORM, así que si tus cursos están en formato SCORM puedes pasarlos entre Moodle, Chamilo y Forma LMS sin perderlos. Si tus cursos son simples (texto + vídeo), se reupload manualmente en la nueva plataforma. Lo que casi nunca migra bien es el historial de calificaciones y los datos de alumnos: prepárate a empezar limpio.',
            ],
            [
                'q' => '¿Qué LMS open source es mejor para SEO?',
                'a' => 'Para SEO importan tres cosas: velocidad de carga, posibilidad de tener blog integrado y limpieza del HTML generado. Cursalia y los basados en WordPress (LearnDash, TutorLMS) son los más SEO-friendly por defecto. Moodle y Chamilo requieren ajustes manuales o plugins para SEO básico. Open edX y Forma LMS no están pensados para tráfico orgánico desde Google.',
            ],
        ];
    }

    private function buildHeroSvg(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" role="img" aria-label="7 LMS open source comparados">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#F0FDF4"/>
      <stop offset="1" stop-color="#FEF3F2"/>
    </linearGradient>
    <linearGradient id="brand" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#10B981"/>
      <stop offset="1" stop-color="#047857"/>
    </linearGradient>
    <linearGradient id="coral" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#FB7185"/>
      <stop offset="1" stop-color="#E11D48"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <!-- Halos decorativos -->
  <circle cx="120" cy="100" r="160" fill="#10B981" opacity="0.08"/>
  <circle cx="1080" cy="530" r="200" fill="#FB7185" opacity="0.08"/>

  <!-- Etiqueta -->
  <g transform="translate(420,60)">
    <rect width="360" height="38" rx="19" fill="#fff" stroke="#10B981" stroke-width="2"/>
    <text x="180" y="25" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="13" font-weight="800" fill="#047857" letter-spacing="2">COMPARATIVA HONESTA · 2026</text>
  </g>

  <!-- Título -->
  <text x="600" y="170" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="48" font-weight="900" fill="#1F2933">LMS open source en español</text>
  <text x="600" y="220" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="32" font-weight="700" fill="#FB7185">7 alternativas comparadas</text>

  <!-- Iconos LMS -->
  <g transform="translate(140,300)">
    <g>
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">Moodle</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">Universidad</text>
    </g>
    <g transform="translate(180,0)">
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">Chamilo</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">Centros pequeños</text>
    </g>
    <g transform="translate(360,0)">
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">Open edX</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">MOOCs masivos</text>
    </g>
    <g transform="translate(540,0)">
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">LearnDash</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">WordPress Pro</text>
    </g>
    <g transform="translate(720,0)">
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">TutorLMS</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">Marketplace WP</text>
    </g>
  </g>

  <g transform="translate(330,400)">
    <g>
      <rect width="160" height="80" rx="16" fill="#fff" stroke="#1F2933" stroke-width="2"/>
      <text x="80" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="20" font-weight="800" fill="#1F2933">Forma LMS</text>
      <text x="80" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#6B7280">Corporate SCORM</text>
    </g>
    <g transform="translate(200,0)">
      <rect width="200" height="80" rx="16" fill="url(#brand)" stroke="none"/>
      <text x="100" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="22" font-weight="900" fill="#fff">Cursalia</text>
      <text x="100" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="11" fill="#D1FAE5">Creador individual ES</text>
    </g>
    <g transform="translate(440,0)">
      <rect width="200" height="80" rx="16" fill="#1F2933" stroke="none"/>
      <text x="100" y="35" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="14" font-weight="700" fill="#fff">¿Cuál te conviene?</text>
      <text x="100" y="58" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="10" fill="#9CA3AF">tabla completa dentro ↓</text>
    </g>
  </g>

  <!-- Footer marca -->
  <g transform="translate(540,560)" opacity="0.6">
    <rect x="0" y="0" width="120" height="32" rx="16" fill="#1F2933"/>
    <text x="60" y="22" text-anchor="middle" font-family="Inter,Arial,sans-serif" font-size="14" font-weight="700" fill="#fff">cursalia.com</text>
  </g>
</svg>
SVG;
    }
}
