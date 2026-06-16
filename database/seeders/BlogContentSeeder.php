<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Siembra 6 posts de blog del LMS Cursalia con estilo /guia
 * (artículo profesional, callouts, código con Prism, FAQ y portada).
 */
class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = (int) DB::table('admins')->orderBy('id')->value('id');
        if (! $adminId) {
            return;
        }

        $catMap = BlogCategory::pluck('id', 'slug')->all();
        $getCat = fn (string $slug, string $name) => $catMap[$slug]
            ?? BlogCategory::create(['name' => $name, 'slug' => $slug, 'color' => '#10b981', 'status' => true])->id;

        $posts = [
            [
                'slug' => 'como-crear-tu-primer-curso-en-cursalia',
                'title' => 'Cómo crear tu primer curso en Cursalia (paso a paso)',
                'category' => 'tutoriales',
                'thumbnail' => 'blog/como-crear-tu-primer-curso-en-cursalia.png',
                'summary' => 'Tutorial práctico para crear tu primer curso en Cursalia desde el panel admin. Estructura, lecciones, videos y publicación, en menos de 30 minutos.',
                'meta_title' => 'Cómo crear tu primer curso online en Cursalia · Tutorial paso a paso',
                'meta_description' => 'Aprende a crear tu primer curso en Cursalia LMS: cómo estructurar módulos y lecciones, subir videos, añadir quizzes y publicar tu curso para vender.',
                'content' => '<p>Tienes tu LMS Cursalia instalado y ahora quieres crear tu primer curso. <strong>Genial</strong>. En este tutorial te llevo paso a paso, desde el panel admin hasta el botón "Publicar". En 30 minutos tendrás tu primer curso vivo, listo para que tus alumnos se inscriban.</p>

<h2>1. Antes de empezar</h2>
<p>Necesitas dos cosas listas:</p>
<ul>
<li><strong>Tu plan del curso:</strong> qué módulos tendrá, cuántas lecciones por módulo, qué quieres que aprendan al final.</li>
<li><strong>Tu primer video o texto</strong> grabado (o al menos uno, para arrancar).</li>
</ul>
<blockquote><strong>💡 Consejo:</strong> no esperes a tenerlo todo perfecto. Sube un primer módulo, recibe feedback de los primeros alumnos y ve completando el resto.</blockquote>

<h2>2. Entra al panel admin</h2>
<p>Inicia sesión como administrador y navega al menú <strong>Cursos → Crear curso</strong>. Verás un formulario amplio dividido en secciones.</p>

<h2>3. Datos básicos del curso</h2>
<ul>
<li><strong>Título:</strong> claro y orientado al beneficio (ej: "Aprende a hacer cheesecake como un profesional").</li>
<li><strong>Slug:</strong> se genera solo, pero edítalo si quieres una URL más limpia.</li>
<li><strong>Categoría e idioma:</strong> elige lo que aplique.</li>
<li><strong>Precio:</strong> 0 si es gratis, o el monto en tu moneda.</li>
<li><strong>Imagen de portada:</strong> 16:9, 1200×675 px funciona perfecto.</li>
</ul>

<h2>4. Estructura el contenido</h2>
<p>Dentro del curso, crea <strong>capítulos (módulos)</strong> y dentro de cada uno, <strong>lecciones</strong>. Una estructura típica:</p>
<ul>
<li>Módulo 1 · Bienvenida y bases</li>
<li>Módulo 2 · Primeros pasos prácticos</li>
<li>Módulo 3 · Técnicas avanzadas</li>
<li>Módulo 4 · Casos prácticos y cierre</li>
</ul>
<blockquote><strong>⚠️ Error común:</strong> meter 30 lecciones en un solo módulo. Mejor 5-10 por módulo. Es más fácil para el alumno y mejor para el SEO interno.</blockquote>

<h2>5. Sube tus videos por enlace (no a tu hosting)</h2>
<p>Cursalia te permite subir videos como archivo, pero para mantener tu hosting ligero te recomendamos usar enlaces:</p>
<ul>
<li><strong>YouTube (no listado):</strong> gratis, fácil, ideal para empezar.</li>
<li><strong>Bunny.net:</strong> económico y profesional, sin marca de YouTube.</li>
</ul>
<p>En cada lección, pega el enlace en el campo <em>"URL del video"</em>.</p>

<h2>6. Añade un quiz al final de cada módulo</h2>
<p>Refuerza el aprendizaje con un mini-test. En el editor de lección activa <em>"Esta lección tiene quiz"</em> y añade 3-5 preguntas de opción múltiple con la respuesta correcta marcada.</p>

<h2>7. Configura el certificado</h2>
<p>Activa el certificado en los ajustes del curso. Tus alumnos podrán descargar un PDF firmado al completar todas las lecciones y aprobar los quizzes.</p>

<blockquote><strong>🔒 Importante:</strong> el certificado de Cursalia es un reconocimiento de finalización del curso, no un título oficial. Si necesitas créditos académicos, coordina con tu colegio profesional o universidad.</blockquote>

<h2>8. Publica tu curso</h2>
<p>Cuando tengas al menos un módulo completo:</p>
<ol>
<li>Verifica los datos básicos.</li>
<li>Cambia el estado a <strong>"Publicado"</strong>.</li>
<li>Comparte el enlace de tu curso con tus primeros alumnos.</li>
</ol>

<h2>Conclusión</h2>
<p>Crear tu primer curso es el paso más importante. Una vez que lo tengas vivo, todo lo demás (mejorar, escalar, automatizar) es relativamente fácil. ¿No quieres empezar de cero? Mira nuestras <a href="/courses">plantillas PRO de la tienda</a>: traen los cursos ya estructurados, solo personalizas con tu marca y tus videos.</p>',
                'faq' => [
                    ['q' => '¿Cuánto tiempo toma crear un curso completo?', 'a' => 'Para un curso de calidad media, entre 20 y 40 horas de trabajo (grabación, edición y carga). Pero puedes publicar un primer módulo en 1-2 horas y completarlo gradualmente.'],
                    ['q' => '¿Puedo cambiar el precio después?', 'a' => 'Sí, en cualquier momento. Los alumnos que ya pagaron mantienen su acceso al precio original.'],
                    ['q' => '¿Cursalia funciona en mi celular?', 'a' => 'Sí, tanto el panel admin como el sitio público de los alumnos son responsive.'],
                ],
            ],

            [
                'slug' => 'posicionar-academia-en-google-seo',
                'title' => 'Cómo posicionar tu academia en Google (SEO básico para LMS)',
                'category' => 'tutoriales',
                'thumbnail' => 'blog/posicionar-academia-en-google-seo.png',
                'summary' => 'Trucos prácticos de SEO para academias online. Cómo conseguir que tus cursos aparezcan en Google sin gastar en publicidad.',
                'meta_title' => 'SEO para academias online · Cómo posicionar tu LMS en Google',
                'meta_description' => 'Guía SEO para academias online: optimización de cursos, blog, palabras clave, Schema.org y trucos para conseguir tráfico gratis desde Google.',
                'content' => '<p>Crear tu academia es el primer paso. <strong>Que la gente la encuentre</strong> es el segundo, y muchas veces el más olvidado. Aquí te dejo una guía SEO práctica, sin tecnicismos, para que Google te tome en serio.</p>

<h2>1. Por qué el SEO es vital para una academia online</h2>
<ul>
<li>Las búsquedas como "curso de repostería online" o "curso EMC online" no paran de crecer.</li>
<li>El tráfico de Google es <strong>gratis y recurrente</strong>: una vez que posicionas, te entran alumnos sin pagar publicidad.</li>
<li>Es la única forma sostenible de crecer sin depender de redes sociales.</li>
</ul>

<h2>2. Optimiza los datos básicos de tu academia</h2>
<p>En tu panel admin → Ajustes del sitio:</p>
<ul>
<li><strong>Título del sitio:</strong> "[Tu marca] · Academia online de [tu nicho]"</li>
<li><strong>Meta descripción:</strong> 2 frases que resuman lo que ofreces.</li>
<li><strong>Idioma:</strong> español.</li>
<li><strong>OG image:</strong> sube tu logo o portada para que se vea bien cuando compartan tu enlace.</li>
</ul>

<h2>3. Cada curso = una página optimizada</h2>
<p>Para cada curso, completa:</p>
<ul>
<li><strong>Slug</strong> con la palabra clave principal (ej: <code>cheesecake-frio-sin-horno</code>).</li>
<li><strong>Meta título:</strong> incluye la palabra clave + un beneficio.</li>
<li><strong>Descripción larga:</strong> mínimo 300 palabras con palabras clave naturales.</li>
<li><strong>Imagen alt:</strong> describe la imagen con texto relevante.</li>
</ul>
<blockquote><strong>💡 Consejo:</strong> piensa en tu alumno buscando en Google. ¿Qué frase exacta escribiría? Esa es tu palabra clave. Por ejemplo: "como hacer detergente liquido casero rentable".</blockquote>

<h2>4. El blog: tu mejor activo SEO</h2>
<p>Un blog activo es lo que más tráfico orgánico te trae. Posts ganadores:</p>
<ul>
<li><strong>"Cómo hacer X"</strong> — tutoriales prácticos.</li>
<li><strong>"X mejores Y de 2026"</strong> — listas comparativas.</li>
<li><strong>"X vs Y"</strong> — comparativas entre opciones.</li>
<li><strong>"Errores comunes al hacer X"</strong> — resuelve dudas reales.</li>
</ul>
<p>Apunta a 1-2 posts por semana al inicio.</p>

<h2>5. Datos estructurados (Schema.org)</h2>
<p>Cursalia ya incluye Schema.org para cursos automáticamente. Esto le dice a Google que tu página describe un curso, no un producto cualquiera. Aparecerás en resultados enriquecidos con precio, rating y duración:</p>

<div class="code-block"><div class="code-header"><span class="code-lang">json</span></div><pre><code class="language-json">{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "Tu curso aquí",
  "description": "Tu descripción aquí",
  "provider": {
    "@type": "Organization",
    "name": "Tu academia"
  }
}</code></pre></div>

<h2>6. Velocidad y mobile-first</h2>
<p>Google penaliza sitios lentos:</p>
<ul>
<li>Subir videos por enlace (no a tu hosting).</li>
<li>Optimizar imágenes (WebP, compresión).</li>
<li>Activar caché de Laravel: <code>php artisan optimize</code>.</li>
</ul>

<h2>7. Backlinks y autoridad</h2>
<p>Que otros sitios te enlacen es una señal fuerte. Cómo conseguirlos:</p>
<ul>
<li><strong>Tu marca en redes</strong> con enlace al sitio.</li>
<li><strong>Colaboraciones</strong> con otros instructores de tu nicho.</li>
<li><strong>Guest posts</strong> en blogs de tu sector.</li>
</ul>
<blockquote><strong>⚠️ Error común:</strong> comprar backlinks en webs raras. Google los detecta y te penaliza. Mejor crece lento pero limpio.</blockquote>

<h2>8. Mide y mejora</h2>
<p>Conecta Google Search Console y Google Analytics. Revisa cada mes:</p>
<ul>
<li>¿Por qué palabras clave estás apareciendo?</li>
<li>¿Cuáles páginas tienen mejor CTR?</li>
<li>¿Hay errores 404 que reparar?</li>
</ul>

<h2>Conclusión</h2>
<p>El SEO es una carrera de largo plazo, pero es la única forma de tener alumnos llegando solos, sin gastar en publicidad cada mes. Empieza por lo básico, sé constante, y en 3-6 meses verás resultados claros.</p>',
                'faq' => [
                    ['q' => '¿Cuánto tardan los efectos del SEO?', 'a' => 'Los primeros movimientos aparecen entre la semana 4 y 8. Los resultados consistentes vienen entre los 3 y 6 meses de trabajo constante.'],
                    ['q' => '¿Cuántos posts debo publicar para empezar?', 'a' => 'Un buen objetivo de arranque es 1 post de calidad por semana durante 3 meses (12-13 posts). Luego puedes ralentizar o mantener.'],
                    ['q' => '¿Necesito pagar a una agencia SEO?', 'a' => 'No al principio. Hay mucho que puedes hacer tú mismo. Considera una agencia solo cuando ya facturas y quieras escalar.'],
                ],
            ],

            [
                'slug' => '10-ideas-cursos-online-rentables-2026',
                'title' => '10 ideas de cursos online rentables para emprender en 2026',
                'category' => 'tips-y-trucos',
                'thumbnail' => 'blog/10-ideas-cursos-online-rentables-2026.png',
                'summary' => '10 nichos validados para crear cursos online con buena demanda y poca saturación. Ideal si todavía no decides qué enseñar.',
                'meta_title' => '10 ideas de cursos online rentables 2026 · Nichos validados',
                'meta_description' => 'Las 10 mejores ideas de cursos online rentables para 2026: repostería, EMC médica, detergentes, fitness, idiomas y más. Validados con datos reales.',
                'content' => '<p>¿Quieres montar tu academia online pero no sabes <strong>qué enseñar</strong>? Aquí van 10 ideas validadas con demanda real y poca competencia. Todas se pueden lanzar con Cursalia.</p>

<h2>1. Repostería para vender desde casa</h2>
<p>Demanda gigante en LATAM. Madres y mujeres que quieren emprender desde su cocina. Mercado: medio. Competencia: media. <strong>Precio sugerido del curso: $29-49 USD.</strong></p>
<blockquote><strong>💡 Plantilla lista:</strong> mira <a href="/courses">"Repostería para Vender PRO"</a> en nuestra tienda — 6 cursos + 6 posts incluidos.</blockquote>

<h2>2. Educación Médica Continua (EMC)</h2>
<p>Médicos OBLIGADOS por ley a actualizarse cada año. Mercado: alto. Competencia: baja en español. <strong>Precio sugerido: $50-150 USD por curso.</strong> Requiere autoridad y/o acreditación.</p>

<h2>3. Detergentes y limpieza artesanal</h2>
<p>Negocio rentable para emprendedores de barrio. Margen alto en producción. Mercado: medio. Competencia: baja. <strong>Precio: $19-39 USD.</strong></p>

<h2>4. Jabones y cosmética natural</h2>
<p>Auge del "lo natural". Producto bonito, fácil de fotografiar para marketing. Mercado: medio-alto. Competencia: media. <strong>Precio: $29-49 USD.</strong></p>

<h2>5. Inglés práctico para profesionales</h2>
<p>Necesidad eterna en LATAM. Mejor si te especializas: inglés médico, inglés para negocios, inglés para programadores. Mercado: muy alto. Competencia: alta (pero hay sub-nichos abiertos). <strong>Precio: $97-297 USD.</strong></p>

<h2>6. Programación práctica con resultado visible</h2>
<p>"Crea tu primera web en 7 días" funciona mejor que "Aprende JavaScript desde cero". Mercado: alto. Competencia: alta. <strong>Precio: $47-197 USD.</strong></p>

<h2>7. Marketing digital para emprendedores locales</h2>
<p>Específico para quien tiene un negocio de barrio: WhatsApp, Instagram, fotos con celular. Mercado: alto. Competencia: media. <strong>Precio: $39-97 USD.</strong></p>

<h2>8. Bienestar mental y manejo del estrés</h2>
<p>Tendencia creciente post-pandemia. Requiere credenciales (psicología, mindfulness certificado). Mercado: alto. Competencia: media. <strong>Precio: $49-127 USD.</strong></p>

<h2>9. Costura, tejido y manualidades</h2>
<p>Nicho con audiencia fiel. Vídeos largos retienen muy bien. Mercado: medio. Competencia: media. <strong>Precio: $19-49 USD.</strong></p>

<h2>10. Decoración y catering para eventos</h2>
<p>Bodas, cumpleaños, bautizos. Muy estacional pero alto valor. Mercado: medio. Competencia: baja en español. <strong>Precio: $47-127 USD.</strong></p>

<blockquote><strong>⚠️ Recuerda:</strong> el éxito de un curso no depende solo del nicho. Depende de tu capacidad de explicar bien y promocionar mejor. Elige un tema que te apasione y conozcas a fondo.</blockquote>

<h2>Cómo elegir tu nicho</h2>
<ul>
<li><strong>¿Lo dominas?</strong> Si tienes 5+ años de experiencia, es un punto a favor.</li>
<li><strong>¿Hay demanda?</strong> Busca el término en Google Trends.</li>
<li><strong>¿Compite alguien fuerte?</strong> Si ya hay 10 academias enormes, busca un sub-nicho.</li>
<li><strong>¿Te emociona?</strong> Vas a grabar muchas horas. Mejor que te guste.</li>
</ul>

<h2>Conclusión</h2>
<p>El mejor curso online es el que <strong>combina lo que sabes</strong> con <strong>lo que el mercado quiere</strong>. Empieza con uno, valídalo con 20-30 alumnos, y ahí decides si te enfocas o pivotas.</p>',
                'faq' => [
                    ['q' => '¿Cuánto se gana con un curso online?', 'a' => 'Depende del nicho, precio y promoción. Un curso a $49 USD con 100 ventas/mes = $4,900 USD/mes. Mucha gente logra esto con constancia.'],
                    ['q' => '¿Necesito ser experto reconocido?', 'a' => 'No, pero sí necesitas saber al menos un nivel más que tu alumno objetivo. La autenticidad pesa mucho más que un título.'],
                    ['q' => '¿Y si elijo mal nicho?', 'a' => 'No pasa nada. Mucha gente pivota su academia 1-2 veces hasta encontrar el nicho ganador. Lo importante es empezar.'],
                ],
            ],

            [
                'slug' => 'primeros-100-alumnos-marketing-academia',
                'title' => 'Tus primeros 100 alumnos: marketing práctico para tu academia',
                'category' => 'tips-y-trucos',
                'thumbnail' => 'blog/primeros-100-alumnos-marketing-academia.png',
                'summary' => 'Estrategias prácticas para conseguir tus primeros 100 alumnos sin gastar en publicidad. Aprovecha tu círculo, redes y contenido.',
                'meta_title' => 'Cómo conseguir tus primeros 100 alumnos para tu academia online',
                'meta_description' => 'Marketing práctico para academias online: cómo conseguir tus primeros 100 alumnos con WhatsApp, redes, email y contenido. Sin gastar en publicidad.',
                'content' => '<p>Llevar tu academia de 0 a 100 alumnos es el salto más difícil. Una vez ahí, todo se acelera: testimonios, recomendaciones, mejoras del producto. Aquí cómo lograrlo sin gastar en publicidad.</p>

<h2>1. Tus 10 primeros alumnos vienen de tu círculo</h2>
<p>Suena obvio pero pocos lo aprovechan. Lista en una hoja:</p>
<ul>
<li>10 amigos cercanos que confían en ti.</li>
<li>10 familiares con quienes hablas seguido.</li>
<li>10 compañeros de trabajo o ex-compañeros.</li>
<li>10 personas de tu nicho profesional.</li>
</ul>
<p>Mándales un WhatsApp <strong>personalizado</strong>: "Hola [nombre], acabo de lanzar mi academia online. ¿Te interesa? Si entras como fundador, te doy 50% descuento."</p>
<blockquote><strong>💡 Truco:</strong> NO copies y pegues el mismo mensaje. Persoaliza cada uno. Tasa de respuesta sube del 5% al 30%.</blockquote>

<h2>2. Da algo gratis para captar emails</h2>
<p>Antes de pedirles que paguen, regálales valor. Opciones:</p>
<ul>
<li>Una <strong>lección muestra</strong> de tu curso.</li>
<li>Un <strong>PDF descargable</strong> con tu mejor consejo.</li>
<li>Un <strong>mini-curso de 3 lecciones</strong> gratis.</li>
</ul>
<p>A cambio, su email. Esa lista es oro: cuando lances nuevo curso o cupón, ya tienes a quién avisar.</p>

<h2>3. Crece en una red, no en todas</h2>
<p>Mejor ser <strong>el rey de Instagram</strong> que la novia de todas las redes. Elige UNA según tu nicho:</p>
<ul>
<li><strong>Instagram/TikTok</strong>: nichos visuales (repostería, manualidades, fitness).</li>
<li><strong>LinkedIn</strong>: nichos profesionales (EMC, programación, marketing).</li>
<li><strong>Facebook</strong>: nichos locales y mayores de 35 años.</li>
<li><strong>YouTube</strong>: tutoriales y temas técnicos.</li>
</ul>

<h2>4. Calidad &gt; cantidad en redes</h2>
<p>Publica <strong>4 veces por semana</strong> con contenido útil. NO publiques diario contenido pobre. La gente recuerda al que les enseña algo, no al que llena su feed.</p>

<h2>5. Crea urgencia con cupones limitados</h2>
<p>"Cupón FUNDADORES, 50% off, solo las primeras 20 personas". Funciona increíble porque:</p>
<ul>
<li>Crea <strong>urgencia real</strong> (no inventada).</li>
<li>Premia a los pioneros.</li>
<li>Los testimonios de esos 20 te traen los próximos 80.</li>
</ul>

<h2>6. Webinar gratuito de lanzamiento</h2>
<p>Anuncia un webinar gratis de 45 minutos sobre UN tema específico de tu nicho. Al final, presenta tu curso con un descuento por 24 horas. Conversión típica: 5-15% de los asistentes.</p>
<blockquote><strong>⚠️ Error común:</strong> hacer un webinar que es 90% promoción. La gente lo huele y se va. Mejor 80% valor + 20% oferta.</blockquote>

<h2>7. Pide testimonios desde el día 1</h2>
<p>Al alumno #1 que compre con cupón fundador, pídele en privado: <em>"Cuando termines el módulo 1, me cuentas en un audio de 30 segundos qué te pareció ¿te parece?"</em>. Esos testimonios reales valen más que mil anuncios.</p>

<h2>8. Email marketing simple pero constante</h2>
<p>Una vez por semana, manda un email a tu lista:</p>
<ul>
<li>Un <strong>consejo útil</strong> (60% del contenido).</li>
<li>Una <strong>historia personal</strong> o de alumno (30%).</li>
<li>Un <strong>llamado a la acción suave</strong> (10%).</li>
</ul>

<h2>9. Colabora con otros instructores</h2>
<p>Encuentra a alguien con audiencia similar pero NO competidor directo. Promociónense mutuamente. Gana-gana.</p>

<h2>10. Mide y ajusta</h2>
<p>Cada mes revisa:</p>
<ul>
<li>¿De dónde vienen los alumnos? (WhatsApp, redes, Google)</li>
<li>¿Cuál es tu tasa de conversión?</li>
<li>¿Qué post/email tuvo más respuesta?</li>
</ul>

<h2>Conclusión</h2>
<p>Conseguir 100 alumnos no es magia, es <strong>constancia</strong>. Si haces estos 10 pasos durante 3-6 meses, llegas. Y una vez ahí, el siguiente 1000 cuesta mucho menos esfuerzo.</p>',
                'faq' => [
                    ['q' => '¿Cuánto tardo en conseguir mis primeros 100 alumnos?', 'a' => 'Entre 2 y 6 meses, dependiendo de tu nicho y constancia. Con un círculo activo y buen contenido, puede ser más rápido.'],
                    ['q' => '¿Necesito gastar en publicidad pagada?', 'a' => 'No al principio. Los primeros 100 alumnos los consigues con esfuerzo orgánico. La publicidad tiene sentido cuando ya tienes producto validado y un sistema que convierte.'],
                    ['q' => '¿Y si no tengo red de contactos?', 'a' => 'Empieza creando una. Comenta posts de otros en tu nicho, participa en grupos, ofrece valor antes de pedir algo. Toma tiempo pero funciona.'],
                ],
            ],

            [
                'slug' => 'cursalia-vs-wordpress-learndash',
                'title' => 'Cursalia vs WordPress + LearnDash: ¿qué te conviene?',
                'category' => 'comparativas',
                'thumbnail' => 'blog/cursalia-vs-wordpress-learndash.png',
                'summary' => 'Comparativa honesta entre Cursalia y WordPress con LearnDash. Costos, facilidad, escalabilidad. ¿Cuál es mejor para tu academia?',
                'meta_title' => 'Cursalia vs WordPress + LearnDash · Comparativa LMS 2026',
                'meta_description' => 'Cursalia vs WordPress con LearnDash: análisis honesto de precios, facilidad, rendimiento y soporte. ¿Cuál es la mejor plataforma LMS para tu academia online?',
                'content' => '<p>Si quieres montar tu academia online, probablemente has visto estas dos opciones: <strong>WordPress + LearnDash</strong> (el plugin LMS más conocido) o <strong>Cursalia</strong> (LMS independiente, gratis y open source). Esta es la comparativa honesta.</p>

<h2>1. Precio</h2>
<table>
<thead><tr><th>Concepto</th><th>WordPress + LearnDash</th><th>Cursalia</th></tr></thead>
<tbody>
<tr><td>Software</td><td>WordPress gratis + LearnDash $199/año</td><td>Gratis (open source)</td></tr>
<tr><td>Plantilla/Tema</td><td>$50-100 (Astra Pro, Buddyboss)</td><td>Incluido o plantillas PRO opcionales ($19-49)</td></tr>
<tr><td>Hosting</td><td>$5-30/mes</td><td>$5-30/mes</td></tr>
<tr><td>Plugins extra (pagos, etc.)</td><td>$100-300/año</td><td>Incluidos o complementos opcionales</td></tr>
<tr><td><strong>Total año 1</strong></td><td><strong>~$500-800</strong></td><td><strong>~$60-200</strong></td></tr>
</tbody>
</table>

<h2>2. Facilidad para empezar</h2>
<p><strong>WordPress + LearnDash:</strong> requiere instalar WP, configurar tema, instalar LearnDash, configurar pagos con WooCommerce, integrar todo. Curva de 1-2 semanas.</p>
<p><strong>Cursalia:</strong> instalas el LMS, ya viene todo configurado: cursos, lecciones, pagos, blog. Curva de 1-2 días.</p>

<h2>3. Velocidad y rendimiento</h2>
<p><strong>WordPress:</strong> con 10+ plugins, puede ser lento. Necesita optimización constante.</p>
<p><strong>Cursalia:</strong> está construido en Laravel desde cero, sin plugins. Más rápido por defecto.</p>
<blockquote><strong>💡 Dato:</strong> Cursalia carga típicamente en 1-1.5 segundos vs 2-4 segundos de un WordPress estándar.</blockquote>

<h2>4. Personalización del diseño</h2>
<p><strong>WordPress:</strong> ganador absoluto. Miles de temas, page builders (Elementor, Divi). Total libertad visual.</p>
<p><strong>Cursalia:</strong> personalizable desde el admin (colores, logo, textos, imágenes). Tocar el diseño profundo requiere conocer Laravel/Tailwind.</p>

<h2>5. Ecosistema de plugins</h2>
<p><strong>WordPress:</strong> ganador. 60,000+ plugins gratis y de pago.</p>
<p><strong>Cursalia:</strong> ecosistema pequeño pero curado: complementos PRO oficiales que no rompen entre sí.</p>

<h2>6. Soporte y comunidad</h2>
<p><strong>WordPress:</strong> comunidad enorme global. Cualquier error tiene 50 soluciones en Google.</p>
<p><strong>Cursalia:</strong> comunidad emergente en español. Soporte directo del equipo de Cursalia (limitado pero personalizado).</p>

<h2>7. Cuándo elegir WordPress + LearnDash</h2>
<ul>
<li>Ya conoces WordPress y te sientes cómodo.</li>
<li>Necesitas un diseño muy específico que no encuentras en otros LMS.</li>
<li>Quieres acceso a plugins muy específicos (ej: gamificación super compleja).</li>
<li>Tienes presupuesto para invertir $500-1000/año.</li>
</ul>

<h2>8. Cuándo elegir Cursalia</h2>
<ul>
<li>Quieres lanzar tu academia en 1-2 días, no en semanas.</li>
<li>No tienes presupuesto para licencias anuales.</li>
<li>Tu nicho está cubierto por una plantilla PRO (repostería, EMC, detergentes, etc.).</li>
<li>Te importa la velocidad y el SEO out-of-the-box.</li>
<li>Prefieres que TODO sea editable desde un solo panel admin.</li>
</ul>
<blockquote><strong>⚠️ Sé honesto contigo:</strong> si nunca has usado WordPress y tampoco vas a contratar a alguien, Cursalia es probablemente más rápido para ti. Si ya dominas WP, quizás te aprovechas más de su ecosistema.</blockquote>

<h2>Conclusión</h2>
<p>No hay un ganador absoluto. <strong>WordPress + LearnDash</strong> gana en flexibilidad y ecosistema. <strong>Cursalia</strong> gana en velocidad de despliegue, costo y simplicidad. Elige según tu situación, no según marketing.</p>',
                'faq' => [
                    ['q' => '¿Puedo migrar después de WordPress a Cursalia?', 'a' => 'Sí, pero requiere trabajo manual de migración de cursos. Lo más eficiente es decidir bien al inicio.'],
                    ['q' => '¿Cursalia es realmente gratis para siempre?', 'a' => 'Sí. La base es open source y gratuita. Solo pagas si quieres plantillas PRO o complementos opcionales.'],
                    ['q' => '¿Y si me arrepiento?', 'a' => 'Cursalia te permite exportar tu base de datos en cualquier momento. No estás atrapado.'],
                ],
            ],

            [
                'slug' => 'plantillas-pro-cursalia-academia-en-minutos',
                'title' => 'Plantillas PRO de Cursalia: tu academia completa en minutos',
                'category' => 'noticias',
                'thumbnail' => 'blog/plantillas-pro-cursalia-academia-en-minutos.png',
                'summary' => 'Conoce las plantillas PRO de Cursalia: paquetes completos con cursos, lecciones, blog y portadas listas para tu nicho. Repostería, EMC, Detergentes y más.',
                'meta_title' => 'Plantillas PRO Cursalia · Academias completas para tu nicho',
                'meta_description' => 'Plantillas PRO de Cursalia: paquetes con cursos, lecciones, blog y portadas listos para instalar en tu LMS. Repostería, EMC médica, Detergentes y más.',
                'content' => '<p>Crear una academia desde cero toma meses. <strong>Crearla con una plantilla PRO toma 10 minutos.</strong> Hoy te presentamos qué son las plantillas PRO de Cursalia, cómo funcionan y cuáles están disponibles.</p>

<h2>1. ¿Qué es una plantilla PRO?</h2>
<p>Es un paquete completo que convierte tu LMS Cursalia en una academia profesional de un nicho específico. Incluye:</p>
<ul>
<li><strong>6-7 cursos</strong> ya estructurados con módulos y lecciones.</li>
<li><strong>60-100+ lecciones</strong> escritas profesionalmente.</li>
<li><strong>Quizzes</strong> de autoevaluación en cada curso.</li>
<li><strong>Reseñas de ejemplo</strong> que se ven reales hasta que llegan las tuyas.</li>
<li><strong>6 artículos de blog</strong> con portadas prediseñadas, optimizados para SEO.</li>
<li><strong>Portadas profesionales</strong> de productos y lecciones.</li>
<li><strong>Categoría temática</strong> creada automáticamente.</li>
</ul>

<h2>2. ¿Cómo se instala?</h2>
<p>En 2 pasos:</p>
<ol>
<li><strong>Sube el complemento PRO</strong> a tu hosting y ejecuta <code>unzip -o</code> + <code>php artisan migrate</code>. Tiempo: 5 minutos.</li>
<li><strong>Importa el contenido</strong> desde Admin → Marketplace → Importar plantilla. Marca <em>"Empezar limpio"</em> y listo.</li>
</ol>
<p>Tu academia queda con todos los cursos, lecciones, blog y portadas en su sitio.</p>

<blockquote><strong>💡 Importante:</strong> el modo "Empezar limpio" borra los cursos y blog de demo del LMS gratis. Si quieres conservar lo que tienes, NO marques esa opción.</blockquote>

<h2>3. Plantillas disponibles ahora</h2>

<h3>🧴 Detergentes Artesanales PRO — $19 USD</h3>
<p>Para quien quiere enseñar a fabricar y vender productos de limpieza. <strong>6 cursos · 114 lecciones · 6 posts de blog</strong>. Incluye detergente líquido, en polvo, lavavajillas, suavizante y más.</p>

<h3>🧁 Repostería para Vender PRO — $19 USD</h3>
<p>La plantilla más completa para reposteras emprendedoras. <strong>6 cursos · 65 lecciones · 6 posts</strong>. Cubre tortas, postres fríos, cupcakes, galletas, decoración y un curso especial de negocio (precios, WhatsApp, Instagram).</p>

<h3>🩺 EMC Profesional — $49.99 USD</h3>
<p>Para médicos y clínicas que quieren vender Educación Médica Continua. <strong>7 cursos · 71 lecciones · 6 posts</strong>. Incluye módulo legal con normativa boliviana (Ley 1178, Ley 1152, DS 23318-A). Diferencial único en español.</p>

<h2>4. ¿Y si quiero personalizar?</h2>
<p>Las plantillas son <strong>100% editables desde el admin</strong>:</p>
<ul>
<li>Cambia textos, precios, imágenes.</li>
<li>Reemplaza videos con los tuyos (por enlace).</li>
<li>Oculta cursos que no usas.</li>
<li>Añade los tuyos propios.</li>
</ul>
<p>No tocas código en ningún momento.</p>

<h2>5. Programa Fundadores activo</h2>
<p>Ahora mismo tenemos un programa especial para los primeros 20 compradores: <strong>50% de descuento</strong> con el cupón <code>FUNDADORES</code>. Disponible hasta el 16 de julio de 2026.</p>

<div class="callout callout-tip"><i class="fa-solid fa-lightbulb"></i><p><strong>Aplicar el cupón:</strong> en la página de pago, escribe el código <strong>FUNDADORES</strong> y pulsa "Aplicar". El precio bajará automáticamente.</p></div>

<h2>6. Próximas plantillas en camino</h2>
<ul>
<li>🧼 <strong>Jabones artesanales PRO</strong> — completa la línea de fabricación con Detergentes.</li>
<li>💄 <strong>Belleza y cosmética natural PRO</strong> — para el creciente nicho de "natural".</li>
<li>📱 <strong>Marketing local para PyMEs PRO</strong> — para emprendedores locales con celular.</li>
</ul>
<p>Si te suscribes a nuestra newsletter, te avisamos al lanzar cada una con cupón especial.</p>

<h2>Conclusión</h2>
<p>Si tienes claro tu nicho y este coincide con una de nuestras plantillas, <strong>te ahorras meses de trabajo</strong>. Si quieres ver el catálogo completo, visita nuestra <a href="/courses">tienda</a>.</p>',
                'faq' => [
                    ['q' => '¿Las plantillas funcionan en cualquier hosting?', 'a' => 'Sí, en cualquier hosting compartido con PHP 8.3+ y MySQL. BanaHosting, SiteGround, Hostinger, etc.'],
                    ['q' => '¿Tengo que pagar mensual?', 'a' => 'No. Es un pago único. Lo que vendas en tu academia es 100% tuyo.'],
                    ['q' => '¿Puedo personalizar el contenido?', 'a' => 'Sí, totalmente. Cambias textos, precios, imágenes y videos desde el admin sin código.'],
                ],
            ],
        ];

        foreach ($posts as $p) {
            Blog::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'admin_id' => $adminId,
                    'blog_category_id' => $getCat($p['category'], ucfirst($p['category'])),
                    'title' => $p['title'],
                    'thumbnail' => $p['thumbnail'],
                    'summary' => $p['summary'],
                    'content' => $p['content'],
                    'status' => 'published',
                    'published_at' => now(),
                    'meta_title' => $p['meta_title'] ?? null,
                    'meta_description' => $p['meta_description'] ?? null,
                    'faq' => $p['faq'] ?? null,
                ]
            );
        }
    }
}
