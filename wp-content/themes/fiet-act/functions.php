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
 * Al activar el tema, crea las páginas que usan las plantillas (page-<slug>.php)
 * si no existen todavía.
 * ---------------------------------------------------------------------- */
add_action( 'after_switch_theme', function () {
	$pages = array(
		'inicio'          => 'Inicio (El teléfono)',
		'que-es-la-trata' => 'Qué es la trata',
		'prevencion'      => 'Prevención',
		'recursos'        => 'Recursos',
	);
	foreach ( $pages as $slug => $title ) {
		$p = get_page_by_path( $slug );
		if ( ! $p ) {
			$id = wp_insert_post( array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_name'   => $slug,
				'post_title'  => $title,
				'post_content'=> '',
			) );
			if ( $slug === 'inicio' ) { $inicio_id = $id; }
		} elseif ( $slug === 'inicio' ) {
			$inicio_id = $p->ID;
		}
	}
	// Portada estática = "Inicio" (la renderiza front-page.php)
	if ( ! empty( $inicio_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $inicio_id );
	}
	// Enlaces permanentes "bonitos" (para /prevencion/, etc.)
	if ( get_option( 'permalink_structure' ) === '' ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();
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
/** Imprime un campo de la página actual (escapado), con valor por defecto */
function ff( $name, $default = '' ) {
	echo esc_html( fiet_field( $name, $default ) );
}

/** Campos editables por página */
require get_template_directory() . '/inc/acf-fields.php';

/**
 * Formulario "Informar una sospecha": usa Contact Form 7 si está configurado
 * (editable y funcional desde el admin); si no, el formulario estático de
 * reserva. En ambos casos conserva el mismo diseño (mismas clases .report-*).
 */
// Contact Form 7: no insertar <p>/<br> automáticos (respeta el markup del panel)
add_filter( 'wpcf7_autop_or_not', '__return_false' );

function fiet_report_form_inner() {
	$cf7 = fiet_option( 'cf7_id', get_option( 'fiet_report_cf7', '' ) );
	if ( $cf7 && shortcode_exists( 'contact-form-7' ) ) {
		echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7 ) . '"]' );
	} else {
		get_template_part( 'parts/report-form-static' );
	}
}

/** Markup (form-tags CF7) del formulario "Informar una sospecha" */
function fiet_cf7_form_markup() {
	return <<<HTML
<div class="report-inner">
  <div class="report-left">
    <span class="tag">Informar una sospecha</span>
    <h2>Podemos ayudarte.</h2>
    <p>Eres víctima o sospechas de una potencial situación de trata. Describe la situación con el mayor detalle posible, incluyendo fechas, horas, ubicación exacta (país, ciudad, dirección, código postal y referencias), descripción de las personas implicadas y, si procede, matrículas u otros datos identificativos.</p>
    <p>Si la situación ocurre en el ámbito digital, facilita el enlace de la publicación, los nombres de las cuentas implicadas y una breve descripción de lo sucedido. Todas las comunicaciones son confidenciales y puedes permanecer en el anonimato.</p>
  </div>
  <div class="report-right">
    <span class="tag">Detalles de la descripción</span>
    [textarea* descripcion rows:5 placeholder "Describe la situación con el mayor detalle posible..."]
    <p class="report-note">Si consientes que un miembro de nuestro equipo pueda ponerse en contacto contigo, facilita alguno de los siguientes datos. Todos son opcionales.</p>
    <div class="report-grid">
      <label>Nombre[text nombre autocomplete:name]</label>
      <label>Número de teléfono[tel telefono autocomplete:tel]</label>
      <label>Correo[email correo autocomplete:email]</label>
      <label>Redes sociales[text redes]</label>
    </div>
    <label class="check">[acceptance privacidad] Acepto la Política de Privacidad.</label>
    <label class="check">[acceptance comunicaciones optional] Acepto recibir comunicaciones informativas de FIET.</label>
    <div class="report-submit">[submit class:btn-hero class:btn-dark "Enviar"]</div>
  </div>
</div>
HTML;
}

/** Crea el formulario CF7 si no existe todavía (portabilidad al hosting real) */
add_action( 'admin_init', function () {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) return;
	$id = (int) get_option( 'fiet_report_cf7', 0 );
	if ( $id && get_post( $id ) ) return;

	$email = fiet_option( 'email_contacto', 'informacion@fiet.ong' );
	$body  = "Nueva comunicación recibida desde el sitio.\n\nDescripción:\n[descripcion]\n\nNombre: [nombre]\nTeléfono: [telefono]\nCorreo: [correo]\nRedes: [redes]\n\nAcepta comunicaciones: [comunicaciones]\n";

	$cf7 = WPCF7_ContactForm::get_template();
	$cf7->set_title( 'Informar una sospecha' );
	$cf7->set_properties( array(
		'form' => fiet_cf7_form_markup(),
		'mail' => array(
			'active'             => true,
			'subject'            => 'Nueva comunicación · Teléfono contra la Trata',
			'sender'             => '[_site_title] <wordpress@' . preg_replace( '#^https?://#', '', home_url() ) . '>',
			'recipient'          => $email,
			'body'               => $body,
			'additional_headers' => 'Reply-To: [correo]',
			'attachments'        => '',
			'use_html'           => 0,
			'exclude_blank'      => 1,
		),
	) );
	$new = $cf7->save();
	if ( $new ) update_option( 'fiet_report_cf7', $new );
} );

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
			array( 'key' => 'field_cf7', 'label' => 'Formulario Contact Form 7 · ID', 'name' => 'cf7_id', 'type' => 'text', 'instructions' => 'ID del formulario CF7 para "Informar una sospecha". Vacío = formulario estático.', 'default_value' => '' ),
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
