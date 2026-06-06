@extends('layouts.admin')

@section('title', $blog->exists ? 'Editar artículo' : 'Nuevo artículo')
@section('page-title', $blog->exists ? 'Editar artículo' : 'Nuevo artículo')
@section('page-subtitle', $blog->exists ? $blog->title : 'Una lección o entrada del blog')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.blogs.index') }}" class="hover:text-brand-700">Artículos</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $blog->exists ? $blog->title : 'Nuevo' }}</span>
</nav>

<form method="POST"
      action="{{ $blog->exists ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}"
      enctype="multipart/form-data"
      class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    @csrf
    @if ($blog->exists)@method('PUT')@endif

    {{-- Columna izquierda · contenido --}}
    <div class="space-y-6">

        {{-- Título y slug --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título del artículo</label>
                <input type="text" name="title" value="{{ old('title', $blog->title) }}" required maxlength="255" autofocus
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-lg font-display font-bold">
                @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Slug (URL)</label>
                <div class="flex items-center gap-2 bg-cream-2 border border-ink-200 rounded-2xl px-4 py-3">
                    <span class="text-xs text-ink-500 font-mono">/blog/</span>
                    <input type="text" name="slug" value="{{ old('slug', $blog->slug) }}" maxlength="255" placeholder="se-genera-del-titulo-si-vacio"
                        class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm font-mono">
                </div>
                <p class="text-xs text-ink-400 mt-1.5">Para las lecciones del curso usa: <code class="bg-cream-2 px-1.5 py-0.5 rounded">lec-XX-titulo-de-la-leccion</code></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Resumen <span class="text-ink-400 font-normal">(aparece en /blog y en la búsqueda)</span></label>
                <textarea name="summary" rows="2" maxlength="500"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-none">{{ old('summary', $blog->summary) }}</textarea>
            </div>
        </section>

        {{-- Editor de contenido con BOTONES DE SNIPPETS --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8"
                 x-data="contentEditor()">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-nib text-brand-600"></i> Contenido (HTML)
                </h2>
                <span class="text-xs text-ink-400">
                    <span x-text="charCount"></span> caracteres · <span x-text="wordCount"></span> palabras · ~<span x-text="readingMinutes"></span> min lectura
                </span>
            </div>

            {{-- Toolbar: botones de snippets --}}
            <div class="rounded-2xl bg-cream-2 border border-ink-200 p-3 mb-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Insertar elemento
                </p>
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $snippets = [
                            // [label, icon, color, key]
                            ['Título H2',    'fa-heading',          'brand', 'h2'],
                            ['Subtítulo H3', 'fa-heading',          'brand', 'h3'],
                            ['Párrafo',      'fa-paragraph',        'ink',   'p'],
                            ['Lista •',      'fa-list-ul',          'ink',   'ul'],
                            ['Lista 1.',     'fa-list-ol',          'ink',   'ol'],
                            ['Cita',         'fa-quote-left',       'brand', 'quote'],
                            ['Separador',    'fa-minus',            'ink',   'hr'],
                            ['💡 Tip',       'fa-lightbulb',        'brand', 'tip'],
                            ['ℹ️ Info',     'fa-circle-info',      'brand', 'info'],
                            ['⚠️ Aviso',    'fa-triangle-exclamation', 'sun', 'warning'],
                            ['🚨 Peligro',   'fa-circle-exclamation','coral','danger'],
                            ['💬 Cita big',  'fa-quote-right',      'ink',   'quote-callout'],
                            ['🎯 Aprender',  'fa-bullseye',         'brand', 'learn'],
                            ['Código PHP',   'fa-code',             'coral', 'code-php'],
                            ['Código JS',    'fa-code',             'sun',   'code-js'],
                            ['Código Bash',  'fa-terminal',         'ink',   'code-bash'],
                            ['Código HTML',  'fa-code',             'coral', 'code-html'],
                            ['Tabla',        'fa-table',            'ink',   'table'],
                            ['Imagen',       'fa-image',            'ink',   'img'],
                        ];
                    @endphp
                    @foreach ($snippets as [$label, $icon, $color, $key])
                        <button type="button" @click="insert('{{ $key }}')"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white border border-ink-200 hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700 transition text-xs font-semibold text-ink-700">
                            <i class="fa-solid {{ $icon }} text-[10px]"></i> {{ $label }}
                        </button>
                    @endforeach
                </div>
                <p class="text-[10px] text-ink-400 mt-2.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i>
                    Posiciona el cursor donde quieras insertar y haz clic. También puedes escribir HTML a mano.
                </p>
            </div>

            <textarea name="content" required rows="22" x-ref="editor" @input="update()" spellcheck="false"
                class="w-full px-5 py-4 rounded-2xl bg-brand-50/40 text-ink-900 border-2 border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-100 focus:border-brand-400 focus:bg-white text-sm font-mono leading-relaxed resize-y transition placeholder-ink-400" style="min-height: 500px; tab-size: 4;" placeholder="Escribe tu lección en HTML. Usa los botones de arriba para insertar elementos rápido…">{{ old('content', $blog->content) }}</textarea>
            @error('content')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror

            <details class="mt-4">
                <summary class="cursor-pointer text-xs font-semibold text-brand-700 hover:text-brand-600 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-book-open"></i> Ver referencia rápida de snippets
                </summary>
                <div class="mt-3 p-4 rounded-2xl bg-cream-2 border border-ink-200 text-xs text-ink-700 space-y-2">
                    <p><strong>Callouts disponibles:</strong> info (azul), tip (verde), warning (amarillo), danger (rojo), quote (oscuro), learn-box (verde con checks).</p>
                    <p><strong>Bloques de código con highlight:</strong> usa <code>&lt;pre&gt;&lt;code class="language-XXX"&gt;…&lt;/code&gt;&lt;/pre&gt;</code> con php, javascript, bash, html, css, json.</p>
                    <p><strong>Tabla de contenidos:</strong> los <code>&lt;h2&gt;</code> y <code>&lt;h3&gt;</code> se añaden automáticamente con anchor ID.</p>
                </div>
            </details>
        </section>

        {{-- ════════════════ FAQ Schema (rich snippet de preguntas) ════════════════ --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8"
                 x-data="faqBuilder({{ Js::from(old('faq', $blog->faq) ?: []) }})">
            <div class="flex items-center justify-between gap-3 mb-2">
                <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-brand-600"></i> Preguntas frecuentes (FAQ)
                </h2>
                <span class="text-xs text-ink-500" x-text="items.length + ' pregunta' + (items.length !== 1 ? 's' : '')"></span>
            </div>
            <p class="text-xs text-ink-500 mb-4 leading-relaxed">
                Google muestra estas preguntas como <strong>rich snippet desplegable</strong> en los resultados de búsqueda (Schema.org <code class="bg-cream-2 px-1.5 py-0.5 rounded">FAQPage</code>).
                Triplica el espacio que ocupas en la SERP. Recomendado: 3–6 preguntas reales que tu lector tiene.
            </p>

            <template x-for="(item, idx) in items" :key="idx">
                <div class="rounded-2xl bg-cream-2/60 border border-ink-200 p-4 mb-3 space-y-2 relative">
                    <button type="button" @click="remove(idx)" class="absolute top-3 right-3 grid place-items-center w-7 h-7 rounded-full bg-white border border-ink-200 hover:bg-coral-50 hover:text-coral-600 text-ink-500 transition" title="Eliminar pregunta">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-ink-400">Pregunta <span x-text="idx + 1"></span></label>
                        <input type="text" :name="`faq[${idx}][q]`" x-model="item.q" maxlength="255" placeholder="Ej: ¿Necesito saber programar para usar Cursalia?"
                               class="w-full mt-1 px-3 py-2 rounded-xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm font-semibold">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-ink-400">Respuesta</label>
                        <textarea :name="`faq[${idx}][a]`" x-model="item.a" rows="3" maxlength="1500" placeholder="Ej: No. Las primeras 13 lecciones se hacen sin código, copiando y pegando los comandos."
                                  class="w-full mt-1 px-3 py-2 rounded-xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm resize-y leading-relaxed"></textarea>
                    </div>
                </div>
            </template>

            <button type="button" @click="add()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold text-sm border-2 border-dashed border-brand-200 transition">
                <i class="fa-solid fa-plus"></i> Añadir pregunta
            </button>
            <p x-show="items.length === 0" class="text-center text-xs text-ink-400 mt-3">Sin preguntas todavía. Empieza añadiendo la duda más común de tu lector.</p>
        </section>
    </div>

    {{-- Columna derecha · sidebar --}}
    <aside class="space-y-5 lg:sticky lg:top-24">

        {{-- Estado y categoría --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Estado</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <option value="draft"     @selected(old('status', $blog->status) === 'draft')>Borrador (no público)</option>
                    <option value="published" @selected(old('status', $blog->status) === 'published')>Publicado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Categoría</label>
                <select name="blog_category_id" required class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <option value="">— Selecciona —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('blog_category_id', $blog->blog_category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('blog_category_id')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-[10px] text-ink-400 mt-2"><a href="{{ route('admin.blog-categories.create') }}" target="_blank" class="text-brand-700 hover:text-brand-600">+ Crear categoría</a></p>
            </div>
        </section>

        {{-- Thumbnail --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
            <h3 class="font-display font-bold text-ink-900 text-sm mb-3">Imagen destacada</h3>
            @if ($blog->thumbnail)
                <img src="{{ asset('storage/'.$blog->thumbnail) }}" alt="" class="w-full aspect-[1200/630] rounded-xl object-cover border border-ink-200 mb-3">
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full text-xs text-ink-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
            <p class="text-[10px] text-ink-400 mt-2">JPG, PNG, WEBP o SVG · Recomendado 1200×630</p>
            @error('thumbnail')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </section>

        {{-- ════════════════ SEO Google ════════════════ --}}
        <section class="bg-gradient-to-br from-brand-50/70 via-white to-cream border border-brand-200 rounded-3xl shadow-soft p-5"
                 x-data="{
                     mt: @js(old('meta_title', $blog->meta_title)),
                     md: @js(old('meta_description', $blog->meta_description)),
                     fallbackT: @js($blog->title),
                     fallbackD: @js($blog->summary),
                 }">
            <div class="flex items-center gap-2 mb-3">
                <span class="grid place-items-center w-8 h-8 rounded-xl bg-brand-600 text-white shrink-0">
                    <i class="fa-brands fa-google text-xs"></i>
                </span>
                <div>
                    <h3 class="font-display font-bold text-ink-900 text-sm">SEO · Google</h3>
                    <p class="text-[10px] text-ink-500">Cómo se ve este post en los resultados</p>
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-ink-700">Meta título <span class="text-ink-400 font-normal">(50–60 char)</span></label>
                        <span class="text-[10px] font-mono" :class="(mt?.length || 0) > 60 ? 'text-coral-600' : (mt?.length || 0) > 40 ? 'text-brand-600' : 'text-ink-400'">
                            <span x-text="mt?.length || 0"></span>/60
                        </span>
                    </div>
                    <input type="text" name="meta_title" x-model="mt" maxlength="70"
                           :placeholder="fallbackT || 'Si vacío usa el título normal'"
                           class="w-full px-3 py-2 rounded-xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-ink-700">Meta descripción <span class="text-ink-400 font-normal">(140–155 char)</span></label>
                        <span class="text-[10px] font-mono" :class="(md?.length || 0) > 160 ? 'text-coral-600' : (md?.length || 0) > 120 ? 'text-brand-600' : 'text-ink-400'">
                            <span x-text="md?.length || 0"></span>/155
                        </span>
                    </div>
                    <textarea name="meta_description" x-model="md" maxlength="180" rows="3"
                              :placeholder="fallbackD || 'Si vacío usa el resumen del post'"
                              class="w-full px-3 py-2 rounded-xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm resize-none leading-relaxed"></textarea>
                </div>

                {{-- Preview tipo SERP --}}
                <div class="bg-white rounded-2xl border border-ink-200/70 p-3 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400 mb-1">Vista previa en Google</p>
                    <p class="text-[11px] text-ink-500 truncate" x-text="`{{ url('/') }}/blog/{{ $blog->slug ?: 'tu-slug' }}`"></p>
                    <p class="text-[13px] text-blue-700 font-medium leading-tight" x-text="(mt || fallbackT || 'Título de tu post').slice(0, 60)"></p>
                    <p class="text-[11px] text-ink-700 leading-snug" x-text="(md || fallbackD || 'Aquí saldría tu descripción en los resultados de búsqueda…').slice(0, 155)"></p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-ink-700 block mb-1">Imagen para redes (OG)</label>
                    @if ($blog->og_image_custom)
                        <img src="{{ asset('storage/'.$blog->og_image_custom) }}" alt="" class="w-full aspect-[1200/630] rounded-xl object-cover border border-ink-200 mb-2">
                    @endif
                    <input type="file" name="og_image_custom" accept="image/*"
                           class="w-full text-xs text-ink-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
                    <p class="text-[10px] text-ink-400 mt-1">Opcional. Si vacía usa la imagen destacada del post.</p>
                </div>
            </div>
        </section>

        {{-- Acciones --}}
        <div class="space-y-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> {{ $blog->exists ? 'Guardar cambios' : 'Crear artículo' }}
            </button>
            <a href="{{ route('admin.blogs.index') }}" class="block text-center px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">
                Cancelar
            </a>
        </div>
    </aside>
</form>

<script>
// ─── Snippets HTML listos para insertar ───────────────────────────────────
const BLOG_SNIPPETS = {
    h2:    `\n<h2>Título de sección</h2>\n`,
    h3:    `\n<h3>Subtítulo</h3>\n`,
    p:     `\n<p>Párrafo de texto.</p>\n`,
    ul:    `\n<ul>\n    <li>Primer ítem</li>\n    <li>Segundo ítem</li>\n    <li>Tercer ítem</li>\n</ul>\n`,
    ol:    `\n<ol>\n    <li>Primer paso</li>\n    <li>Segundo paso</li>\n    <li>Tercer paso</li>\n</ol>\n`,
    quote: `\n<blockquote>\n    <p>Cita destacada.</p>\n</blockquote>\n`,
    hr:    `\n<hr>\n`,

    tip:           `\n<div class="callout callout-tip">\n    <i class="fa-solid fa-lightbulb"></i>\n    <p><strong>Tip:</strong> aquí va el consejo útil.</p>\n</div>\n`,
    info:          `\n<div class="callout callout-info">\n    <i class="fa-solid fa-circle-info"></i>\n    <p><strong>Info:</strong> aclaración o dato extra.</p>\n</div>\n`,
    warning:       `\n<div class="callout callout-warning">\n    <i class="fa-solid fa-triangle-exclamation"></i>\n    <p><strong>Cuidado:</strong> aquí va la advertencia.</p>\n</div>\n`,
    danger:        `\n<div class="callout callout-danger">\n    <i class="fa-solid fa-circle-exclamation"></i>\n    <p><strong>Error común:</strong> esto suele fallar.</p>\n</div>\n`,
    'quote-callout': `\n<div class="callout callout-quote">\n    <i class="fa-solid fa-quote-left"></i>\n    <p><strong>Cita destacada</strong> con fondo oscuro y estilo editorial.</p>\n</div>\n`,
    learn:         `\n<div class="learn-box">\n    <p><i class="fa-solid fa-bullseye"></i> Lo que vas a aprender</p>\n    <ul>\n        <li>Primer aprendizaje</li>\n        <li>Segundo aprendizaje</li>\n        <li>Tercer aprendizaje</li>\n    </ul>\n</div>\n`,

    'code-php':  `\n<pre><code class="language-php">// Tu código PHP\npublic function ejemplo()\n{\n    return view('home');\n}</code></pre>\n`,
    'code-js':   `\n<pre><code class="language-javascript">// Tu código JavaScript\nconst saludo = (nombre) => \`Hola \${nombre}\`;\nconsole.log(saludo('mundo'));</code></pre>\n`,
    'code-bash': `\n<pre><code class="language-bash">composer install --no-dev\nphp artisan migrate --force\nphp artisan storage:link</code></pre>\n`,
    'code-html': `\n<pre><code class="language-markup">&lt;div class="ejemplo"&gt;\n    &lt;p&gt;Texto&lt;/p&gt;\n&lt;/div&gt;</code></pre>\n`,

    table: `\n<table>\n    <thead>\n        <tr><th>Columna A</th><th>Columna B</th></tr>\n    </thead>\n    <tbody>\n        <tr><td>Dato 1</td><td>Dato 2</td></tr>\n        <tr><td>Dato 3</td><td>Dato 4</td></tr>\n    </tbody>\n</table>\n`,

    img:   `\n<img src="/storage/blog/tu-imagen.png" alt="Descripción de la imagen">\n`,
};

// ─── FAQ builder (Schema.org FAQPage) ─────────────────────────────────────
function faqBuilder(initial) {
    return {
        items: Array.isArray(initial) ? initial.filter(i => i && (i.q || i.a)) : [],
        add() {
            this.items.push({ q: '', a: '' });
        },
        remove(idx) {
            this.items.splice(idx, 1);
        },
    };
}

function contentEditor() {
    return {
        charCount: 0,
        wordCount: 0,
        readingMinutes: 1,

        init() {
            this.update();
        },

        update() {
            const text = this.$refs.editor.value;
            const plain = text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
            this.charCount = text.length;
            this.wordCount = plain ? plain.split(' ').length : 0;
            this.readingMinutes = Math.max(1, Math.ceil(this.wordCount / 200));
        },

        insert(key) {
            const snippet = BLOG_SNIPPETS[key];
            if (! snippet) return;
            const editor = this.$refs.editor;
            const start = editor.selectionStart;
            const end   = editor.selectionEnd;
            const before = editor.value.slice(0, start);
            const after  = editor.value.slice(end);
            editor.value = before + snippet + after;
            // Posicionar cursor justo después del snippet
            const newPos = start + snippet.length;
            editor.focus();
            editor.setSelectionRange(newPos, newPos);
            this.update();
        },
    };
}
</script>

@endsection
