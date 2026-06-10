<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Lección 1 del curso del blog: las herramientas que necesitas.
 * Sigue la misma estructura editorial que la Lección 0.
 */
class CursaliaLesson01Seeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        $category = BlogCategory::firstOrCreate(
            ['slug' => 'curso-cursalia'],
            ['name' => 'Curso Cursalia', 'color' => '#10B981', 'status' => true]
        );

        $admin = Admin::first();

        $heroPath = 'blog/leccion-01-hero.svg';
        Storage::disk('public')->put($heroPath, $this->buildHeroSvg());

        Blog::updateOrCreate(
            ['slug' => 'lec-01-herramientas-laravel-windows'],
            [
                'admin_id' => $admin?->id,
                'blog_category_id' => $category->id,
                'title' => 'Las herramientas que necesitas (e instálalas en 30 minutos)',
                'thumbnail' => $heroPath,
                'summary' => 'Laragon, Node.js, Git y VS Code: las 4 herramientas que vas a usar todo el curso. Las instalas en media hora y nunca más te tocan.',
                'content' => $this->buildContent(),
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $this->command->info('  ✓ Lección 1 publicada.');
        $this->command->info('  → http://cursalia.test/blog/lec-01-herramientas-laravel-windows');
    }

    private function buildHeroSvg(): string
    {
        return <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" preserveAspectRatio="xMidYMid slice">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#3E6CF6"/>
      <stop offset="55%" stop-color="#1d4ed8"/>
      <stop offset="100%" stop-color="#1e3a8a"/>
    </linearGradient>
    <radialGradient id="r1" cx="0.85" cy="0.15" r="0.8">
      <stop offset="0%" stop-color="#10B981" stop-opacity="0.5"/>
      <stop offset="100%" stop-color="#10B981" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="r2" cx="0.1" cy="0.85" r="0.7">
      <stop offset="0%" stop-color="#FBBF24" stop-opacity="0.35"/>
      <stop offset="100%" stop-color="#FBBF24" stop-opacity="0"/>
    </radialGradient>
    <style>
      .title { font-family: 'Poppins', 'Inter', sans-serif; font-weight: 800; fill: #fff; }
      .sub   { font-family: 'Inter', sans-serif; font-weight: 500; fill: rgba(255,255,255,0.88); }
      .badge { font-family: 'Inter', sans-serif; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; fill: rgba(255,255,255,0.92); }
      .deco  { font-family: 'Poppins', sans-serif; font-weight: 800; fill: rgba(255,255,255,0.07); }
      .tool  { font-family: 'Poppins', sans-serif; fill: #fff; }
    </style>
  </defs>

  <rect width="1200" height="630" fill="url(#g)"/>
  <rect width="1200" height="630" fill="url(#r1)"/>
  <rect width="1200" height="630" fill="url(#r2)"/>

  <!-- L1 gigante decorativo de fondo -->
  <text x="-40" y="780" class="deco" font-size="640">L1</text>

  <!-- Badge -->
  <g transform="translate(75,80)">
    <rect width="280" height="38" rx="19" fill="rgba(255,255,255,0.18)"/>
    <text x="140" y="25" text-anchor="middle" class="badge" font-size="12">Curso Cursalia · Lección 1</text>
  </g>

  <!-- Título 3 líneas -->
  <text x="75" y="230" class="title" font-size="62">Las herramientas</text>
  <text x="75" y="300" class="title" font-size="62">que necesitas</text>
  <text x="75" y="370" class="title" font-size="62">(en 30 minutos)</text>

  <!-- Subtítulo -->
  <text x="75" y="450" class="sub" font-size="24">Laragon · Node.js · Git · VS Code</text>
  <text x="75" y="482" class="sub" font-size="24">Las instalas una vez y no las tocas más en todo el curso.</text>

  <!-- 4 cajas de herramientas como decoración derecha -->
  <g transform="translate(885,260)">
    <!-- Sombra -->
    <rect x="4" y="6" width="240" height="240" rx="22" fill="rgba(0,0,0,0.18)"/>
    <!-- Marco -->
    <rect width="240" height="240" rx="22" fill="rgba(255,255,255,0.13)" stroke="rgba(255,255,255,0.28)" stroke-width="2"/>

    <!-- 4 cajas de herramientas -->
    <g transform="translate(30,30)">
      <!-- Laragon -->
      <rect x="0" y="0" width="80" height="80" rx="16" fill="#10B981"/>
      <text x="40" y="52" text-anchor="middle" class="tool" font-size="32" font-weight="800">L</text>
      <!-- Node -->
      <rect x="100" y="0" width="80" height="80" rx="16" fill="#84cc16"/>
      <text x="140" y="52" text-anchor="middle" class="tool" font-size="32" font-weight="800">N</text>
      <!-- Git -->
      <rect x="0" y="100" width="80" height="80" rx="16" fill="#FB7185"/>
      <text x="40" y="152" text-anchor="middle" class="tool" font-size="32" font-weight="800">G</text>
      <!-- VS Code -->
      <rect x="100" y="100" width="80" height="80" rx="16" fill="#3E6CF6"/>
      <text x="140" y="152" text-anchor="middle" class="tool" font-size="32" font-weight="800">{ }</text>
    </g>
  </g>
</svg>
SVG;
    }

    private function buildContent(): string
    {
        return <<<'HTML'
<p>Hola otra vez. Si llegaste aquí es porque <strong>aceptaste el reto</strong> de la Lección 0. Bien. Hoy vamos a empezar a ensuciarnos las manos.</p>

<p>Antes de tocar Cursalia, necesitamos preparar tu computadora con cuatro herramientas. Las instalas <strong>una sola vez en tu vida</strong> y luego ya no piensas más en ellas. Como cuando instalas Word.</p>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Esta lección es para Windows</strong>, que es lo que usa la mayoría. Si tienes Mac o Linux, los pasos son parecidos pero las descargas son distintas. Avísanos en los comentarios y te orientamos.</p>
</div>

<h2 id="por-que-necesitas">¿Por qué necesitas estas herramientas?</h2>

<p>Cursalia, como cualquier sitio web moderno, vive de muchas piezas. Para hacerlo funcionar en tu computadora antes de subirlo a internet, necesitas:</p>

<ul>
    <li><strong>Un servidor web local</strong> que entienda PHP (Laragon te lo da en 5 minutos).</li>
    <li><strong>Una base de datos</strong> donde se guardan los cursos, alumnos, etc. (también viene en Laragon).</li>
    <li><strong>Node.js</strong> para compilar los estilos del sitio (los colores, los bordes redondeados, etc.).</li>
    <li><strong>Git</strong> para descargar Cursalia y, más adelante, controlar cambios.</li>
    <li><strong>Un editor de código</strong> donde leer/escribir esos archivos cuando quieras tocarlos.</li>
</ul>

<p>No te asustes con los nombres. Si los lees en voz alta suenan a chino, pero <strong>son solo programas que instalas con un instalador, igual que WhatsApp</strong>.</p>

<h2 id="laragon">1. Laragon · tu servidor local todo-en-uno</h2>

<p>Laragon es un programa para Windows que incluye <strong>Apache (servidor web), PHP, MySQL y Composer</strong>, todos juntos. Lo que en otros tutoriales te pedirían que instales por separado, Laragon lo hace en un solo paso.</p>

<h3 id="descarga-laragon">Descarga e instalación</h3>

<ol>
    <li>Ve a <a href="https://laragon.org/download/" target="_blank" rel="noopener">laragon.org/download</a>.</li>
    <li>Descarga la versión <strong>"Laragon - Full"</strong> (~150 MB). Te lleva un par de minutos.</li>
    <li>Doble click al instalador. Acepta los valores por defecto en TODAS las pantallas (Siguiente, Siguiente, Siguiente).</li>
    <li>Al finalizar, Laragon se abre solo.</li>
</ol>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>El consejo de oro</strong>: cuando te pregunte dónde instalarlo, déjalo en <code>C:\laragon</code>. NO lo cambies. Todo el curso asume esa ruta y te ahorrarás muchos dolores de cabeza.</p>
</div>

<h3 id="primer-arranque-laragon">Primer arranque</h3>

<p>Cuando Laragon abra, verás una ventana con varios botones y un cuadro negro grande. Pulsa el botón verde grande que dice <strong>"Iniciar todos"</strong> (Start All).</p>

<p>Si después de 10 segundos ves los puntos azules junto a "Apache" y "MySQL", ¡felicidades! Tu computadora ya es un mini-servidor web. 🎉</p>

<div class="callout callout-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <p>Si Apache te da error al iniciar, suele ser porque <strong>Skype, IIS u otro programa está usando el puerto 80</strong>. Cierra Skype y otras apps de servidor, y vuelve a probar. Si sigue, comenta abajo: te ayudamos.</p>
</div>

<h2 id="nodejs">2. Node.js · para los estilos del sitio</h2>

<p>Node.js es un programa que ejecuta JavaScript fuera del navegador. Nosotros lo necesitamos para <strong>compilar Tailwind CSS</strong> (los estilos bonitos del sitio).</p>

<ol>
    <li>Ve a <a href="https://nodejs.org/" target="_blank" rel="noopener">nodejs.org</a>.</li>
    <li>Descarga la versión <strong>LTS</strong> (la verde de la izquierda, que dice "Recomendado para la mayoría de usuarios").</li>
    <li>Ejecuta el instalador. <strong>Siguiente, siguiente, siguiente</strong>. Acepta TODOS los defaults.</li>
</ol>

<h3 id="verificar-nodejs">Verifica que funciona</h3>

<p>Abre el <strong>menú Inicio de Windows</strong>, escribe <code>cmd</code> y presiona Enter. Se abre una ventana negra. Escribe:</p>

<pre><code class="language-bash">node --version
npm --version</code></pre>

<p>Si ves algo como <code>v22.21.1</code> y <code>11.7.0</code>, perfecto. Las versiones exactas no importan mientras sean razonablemente nuevas.</p>

<h2 id="git">3. Git · para descargar Cursalia (y más cosas)</h2>

<p>Git es el sistema que usamos para "descargar" Cursalia y para mantener un historial de cambios cuando empieces a personalizarlo. No te preocupes: en este curso lo usaremos muy poco.</p>

<ol>
    <li>Ve a <a href="https://git-scm.com/download/win" target="_blank" rel="noopener">git-scm.com/download/win</a>.</li>
    <li>Se descarga solo. Ejecuta el instalador.</li>
    <li><strong>Siguiente, siguiente, siguiente</strong>. En serio, los defaults están bien para todos.</li>
</ol>

<p>Verifica que se instaló bien abriendo otra ventana negra (cmd) y escribiendo:</p>

<pre><code class="language-bash">git --version</code></pre>

<p>Debe responder algo como <code>git version 2.40.0</code>. Cualquier número arriba de 2.30 nos vale.</p>

<h3 id="configurar-git">Configura tu nombre (solo una vez)</h3>

<p>Aunque no lo uses mucho, Git necesita saber quién eres. En la misma ventana negra ejecuta estos dos comandos (cambia el nombre y el correo por los tuyos):</p>

<pre><code class="language-bash">git config --global user.name "Tu Nombre"
git config --global user.email "tu@correo.com"</code></pre>

<p>Listo. Esa configuración se queda para siempre.</p>

<h2 id="vscode">4. Visual Studio Code · tu editor</h2>

<p>VS Code es donde vas a ver y editar los archivos de Cursalia. Es como Word, pero para código. Es <strong>gratis y de Microsoft</strong>.</p>

<ol>
    <li>Ve a <a href="https://code.visualstudio.com/" target="_blank" rel="noopener">code.visualstudio.com</a>.</li>
    <li>Pulsa el botón grande <strong>"Download for Windows"</strong>.</li>
    <li>Ejecuta el instalador con los defaults.</li>
</ol>

<h3 id="extensiones-vscode">Extensiones que vas a querer</h3>

<p>Abre VS Code. En la barra lateral izquierda hay un icono que parece un Tetris (cuatro cuadrados). Pulsa ahí y busca + instala estas 4 extensiones gratis:</p>

<ul>
    <li><strong>Laravel Blade Snippets</strong> · te resalta los archivos <code>.blade.php</code>.</li>
    <li><strong>Tailwind CSS IntelliSense</strong> · autocompleta las clases de Tailwind.</li>
    <li><strong>PHP Intelephense</strong> · entiende el código PHP.</li>
    <li><strong>Material Icon Theme</strong> · pone iconos bonitos a los archivos (opcional pero queda lindo).</li>
</ul>

<div class="callout callout-tip">
    <i class="fa-solid fa-lightbulb"></i>
    <p><strong>Si te abruma</strong>, instala solo las 2 primeras. Las otras dos las puedes añadir más tarde sin problema.</p>
</div>

<h2 id="composer">5. Composer · el que gestiona las librerías de PHP</h2>

<p>¡Buenas noticias! <strong>Composer ya viene con Laragon</strong>. No tienes que instalar nada. Solo vamos a verificar que funciona.</p>

<p>En la ventana de Laragon, busca arriba el botón <strong>"Terminal"</strong> y púlsalo. Se abre otra ventana negra. Escribe:</p>

<pre><code class="language-bash">composer --version
php --version</code></pre>

<p>Verás algo como:</p>

<pre><code class="language-bash">Composer version 2.7.7
PHP 8.3.26 (cli)</code></pre>

<p>Si ves esto, ya tienes <strong>Composer y PHP funcionando</strong>. Genial.</p>

<h2 id="verificacion-final">Verificación final · ¿Todo en orden?</h2>

<p>Hagamos un repaso rápido. En la terminal de Laragon, ejecuta estos 5 comandos uno por uno:</p>

<pre><code class="language-bash">php --version       # debe decir PHP 8.3.x o superior
composer --version  # debe decir Composer 2.x
node --version      # debe decir v20+ o v22+
npm --version       # debe decir 10+
git --version       # debe decir 2.30+</code></pre>

<p>Si los 5 responden bien, <strong>ya estás listo para la Lección 2</strong>. Eres un crack. 💪</p>

<div class="callout callout-info">
    <i class="fa-solid fa-circle-info"></i>
    <p><strong>Si alguno falla</strong>: revisa que hayas instalado Laragon en <code>C:\laragon</code>, reinicia tu PC, y abre la terminal NUEVA. A veces Windows necesita un reinicio para que registre los nuevos programas.</p>
</div>

<h2 id="que-acabas-de-instalar">¿Qué acabas de instalar?</h2>

<p>Para que no te quedes con la duda:</p>

<ul>
    <li><strong>Laragon</strong>: tu mini-Internet en tu PC. Sirve páginas y guarda datos.</li>
    <li><strong>PHP + Composer</strong>: el lenguaje en que está escrito Cursalia y su gestor de librerías.</li>
    <li><strong>MySQL</strong>: la base de datos donde se guardan los cursos, alumnos, etc.</li>
    <li><strong>Node.js + npm</strong>: para compilar los estilos visuales (Tailwind CSS).</li>
    <li><strong>Git</strong>: para descargar Cursalia.</li>
    <li><strong>VS Code</strong>: el "Word para programadores".</li>
</ul>

<p>Es bastante para una sola sesión. Si te dio dolor de cabeza, <strong>respira</strong>. Lo importante es que ya está hecho. <strong>Ya no tendrás que tocar nada de esto durante el resto del curso</strong>.</p>

<hr>

<h2 id="proxima">¿Vamos por la Lección 2?</h2>

<p>En la siguiente lección vamos a <strong>descargar Cursalia y abrirla por primera vez en tu navegador</strong>. Es la sesión donde dirás "ostras, esto es de verdad mío".</p>

<div class="callout callout-tip">
    <i class="fa-solid fa-arrow-right"></i>
    <p><strong>Lección 2 — Descarga Cursalia: tu academia ya está casi lista</strong></p>
    <p>Vamos a clonar el repositorio, configurar la base de datos y ver el panel admin funcionando en menos de 20 minutos. Se publica el próximo viernes a las 9:00.</p>
</div>

<hr>

<h3 id="resumen">📚 Resumen en 5 puntos</h3>

<ol>
    <li><strong>Laragon te da PHP + MySQL + Apache + Composer todo junto</strong>. Instala en <code>C:\laragon</code> y pulsa "Iniciar todos".</li>
    <li><strong>Node.js LTS</strong> es para compilar los estilos. Acepta los defaults del instalador.</li>
    <li><strong>Git</strong> es para descargar Cursalia. Configura tu nombre y correo una sola vez.</li>
    <li><strong>VS Code</strong> es tu editor. Instala las 2 extensiones de Laravel y Tailwind para empezar.</li>
    <li><strong>Verifica todo con la terminal</strong>: <code>php --version</code>, <code>node --version</code>, <code>git --version</code>. Si responden los 3, estás listo.</li>
</ol>
HTML;
    }
}
