<?php
/**
 * Plantilla por defecto para páginas (sin plantilla específica asignada).
 * Muestra el título y el contenido del editor con el diseño del sitio.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>


  <main class="policy">
    <div class="policy-inner">
      <header class="policy-head">
        <h1 class="policy-title"><?php the_title(); ?></h1>
      </header>
      <div class="policy-body">
        <?php
        while ( have_posts() ) {
          the_post();
          the_content();
        }
        ?>
      </div>
    </div>
  </main>

<?php get_footer();
