<?php
/**
 * Tema FIET · Teléfono ACT
 * Encola CSS/JS, expone la base de assets al canvas y define los campos editables.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* -------------------------------------------------------------------------
 * Soporte de tema
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'style', 'script' ) );
} );

/* -------------------------------------------------------------------------
 * Encolado de estilos y scripts
 * ---------------------------------------------------------------------- */
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

	// CSS del sitio (styles.css es el real; style.css solo lleva la cabecera del tema)
	wp_enqueue_style( 'fiet-styles', $uri . '/styles.css', array(), filemtime( $dir . '/styles.css' ) );

	// Scripts compartidos (footer). main.js no hace nada donde no hay lienzo.
	foreach ( array( 'main', 'quiz', 'report', 'nav' ) as $h ) {
		wp_enqueue_script( 'fiet-' . $h, $uri . "/$h.js", array(), filemtime( "$dir/$h.js" ), true );
	}

	// Base de assets para las imágenes que carga el canvas (main.js)
	wp_add_inline_script(
		'fiet-main',
		'window.FIET_ASSETS = ' . wp_json_encode( trailingslashit( $uri ) ) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'fiet_act_assets' );

/* -------------------------------------------------------------------------
 * Helper de contenido editable: usa ACF si está disponible, si no el valor
 * por defecto (para que el tema funcione también sin el plugin).
 * ---------------------------------------------------------------------- */
function fiet_field( $name, $default = '', $id = false ) {
	if ( function_exists( 'get_field' ) ) {
		$v = get_field( $name, $id );
		if ( $v !== null && $v !== '' ) {
			return $v;
		}
	}
	return $default;
}
function fiet_option( $name, $default = '' ) {
	return fiet_field( $name, $default, 'option' );
}

/* -------------------------------------------------------------------------
 * ACF: campos globales (grupo local, en el tema) + página de ajustes propia.
 * Compatible con ACF free (las Options Pages programáticas son de ACF PRO):
 * creamos nuestra página de admin y renderizamos el grupo con acf_form().
 * ---------------------------------------------------------------------- */
add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;
	acf_add_local_field_group( array(
		'key'    => 'group_fiet_global',
		'title'  => 'FIET · Datos globales',
		'fields' => array(
			array( 'key' => 'field_tel_display', 'label' => 'Teléfono (visible)', 'name' => 'telefono_display', 'type' => 'text', 'default_value' => '900 759 759' ),
			array( 'key' => 'field_tel_tel', 'label' => 'Teléfono (enlace, sin espacios)', 'name' => 'telefono_tel', 'type' => 'text', 'default_value' => '900759759' ),
			array( 'key' => 'field_email', 'label' => 'Correo de contacto', 'name' => 'email_contacto', 'type' => 'text', 'default_value' => 'informacion@fiet.ong' ),
		),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'fiet-ajustes' ) ) ),
	) );
} );

// Página de ajustes en el admin (menú "Ajustes FIET")
add_action( 'admin_menu', function () {
	$hook = add_menu_page(
		'Ajustes del sitio (FIET)', 'Ajustes FIET', 'edit_theme_options',
		'fiet-ajustes', 'fiet_render_ajustes', 'dashicons-phone', 59
	);
	add_action( 'load-' . $hook, function () {
		if ( function_exists( 'acf_form_head' ) ) acf_form_head();
	} );
} );
function fiet_render_ajustes() {
	echo '<div class="wrap"><h1>Ajustes del sitio (FIET)</h1>';
	if ( function_exists( 'acf_form' ) ) {
		acf_form( array(
			'post_id'      => 'options',
			'field_groups' => array( 'group_fiet_global' ),
			'submit_value' => 'Guardar cambios',
		) );
	} else {
		echo '<p>Instala y activa <strong>Advanced Custom Fields</strong> para editar estos ajustes.</p>';
	}
	echo '</div>';
}
