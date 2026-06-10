@extends('layouts.admin')

@section('title', 'Ayuda')
@section('page-title', 'Ayuda')
@section('page-subtitle', 'Monta tu academia sin tocar código, paso a paso')

@section('content')

@php
    // Atajo clicable a una sección real del panel.
    if (! function_exists('helpLink')) {
        function helpLink(string $route, string $label) {
            return '<a href="'.route($route).'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-ink-900 text-white text-xs font-semibold hover:bg-brand-600 transition no-underline whitespace-nowrap"><i class="fa-solid fa-arrow-right-long text-[10px] opacity-60"></i> '.$label.'</a>';
        }
    }
@endphp

<div class="max-w-3xl mx-auto">

    {{-- Intro --}}
    <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-7 sm:p-9 shadow-soft relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
        <span class="relative inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-[11px] font-bold uppercase tracking-wider">
            <i class="fa-solid fa-life-ring"></i> Guía para principiantes
        </span>
        <h1 class="relative font-display font-extrabold text-2xl sm:text-3xl mt-4">De cero a tu academia lista</h1>
        <p class="relative mt-3 text-brand-50 leading-relaxed max-w-xl">Vas a personalizar tu Cursalia entero —marca, portada, cursos, blog, contacto— desde este panel, <strong>sin tocar código</strong>. Si sabes rellenar un formulario, sabes hacer esto. Cada paso enlaza directo a su sección. ☕</p>
    </div>

    {{-- Índice --}}
    <div class="mt-6 rounded-2xl border border-brand-100 bg-brand-50/60 p-5 sm:p-6">
        <p class="font-display font-bold text-ink-900 mb-3"><i class="fa-solid fa-list-check text-brand-600"></i> Lo que vas a hacer</p>
        <ol class="grid sm:grid-cols-2 gap-x-8 gap-y-1.5 text-sm text-ink-700 list-decimal list-inside marker:text-brand-500 marker:font-bold">
            <li><a href="#antes" class="hover:text-brand-700">Antes de empezar</a></li>
            <li><a href="#entrar" class="hover:text-brand-700">El panel y el menú</a></li>
            <li><a href="#resumen" class="hover:text-brand-700">El Resumen</a></li>
            <li><a href="#marca" class="hover:text-brand-700">Tu marca: Apariencia</a></li>
            <li><a href="#seo" class="hover:text-brand-700">SEO básico</a></li>
            <li><a href="#menu" class="hover:text-brand-700">Menú y cabecera</a></li>
            <li><a href="#portada" class="hover:text-brand-700">La portada</a></li>
            <li><a href="#confianza" class="hover:text-brand-700">Testimonios, cifras y marcas</a></li>
            <li><a href="#contacto" class="hover:text-brand-700">Contacto y mensajes</a></li>
            <li><a href="#pie" class="hover:text-brand-700">Pie de página y redes</a></li>
            <li><a href="#legales" class="hover:text-brand-700">Páginas legales y propias</a></li>
            <li><a href="#curso" class="hover:text-brand-700">Tu primer curso</a></li>
            <li><a href="#blog" class="hover:text-brand-700">Blog y newsletter</a></li>
            <li><a href="#marketplace" class="hover:text-brand-700">Marketplace</a></li>
            <li><a href="#perfil" class="hover:text-brand-700">Tu perfil y seguridad</a></li>
            <li><a href="#fin" class="hover:text-brand-700">Tu primer día: checklist</a></li>
        </ol>
    </div>

    {{-- Contenido --}}
    <div class="mt-6 bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 sm:p-9
                [&_h2]:font-display [&_h2]:font-extrabold [&_h2]:text-xl [&_h2]:text-ink-900 [&_h2]:mt-9 [&_h2]:mb-2 [&_h2]:pt-5 [&_h2]:border-t-2 [&_h2]:border-brand-100 [&_h2:first-child]:mt-0 [&_h2:first-child]:pt-0 [&_h2:first-child]:border-0
                [&_h2_.n]:text-brand-600
                [&_h3]:font-bold [&_h3]:text-base [&_h3]:text-brand-700 [&_h3]:mt-5 [&_h3]:mb-1
                [&_p]:text-ink-700 [&_p]:leading-relaxed [&_p]:my-2.5
                [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:my-2.5 [&_ul]:text-ink-700 [&_ul]:space-y-1
                [&_ol.steps]:list-decimal [&_ol.steps]:pl-5 [&_ol.steps]:my-2.5 [&_ol.steps]:text-ink-700 [&_ol.steps]:space-y-1.5 [&_ol.steps]:marker:text-brand-500 [&_ol.steps]:marker:font-bold
                [&_strong]:text-ink-900 [&_code]:bg-brand-50 [&_code]:text-brand-700 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:rounded [&_code]:text-sm">

        <h2 id="antes"><span class="n">1.</span> Antes de empezar</h2>
        <p>Para seguir esta guía solo necesitas dos cosas:</p>
        <ul>
            <li>Tu <strong>Cursalia ya instalado</strong> (en tu ordenador o en tu hosting).</li>
            <li>Tu <strong>usuario y contraseña de administrador</strong> (ya los tienes: estás dentro 😉).</li>
        </ul>
        <p>No vas a editar ningún archivo. Todo se hace desde aquí, con clics.</p>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Consejo</span> · ten a mano el logo de tu academia y tus colores. Si aún no los tienes, no pasa nada: puedes cambiarlos cuando quieras.</div>

        <h2 id="entrar"><span class="n">2.</span> El panel y el menú</h2>
        <p>Ya estás en el panel. A la izquierda tienes el <strong>menú</strong>, organizado en grupos. Este es el mapa de toda la guía:</p>
        <ul>
            <li><strong>General</strong> → Resumen y esta Ayuda.</li>
            <li><strong>Aprendizaje</strong> → Categorías, Cursos, Autoevaluaciones.</li>
            <li><strong>Contenido</strong> → Artículos blog, Categorías blog, Comentarios, Mensajes, Página de contacto, Newsletter.</li>
            <li><strong>Páginas del sitio</strong> → Inicio, Testimonios, Cifras, Marcas.</li>
            <li><strong>Marketplace</strong> → Plantillas, Lista de espera, Servicios, Pedidos de servicios.</li>
            <li><strong>Sistema</strong> → Mi perfil, Apariencia, Navegación, Cabecera, Pie de página, Páginas.</li>
        </ul>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Truco</span> · arriba a la derecha tienes el botón <strong>“Ver sitio”</strong>: ábrelo en otra pestaña para ver tus cambios en directo mientras editas.</div>

        <h2 id="resumen"><span class="n">3.</span> El Resumen (tu tablero)</h2>
        <p>{!! helpLink('admin.dashboard', 'General › Resumen') !!} te da una foto rápida de tu academia: cuántos <strong>estudiantes</strong> e <strong>instructores</strong> tienes, cuántos <strong>cursos</strong> están activos, y tus últimos cursos y artículos.</p>
        <p>Verás bloques marcados como <strong>Fase 2</strong> (ventas, ingresos, certificados): son funciones que llegarán más adelante. Por ahora, tu versión gratuita ya te deja montar y publicar todo.</p>

        <h2 id="marca"><span class="n">4.</span> Tu marca: logo, nombre y colores</h2>
        <p><strong>Qué consigues:</strong> que el sitio muestre el nombre, el logo y los colores de <em>tu</em> academia. Es la sección estrella.</p>
        <p>Entra a {!! helpLink('admin.appearance.edit', 'Sistema › Apariencia') !!} y dentro encontrarás:</p>
        <ul>
            <li><strong>Paleta de marca</strong> — elige una paleta lista con un clic (Cursalia Green, Coral, Violet, Slate, Amber) o pulsa <strong>Personalizado</strong> y pon tus colores.</li>
            <li><strong>Identidad</strong> — nombre del sitio, eslogan, idioma, copyright y meta descripción.</li>
            <li><strong>Imágenes de marca</strong> — logo claro, logo oscuro, favicon, imagen para redes (OG) e imagen de portada.</li>
            <li><strong>Tipografía</strong> — la fuente de los títulos y la del texto.</li>
            <li><strong>Secciones del home</strong> — casillas para mostrar u ocultar cada bloque de la portada.</li>
        </ul>
        <p>Pulsa <strong>Guardar</strong> y abre tu sitio en otra pestaña: los cambios salen al instante.</p>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Consejo</span> · usa un logo con fondo transparente (PNG o SVG) para que se vea bien sobre cualquier color.</div>

        <h2 id="seo"><span class="n">5.</span> SEO básico: que Google te encuentre</h2>
        <p>En la misma {!! helpLink('admin.appearance.edit', 'Sistema › Apariencia') !!} tienes el apartado de <strong>SEO</strong>. Ahí pegas el código de <strong>Google Search Console</strong>, el de <strong>Bing</strong>, tu <strong>Google Analytics</strong> y una <strong>meta descripción</strong> general.</p>
        <p>No hace falta ser experto: solo pegar los códigos que te dan esas herramientas. A cambio, los buscadores te indexan y empiezas a recibir visitas gratis.</p>

        <h2 id="menu"><span class="n">6.</span> El menú de arriba y la cabecera</h2>
        <p><strong>Qué consigues:</strong> decidir qué enlaces aparecen en la barra superior (Inicio, Cursos, Blog, Contacto…).</p>
        <p>En {!! helpLink('admin.navigation.edit', 'Sistema › Navegación') !!} puedes <strong>añadir</strong>, <strong>quitar</strong>, <strong>renombrar</strong> y <strong>reordenar</strong> (arrastrando) los enlaces. Y en {!! helpLink('admin.header-settings.index', 'Sistema › Cabecera') !!} ajustas el buscador y el botón de categorías.</p>

        <h2 id="portada"><span class="n">7.</span> La portada (página de inicio)</h2>
        <p><strong>Qué consigues:</strong> cambiar todos los textos e imágenes de la primera página que ve tu visitante.</p>
        <p>Ve a {!! helpLink('admin.home-sections.index', 'Páginas del sitio › Inicio') !!}. Es una sola pantalla con varios bloques; cada uno se guarda por separado:</p>
        <ul>
            <li><strong>Portada (Hero)</strong> — etiqueta, título grande, frase de bienvenida, dos botones y la imagen de fondo.</li>
            <li><strong>Razones / Ventajas</strong> — hasta 4 tarjetas con icono, título y descripción.</li>
            <li><strong>Categorías</strong> y <strong>Cursos destacados</strong> — su título, subtítulo y cuántos mostrar.</li>
            <li><strong>Sobre nosotros</strong> — un bloque de presentación con texto y un botón.</li>
        </ul>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Consejo</span> · no tienes que rellenarlo todo de golpe. Cambia el título de la portada, guarda, y ve completando el resto otro día.</div>

        <h2 id="confianza"><span class="n">8.</span> Da confianza: testimonios, cifras y marcas</h2>
        <p>Tres secciones que hacen que tu academia se vea profesional:</p>
        <ul>
            <li>{!! helpLink('admin.testimonials.index', 'Testimonios') !!} — reseñas de alumnos con nombre, foto, texto y estrellas.</li>
            <li>{!! helpLink('admin.counter.index', 'Cifras') !!} — cuatro contadores tipo “1.000+ estudiantes”.</li>
            <li>{!! helpLink('admin.brands.index', 'Marcas') !!} — la fila de logos de clientes o partners.</li>
        </ul>
        <div class="rounded-xl bg-coral-50 border border-coral-200 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-coral-700">⚠️ Error común</span> · no inventes cifras ni testimonios falsos. Si aún no tienes alumnos, deja las cifras en cero: la sección se oculta sola y tu sitio se ve honesto.</div>

        <h2 id="contacto"><span class="n">9.</span> Contacto y mensajes</h2>
        <p><strong>Qué consigues:</strong> que la gente pueda escribirte y que los mensajes te lleguen.</p>
        <p>En {!! helpLink('admin.contact-settings.index', 'Contenido › Página de contacto') !!} defines los textos, el <strong>correo que recibe los mensajes</strong>, las tarjetas con tu email/teléfono, el horario y un mapa opcional. Todo lo que te escriban queda guardado en {!! helpLink('admin.messages.index', 'Contenido › Mensajes') !!}.</p>

        <h2 id="pie"><span class="n">10.</span> Pie de página y redes</h2>
        <p><strong>Qué consigues:</strong> los enlaces y redes que aparecen abajo del todo, en todas las páginas.</p>
        <p>En {!! helpLink('admin.footer.index', 'Sistema › Pie de página') !!} editas su título, el modo oscuro, las dos <strong>columnas de enlaces</strong> y tus <strong>redes sociales</strong> (Instagram, YouTube, etc.).</p>

        <h2 id="legales"><span class="n">11.</span> Páginas legales y páginas propias</h2>
        <p><strong>Qué consigues:</strong> tu política de privacidad, términos y cualquier página extra (FAQ, Ayuda…), con <em>tus</em> datos.</p>
        <p>En {!! helpLink('admin.custom-pages.index', 'Sistema › Páginas') !!} creas páginas nuevas con su título, contenido, meta descripción y eliges si aparecen en el menú.</p>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Consejo</span> · revisa la privacidad y los términos con calma: son importantes en cuanto tengas usuarios registrados.</div>

        <h2 id="curso"><span class="n">12.</span> Tu primer curso (¡lo importante!)</h2>
        <p><strong>Qué consigues:</strong> publicar contenido para tus estudiantes. El orden es siempre: primero una <strong>categoría</strong>, luego el <strong>curso</strong>, y dentro sus <strong>capítulos</strong> y <strong>lecciones</strong>.</p>
        <ol class="steps">
            <li>En {!! helpLink('admin.course-categories.index', 'Aprendizaje › Categorías') !!} crea al menos una (ej. “Marketing”, “Cocina”…).</li>
            <li>En {!! helpLink('admin.courses.index', 'Aprendizaje › Cursos') !!} pulsa <strong>Nuevo curso</strong>: título, descripción, categoría, nivel e imagen. Déjalo en <strong>Activo</strong> para que se vea.</li>
            <li>Al guardar, entra a <strong>Contenido</strong> del curso y crea sus <strong>capítulos</strong> (ej. “Módulo 1”).</li>
            <li>Dentro de cada capítulo añade <strong>lecciones</strong>: <strong>vídeo</strong> (YouTube/Vimeo), <strong>texto</strong> o <strong>archivo</strong> descargable (PDF/ZIP).</li>
        </ol>
        <p>En {!! helpLink('admin.quizzes.index', 'Aprendizaje › Autoevaluaciones') !!} puedes crear un test por lección (opción múltiple o verdadero/falso), con nota mínima y reintentos.</p>
        <div class="rounded-xl bg-coral-50 border border-coral-200 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-coral-700">⚠️ Error común</span> · si creas un curso y no aparece en la web, casi siempre es porque quedó como <strong>borrador</strong>. Edítalo y ponlo en <strong>Activo</strong>.</div>

        <h2 id="blog"><span class="n">13.</span> Blog, comentarios y newsletter</h2>
        <p><strong>Qué consigues:</strong> atraer visitas con artículos y reunir una lista de correos.</p>
        <ul>
            <li>Escribe artículos en {!! helpLink('admin.blogs.index', 'Artículos blog') !!} y organízalos con {!! helpLink('admin.blog-categories.index', 'Categorías blog') !!}.</li>
            <li>Modera lo que comentan en {!! helpLink('admin.blog-comments.index', 'Comentarios') !!}: aprueba los buenos y elimina el spam.</li>
            <li>Quien se suscriba aparece en {!! helpLink('admin.newsletter.index', 'Newsletter') !!}, donde además puedes enviarles un correo a todos.</li>
        </ul>

        <h2 id="marketplace"><span class="n">14.</span> Marketplace: plantillas y servicios</h2>
        <p>Tu Cursalia trae un pequeño <strong>marketplace</strong> para enseñar lo que ofreces:</p>
        <ul>
            <li>{!! helpLink('admin.templates.index', 'Plantillas') !!} — un catálogo de plantillas o recursos, con su lista de espera de interesados.</li>
            <li>{!! helpLink('admin.services.index', 'Servicios') !!} — planes que ofreces (consultoría, montaje…); las solicitudes te llegan a Pedidos de servicios.</li>
        </ul>
        <div class="rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-brand-700">💡 Nota</span> · el cobro automático llega más adelante. Por ahora estas secciones te sirven de escaparate y para recoger interesados.</div>

        <h2 id="perfil"><span class="n">15.</span> Tu perfil y seguridad</h2>
        <p>En {!! helpLink('admin.profile', 'Sistema › Mi perfil') !!} cambias tu nombre, tu email y, sobre todo, tu <strong>contraseña</strong>.</p>
        <div class="rounded-xl bg-coral-50 border border-coral-200 px-4 py-3 my-3 text-sm text-ink-700"><span class="font-bold text-coral-700">🔒 Importante</span> · si instalaste con una contraseña de ejemplo, cámbiala ahora mismo por una larga y única. Tu panel controla todo tu sitio: protégelo bien.</div>

        <h2 id="fin"><span class="n">16.</span> Tu primer día: checklist</h2>
        <p>Un buen orden para empezar hoy:</p>
        <ol class="steps">
            <li>Marca: logo y colores (Apariencia).</li>
            <li>Portada y “Sobre nosotros”.</li>
            <li>Contacto y pie de página.</li>
            <li>Tu primer curso con un par de lecciones.</li>
            <li>Cambia tu contraseña de admin.</li>
        </ol>
        <p>Y recuerda: <strong>nada de lo que hagas aquí es definitivo</strong>. Todo se puede volver a cambiar cuando quieras, sin miedo y sin código. 🚀</p>

    </div>

    {{-- CTA final --}}
    <div class="mt-6 rounded-2xl bg-brand-50 border border-brand-100 p-6 text-center">
        <p class="font-display font-extrabold text-lg text-ink-900">¿Lista tu academia?</p>
        <p class="text-ink-600 text-sm mt-1">Empieza por tu marca y tu primer curso. El resto se completa con el tiempo.</p>
        <a href="{{ route('admin.appearance.edit') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-full bg-brand-600 text-white font-bold text-sm hover:bg-brand-700 transition">
            <i class="fa-solid fa-paint-roller"></i> Empezar por la Apariencia
        </a>
    </div>

</div>
@endsection
