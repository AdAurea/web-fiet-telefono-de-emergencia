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

	// Cuestionario editable -> quiz.js
	$qd    = fiet_quiz_defaults();
	$qraw  = fiet_option( 'quiz_preguntas', $qd['preguntas'] );
	$preg  = array_values( array_filter( array_map( 'trim', preg_split( "/\r\n|\r|\n/", $qraw ) ), 'strlen' ) );
	wp_localize_script( 'fiet-quiz', 'FIET_QUIZ', array(
		'questions'   => $preg,
		'umbralMedio' => (int) fiet_option( 'quiz_umbral_medio', 1 ),
		'umbralAlto'  => (int) fiet_option( 'quiz_umbral_alto', 4 ),
		'bajo'  => array( 'titulo' => fiet_option( 'quiz_bajo_titulo', 'Riesgo bajo' ),   'texto' => fiet_option( 'quiz_bajo_texto', $qd['bajo'] ) ),
		'medio' => array( 'titulo' => fiet_option( 'quiz_medio_titulo', 'Riesgo medio' ), 'texto' => fiet_option( 'quiz_medio_texto', $qd['medio'] ) ),
		'alto'  => array( 'titulo' => fiet_option( 'quiz_alto_titulo', 'Riesgo alto' ),   'texto' => fiet_option( 'quiz_alto_texto', $qd['alto'] ) ),
		'tel'        => fiet_option( 'telefono_tel', '900759759' ),
		'telDisplay' => fiet_option( 'telefono_display', '900 759 759' ),
	) );
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

/** Valores por defecto del cuestionario (usados por ACF y por el localize a quiz.js) */
function fiet_quiz_defaults() {
	return array(
		'preguntas' => "¿Tu jefe/a o empleador/a te amenaza?\n¿Tienes limitada tu libertad de movimiento (por ejemplo, no puedes salir cuando no trabajas)?\n¿Te han quitado el pasaporte u otros documentos personales?\n¿Te impiden acceder a atención médica cuando la necesitas?\n¿Trabajas más de 8 horas al día sin descanso?\n¿Trabajas en condiciones inseguras o insalubres?\n¿Te obliga a trabajar incluso cuando estás enfermo/a?\n¿Te obliga a hacer actividades con las que no te sientes cómodo/a?\n¿Te dicen que tienes una deuda que debes pagar?\n¿Recibes el salario tarde, incompleto o variable sin explicación?\n¿Cobras menos del salario mínimo legal?",
		'bajo'  => 'No has marcado señales de alerta. Por lo que has indicado, no aparecen indicios claros de trata. Aun así, si algo te preocupa, puedes hablar con el Teléfono de Ayuda de forma confidencial y gratuita.',
		'medio' => 'Has marcado entre 1 y 3 señales. Algunas de tus respuestas pueden indicar una situación de riesgo. Te recomendamos contactar con el Teléfono de Ayuda para valorarlo con profesionales. Es confidencial y gratuito.',
		'alto'  => 'Has marcado 4 o más señales, que coinciden con indicios de trata. No estás sola: contacta cuanto antes con el Teléfono de Ayuda. Puedes permanecer en el anonimato.',
	);
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

	// --- Cuestionario ---
	$qd = fiet_quiz_defaults();
	acf_add_local_field_group( array(
		'key'    => 'group_fiet_quiz',
		'title'  => 'FIET · Cuestionario',
		'fields' => array(
			fiet_f( 'f_q_tag', 'Panel · Sobretítulo', 'quiz_intro_tag', 'Autoevaluación confidencial' ),
			fiet_f( 'f_q_tit', 'Panel · Título', 'quiz_intro_titulo', 'Evaluación del riesgo' ),
			fiet_f( 'f_q_sub', 'Panel · Subtítulo', 'quiz_intro_sub', 'Marca lo que corresponda a tu situación. El resultado es orientativo y confidencial; no sustituye el asesoramiento profesional.', 'textarea' ),
			array( 'key' => 'f_q_pre', 'label' => 'Preguntas (una por línea)', 'name' => 'quiz_preguntas', 'type' => 'textarea', 'rows' => 12, 'new_lines' => '', 'default_value' => $qd['preguntas'] ),
			fiet_f( 'f_q_btn', 'Botón "Ver resultado"', 'quiz_boton', 'Ver resultado →' ),
			array( 'key' => 'f_q_um', 'label' => 'Umbral riesgo MEDIO (nº de "Sí")', 'name' => 'quiz_umbral_medio', 'type' => 'number', 'default_value' => 1, 'min' => 1 ),
			array( 'key' => 'f_q_ua', 'label' => 'Umbral riesgo ALTO (nº de "Sí")', 'name' => 'quiz_umbral_alto', 'type' => 'number', 'default_value' => 4, 'min' => 1 ),
			fiet_f( 'f_q_bt', 'Resultado BAJO · Título', 'quiz_bajo_titulo', 'Riesgo bajo' ),
			fiet_f( 'f_q_bm', 'Resultado BAJO · Mensaje', 'quiz_bajo_texto', $qd['bajo'], 'textarea' ),
			fiet_f( 'f_q_mt', 'Resultado MEDIO · Título', 'quiz_medio_titulo', 'Riesgo medio' ),
			fiet_f( 'f_q_mm', 'Resultado MEDIO · Mensaje', 'quiz_medio_texto', $qd['medio'], 'textarea' ),
			fiet_f( 'f_q_at', 'Resultado ALTO · Título', 'quiz_alto_titulo', 'Riesgo alto' ),
			fiet_f( 'f_q_am', 'Resultado ALTO · Mensaje', 'quiz_alto_texto', $qd['alto'], 'textarea' ),
		),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'fiet-ajustes' ) ) ),
	) );
} );

// Página de ajustes en el admin (menú "Ajustes FIET")
add_action( 'admin_menu', function () {
	$hook = add_menu_page(
		'FIET', 'FIET', 'edit_theme_options',
		'fiet-ajustes', 'fiet_render_ajustes', 'dashicons-phone', 59
	);
	add_action( 'load-' . $hook, function () {
		if ( function_exists( 'acf_form_head' ) ) acf_form_head();
	} );
} );
function fiet_render_ajustes() {
	$tabs = array( 'globales' => 'Ajustes globales', 'cuestionario' => 'Cuestionario' );
	$tab  = ( isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ) ? sanitize_key( $_GET['tab'] ) : 'globales';

	echo '<div class="wrap"><h1>FIET</h1>';
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $k => $label ) {
		printf(
			'<a href="%s" class="nav-tab%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=fiet-ajustes&tab=' . $k ) ),
			$tab === $k ? ' nav-tab-active' : '',
			esc_html( $label )
		);
	}
	echo '</h2>';

	if ( function_exists( 'acf_form' ) ) {
		$group = ( $tab === 'cuestionario' ) ? 'group_fiet_quiz' : 'group_fiet_global';
		echo '<div style="margin-top:20px;max-width:820px;">';
		acf_form( array(
			'post_id'      => 'options',
			'field_groups' => array( $group ),
			'submit_value' => 'Guardar cambios',
		) );
		echo '</div>';
	} else {
		echo '<p>Instala y activa <strong>Advanced Custom Fields</strong> para editar estos ajustes.</p>';
	}
	echo '</div>';
}
