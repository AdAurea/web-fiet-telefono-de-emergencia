<?php if ( ! defined( 'ABSPATH' ) ) exit;
$uri  = get_template_directory_uri();
$tel  = fiet_option( 'telefono_tel', '900759759' );
$telv = fiet_option( 'telefono_display', '900 759 759' );
?>
<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="FIET · Inicio"><img src="<?php echo esc_url( $uri . '/logo.png' ); ?>" alt="FIET"></a>
<nav class="navbar">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">El teléfono</a>
	<a href="<?php echo esc_url( home_url( '/que-es-la-trata/' ) ); ?>">Qué es la trata</a>
	<a href="<?php echo esc_url( home_url( '/prevencion/' ) ); ?>">Prevención</a>
	<a href="<?php echo esc_url( home_url( '/recursos/' ) ); ?>">Recursos</a>
	<a class="nav-call" href="tel:<?php echo esc_attr( $tel ); ?>" aria-label="Llamar al <?php echo esc_attr( $telv ); ?>"><span class="live"></span>Llama <?php echo esc_html( $telv ); ?></a>
</nav>
<!-- Menú hamburguesa (móvil) -->
<button class="nav-burger" id="navBurger" type="button" aria-label="Abrir menú" aria-expanded="false"><span></span></button>
<div class="nav-backdrop" id="navBackdrop"></div>
<aside class="nav-drawer" id="navDrawer" aria-label="Menú de navegación">
	<button class="nav-drawer-close" id="navDrawerClose" type="button" aria-label="Cerrar menú">&times;</button>
	<nav class="nav-drawer-links" id="navDrawerLinks"></nav>
</aside>
