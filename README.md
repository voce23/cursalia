<h1 align="center">Cursalia LMS</h1>

<p align="center">
  <strong>El LMS gratuito y de código abierto para crear tu academia online en tu propio dominio.</strong><br>
  Sin mensualidades · Sin comisiones · 100&nbsp;% tuyo.
</p>

<p align="center">
  <a href="https://cursalia.org">cursalia.org</a> ·
  <a href="https://github.com/voce23/cursalia">GitHub</a>
</p>

---

## ¿Qué es Cursalia?

Cursalia es un **sistema de gestión de aprendizaje (LMS)** hecho con **Laravel 13**. Te permite crear, organizar y publicar cursos online en tu propio servidor y con tu propia marca. Está pensado para profesores, academias y empresas que quieren su plataforma de formación **sin pagar mensualidades ni ceder comisiones** a plataformas de terceros.

### Funciones principales
- 🎓 **Cursos con capítulos y lecciones** (vídeo, texto y recursos).
- 👩‍🎓 **Panel de estudiante**: cursos inscritos, reproductor con progreso y autoevaluaciones.
- 👨‍🏫 **Panel de instructor** y panel de **administración**.
- 📝 **Blog** integrado con SEO (Schema.org, sitemap, Open Graph).
- 🌐 **Páginas de marketing**: inicio, cursos, servicios, plantillas, contacto, legales.
- 🔒 **Seguridad**: captcha, rate-limiting, CSRF, cabeceras de seguridad, cookies RGPD.
- 🎨 **Personalizable**: marca, colores, navegación y textos editables.

> La versión **gratuita (FREE)** entrega cursos sin coste. Las funciones de negocio (pagos, certificados, gamificación, marketplace…) llegan con **Cursalia PRO** — próximamente.

---

## Requisitos

- **PHP 8.3+** (extensiones: `gd`, `mbstring`, `pdo_mysql`, `intl`, `bcmath`, `fileinfo`, `openssl`, `tokenizer`, `xml`, `curl`, `zip`)
- **Composer 2**
- **MySQL 8** (o MariaDB) — o SQLite para pruebas
- **Node.js 18+** y **npm** (para compilar los assets)

---

## Instalación rápida (local)

```bash
# 1. Clonar el proyecto
git clone https://github.com/voce23/cursalia.git
cd cursalia

# 2. Dependencias
composer install
npm install

# 3. Configuración
cp .env.example .env
php artisan key:generate

# 4. Edita el .env con tus datos de base de datos:
#    DB_DATABASE=cursalia   DB_USERNAME=root   DB_PASSWORD=

# 5. Crea las tablas + datos de ejemplo (admin, cursos demo, blog)
php artisan migrate --seed

# 6. Compila los assets y enlaza el almacenamiento
npm run build
php artisan storage:link

# 7. Arranca
php artisan serve
```

Abre **http://127.0.0.1:8000** (o configura el host en Laragon/Valet).

> ¿Eres principiante? Tienes tutoriales paso a paso en el blog de **[cursalia.org](https://cursalia.org/blog)**: instalación en local con Laragon e instalación en hosting (cPanel).

---

## Acceso al administrador

Tras `migrate --seed` se crea un usuario administrador de ejemplo.

⚠️ **Cambia la contraseña inmediatamente** con:

```bash
php artisan admin:password
```

El panel está en **/admin** (o inicia sesión en **/login** con el usuario admin).

---

## Puesta en producción (resumen)

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force --seed
php artisan admin:password          # contraseña FUERTE
npm run build
php artisan storage:link
php artisan optimize                # cachea config, rutas y vistas
```

En el `.env` de producción: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://tu-dominio`, HTTPS activo, SMTP real y permisos de escritura en `storage/`, `bootstrap/cache/` y `public/uploads/`.

---

## Licencia y responsabilidad

Software **de código abierto bajo licencia MIT**. Se entrega **"tal cual" (as-is), sin garantías**. El autor no se hace responsable del mal uso, pérdida de datos, caídas ni daños derivados de su uso. Lo usas **bajo tu propia responsabilidad**.

---

<p align="center">Hecho con ❤️ y Laravel · <a href="https://cursalia.org">cursalia.org</a></p>
