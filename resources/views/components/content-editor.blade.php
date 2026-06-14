@props([
    'name',
    'value' => '',
    'rows' => 6,
    'placeholder' => 'Escribe el contenido en HTML. Usa los botones de arriba para insertar elementos rápido…',
])

{{--
    Editor de contenido enriquecido (estilo WordPress) reutilizable.
    Barra de snippets (títulos, listas, citas, callouts, código, tabla, imagen)
    + textarea HTML + contador. El HTML se renderiza en el frontend dentro de
    .article-prose (mismo estilo que el blog), así que callouts y código salen
    con formato. Se usa en el blog y en la descripción de las lecciones.
--}}
<div x-data="cursaliaContentEditor('{{ route('admin.editor.image') }}')">
    {{-- Toolbar de snippets --}}
    <div class="rounded-2xl bg-cream-2 border border-ink-200 p-3 mb-2">
        <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Insertar elemento
        </p>
        <div class="flex flex-wrap gap-1.5">
            @php
                $snippets = [
                    ['Título H2', 'fa-heading', 'h2'],
                    ['Subtítulo H3', 'fa-heading', 'h3'],
                    ['Párrafo', 'fa-paragraph', 'p'],
                    ['Lista •', 'fa-list-ul', 'ul'],
                    ['Lista 1.', 'fa-list-ol', 'ol'],
                    ['Cita', 'fa-quote-left', 'quote'],
                    ['Separador', 'fa-minus', 'hr'],
                    ['💡 Tip', 'fa-lightbulb', 'tip'],
                    ['ℹ️ Info', 'fa-circle-info', 'info'],
                    ['⚠️ Aviso', 'fa-triangle-exclamation', 'warning'],
                    ['🚨 Peligro', 'fa-circle-exclamation', 'danger'],
                    ['💬 Cita big', 'fa-quote-right', 'quote-callout'],
                    ['🎯 Aprender', 'fa-bullseye', 'learn'],
                    ['Código PHP', 'fa-code', 'code-php'],
                    ['Código JS', 'fa-code', 'code-js'],
                    ['Código Bash', 'fa-terminal', 'code-bash'],
                    ['Código HTML', 'fa-code', 'code-html'],
                    ['Tabla', 'fa-table', 'table'],
                    ['Imagen', 'fa-image', 'img'],
                ];
            @endphp
            @foreach ($snippets as [$label, $icon, $key])
                <button type="button" @click="insert('{{ $key }}')"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white border border-ink-200 hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700 transition text-xs font-semibold text-ink-700">
                    <i class="fa-solid {{ $icon }} text-[10px]"></i> {{ $label }}
                </button>
            @endforeach
        </div>
        <p class="text-[10px] text-ink-400 mt-2 flex items-center gap-1.5">
            <i class="fa-solid fa-circle-info"></i>
            Pon el cursor donde quieras insertar y haz clic, o escribe HTML a mano. <span x-text="wordCount"></span> palabras.
        </p>
    </div>

    <textarea name="{{ $name }}" rows="{{ $rows }}" x-ref="editor" @input="update()" spellcheck="false"
        placeholder="{{ $placeholder }}"
        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono leading-relaxed resize-y transition"
        style="min-height: 200px; tab-size: 4;">{{ $value }}</textarea>
    <input type="file" accept="image/*" x-ref="imgUpload" @change="onImageSelected($event)" class="hidden">
</div>

@once
<script>
// Snippets HTML del editor enriquecido (compartido por blog y lecciones).
const CURSALIA_SNIPPETS = {
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
    'code-bash': `\n<pre><code class="language-bash">composer install --no-dev\nphp artisan migrate --force</code></pre>\n`,
    'code-html': `\n<pre><code class="language-markup">&lt;div class="ejemplo"&gt;\n    &lt;p&gt;Texto&lt;/p&gt;\n&lt;/div&gt;</code></pre>\n`,
    table: `\n<table>\n    <thead>\n        <tr><th>Columna A</th><th>Columna B</th></tr>\n    </thead>\n    <tbody>\n        <tr><td>Dato 1</td><td>Dato 2</td></tr>\n        <tr><td>Dato 3</td><td>Dato 4</td></tr>\n    </tbody>\n</table>\n`,
    img:   `\n<img src="/storage/tu-imagen.png" alt="Descripción de la imagen">\n`,
};

function cursaliaContentEditor(uploadUrl) {
    return {
        uploadUrl: uploadUrl || '',
        wordCount: 0,
        init() { this.update(); },
        update() {
            const text = this.$refs.editor ? this.$refs.editor.value : '';
            const plain = text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
            this.wordCount = plain ? plain.split(' ').length : 0;
        },
        insertText(snippet) {
            const editor = this.$refs.editor;
            const start = editor.selectionStart, end = editor.selectionEnd;
            editor.value = editor.value.slice(0, start) + snippet + editor.value.slice(end);
            const newPos = start + snippet.length;
            editor.focus();
            editor.setSelectionRange(newPos, newPos);
            this.update();
        },
        insert(key) {
            if (key === 'img') { this.uploadImage(); return; }   // el botón Imagen SUBE un archivo
            const snippet = CURSALIA_SNIPPETS[key];
            if (snippet) this.insertText(snippet);
        },
        uploadImage() {
            const input = this.$refs.imgUpload;
            if (input) { input.value = ''; input.click(); }
        },
        onImageSelected(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            if (!this.uploadUrl) { alert('La subida de imágenes no está disponible aquí.'); return; }
            const self = this;
            const fd = new FormData();
            fd.append('image', file);
            const meta = document.querySelector('meta[name="csrf-token"]');
            const token = meta ? meta.getAttribute('content') : '';
            const flag = '\n<!-- subiendo imagen... -->\n';
            self.insertText(flag);
            fetch(this.uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: fd,
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                self.$refs.editor.value = self.$refs.editor.value.replace(flag, '');
                if (res.ok && res.d && res.d.url) {
                    self.insertText('\n<img src="' + res.d.url + '" alt="">\n');
                } else {
                    alert((res.d && res.d.message) || 'No se pudo subir la imagen.');
                    self.update();
                }
            })
            .catch(function () {
                self.$refs.editor.value = self.$refs.editor.value.replace(flag, '');
                alert('Error al subir la imagen. Revisa tu conexión e inténtalo de nuevo.');
                self.update();
            });
        },
    };
}
</script>
@endonce
