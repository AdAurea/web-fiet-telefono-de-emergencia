<?php
/**
 * Template Name: Política / Legal
 *
 * Plantilla reutilizable para las páginas de políticas (privacidad, cookies,
 * condiciones de donación, aviso legal…). El texto se escribe en el editor de
 * la página; esta plantilla le da el diseño acorde al sitio.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<?php get_template_part( 'parts/site-nav' ); ?>

  <main class="policy">
    <div class="policy-inner">
      <header class="policy-head">
        <span class="policy-eyebrow"><span class="dot"></span>Información legal</span>
        <h1 class="policy-title"><?php the_title(); ?></h1>
        <p class="policy-meta">Última actualización: <?php echo esc_html( get_the_modified_date( 'j \d\e F \d\e Y' ) ); ?></p>
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
