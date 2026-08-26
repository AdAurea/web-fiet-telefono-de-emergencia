<?php
/**
 * Plantilla de reserva (fallback). Rara vez se usa: las páginas emplean
 * page.php / plantillas específicas y la portada front-page.php.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>


  <main class="policy">
    <div class="policy-inner">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <header class="policy-head"><h1 class="policy-title"><?php the_title(); ?></h1></header>
        <div class="policy-body"><?php the_content(); ?></div>
      <?php endwhile; else : ?>
        <div class="policy-body"><p>No hay contenido disponible.</p></div>
      <?php endif; ?>
    </div>
  </main>

<?php get_footer();
