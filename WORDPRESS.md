# FIET · Teléfono ACT — versión WordPress

Rama de trabajo: **`web-wp`**. La web estática vive en `main`.

## Idea general

- **El repositorio es la fuente de verdad del TEMA** (`wp-content/themes/fiet-act/`): diseño, animaciones (canvas/scroll), CSS y JS.
- **WordPress gestiona solo el CONTENIDO** (textos, imágenes, teléfono, enlaces), que se editará desde el admin.
- El núcleo de WordPress, la base de datos y el contenido subido **no se versionan** (ver `.gitignore`).

## 1. Requisitos de hosting

WordPress necesita **PHP + MySQL/MariaDB** (no corre como estático). Cualquier hosting WordPress estándar sirve:

- **PHP** 8.0+ (recomendado 8.2/8.3)
- **MySQL** 5.7+ o **MariaDB** 10.4+
- Apache o Nginx
- HTTPS

## 2. Desarrollo local (Docker)

Con Docker instalado:

```bash
docker compose up -d      # levanta WordPress + base de datos
# abre http://localhost:8080  y completa la instalación de WordPress
```

El `docker-compose.yml` monta `wp-content/themes/fiet-act` dentro de WordPress.
Tras instalar: **Apariencia → Temas → activar "FIET · Teléfono ACT"**.

Para parar / reiniciar:

```bash
docker compose down       # conserva datos (volúmenes)
docker compose down -v    # borra también la base de datos
```

## 3. Rendimiento / assets

- `main.js` ya **no** lleva imágenes incrustadas (pasó de ~4 MB a ~64 KB).
  Los assets del canvas se extrajeron a archivos: `video1.jpg`, `video2.webp`,
  `spain.png`, `comp.webp` (en el tema).
- El JS resuelve esas imágenes con `window.FIET_ASSETS` (la URL del tema),
  que el tema define en `functions.php`. En estático usa rutas relativas.
- `styles.css` y los JS (`main.js`, `quiz.js`, `report.js`, `nav.js`) se
  **encolan** desde `functions.php` (`wp_enqueue_style/script`).

## 4. Estructura del tema

```
wp-content/themes/fiet-act/
├── style.css        Cabecera del tema (requerida por WP)
├── functions.php    Encola CSS/JS y define FIET_ASSETS
├── header.php       <head> + wp_head()
├── footer.php       wp_footer()
├── index.php        Plantilla base (provisional)
├── styles.css       CSS del sitio
├── main.js quiz.js report.js nav.js
├── *.html           Páginas actuales (se convertirán en plantillas PHP)
└── img/assets       logo.png, hero-poster.webp, video1.jpg, ...
```

## Siguiente fase (pendiente)

Convertir cada `.html` en plantilla PHP (`front-page.php`, `page-*.php`) y
exponer los textos/imágenes como campos editables (ACF) para que el contenido
se gestione desde WordPress sin tocar código.
