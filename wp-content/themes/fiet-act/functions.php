<?php
/**
 * Tema FIET · Teléfono ACT
 * Encola el CSS y JS del sitio y expone la base de assets al JS del canvas.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function fiet_act_assets() {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	// Fuentes (Figtree + Fraunces)
	wp_enqueue_style(
		'fiet-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,400&family=Figtree:wght@300;400;500;600;700;800;900&display=swap',
		array(),
		null
	);

	// Estilos del sitio (styles.css es el CSS real; style.css solo lleva la cabecera del tema)
	wp_enqueue_style( 'fiet-styles', $uri . '/styles.css', array(), filemtime( $dir . '/styles.css' ) );

	// Scripts del sitio (en el footer)
	foreach ( array( 'main', 'quiz', 'report', 'nav' ) as $handle ) {
		wp_enqueue_script( 'fiet-' . $handle, $uri . "/$handle.js", array(), filemtime( "$dir/$handle.js" ), true );
	}

	// Base de assets para las imágenes que carga el canvas (main.js): URL del tema
	wp_add_inline_script(
		'fiet-main',
		'window.FIET_ASSETS = ' . wp_json_encode( trailingslashit( $uri ) ) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'fiet_act_assets' );
