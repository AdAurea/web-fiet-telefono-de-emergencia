<?php
/**
 * Template Name: Ancho completo (Elementor)
 *
 * Contenido a ancho completo, sin el contenedor estrecho de page.php.
 * Pensada para páginas maquetadas con Elementor u otro constructor: el
 * navbar (header.php) y el footer (footer.php) del sitio se mantienen.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

  <main class="page-fw">
    <?php
    while ( have_posts() ) {
      the_post();
      the_content();
    }
    ?>
  </main>

<?php get_footer();
