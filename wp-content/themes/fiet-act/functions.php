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
	register_nav_menus( array(
		'principal' => 'Menú principal (navbar)',
	) );
} );

/* -------------------------------------------------------------------------
 * Menú principal (navbar). Gestionable desde Apariencia > Menús.
 *
 * El walker imprime los <a> "pelados" (sin <ul>/<li>) para conservar
 * exactamente el mismo marcado y aspecto que el navbar original. Si no hay
 * ningún menú asignado a la ubicación "principal", el fallback pinta los
 * enlaces por defecto, de modo que el navbar nunca queda vacío.
 * ---------------------------------------------------------------------- */
if ( ! class_exists( 'FIET_Nav_Walker' ) ) {
	class FIET_Nav_Walker extends Walker_Nav_Menu {
		function start_lvl( &$output, $depth = 0, $args = null ) {}
		function end_lvl( &$output, $depth = 0, $args = null ) {}
		function end_el( &$output, $item, $depth = 0, $args = null ) {}
		function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$url    = ! empty( $item->url ) ? $item->url : '#';
			$title  = apply_filters( 'the_title', $item->title, $item->ID );
			$target = ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';
			// Clases personalizadas que el usuario escriba en el campo "Clases CSS"
			// del menú (se ignoran las clases automáticas menu-item-*).
			$extra = array();
			if ( ! empty( $item->classes ) && is_array( $item->classes ) ) {
				foreach ( $item->classes as $c ) {
					$c = trim( $c );
					if ( $c !== '' && strpos( $c, 'menu-item' ) !== 0 && strpos( $c, 'current' ) !== 0 && strpos( $c, 'page-item' ) !== 0 ) {
						$extra[] = $c;
					}
				}
			}
			$class = $extra ? ' class="' . esc_attr( implode( ' ', $extra ) ) . '"' : '';
			$output .= '<a href="' . esc_url( $url ) . '"' . $class . $target . '>' . esc_html( $title ) . '</a>';
		}
	}
}

function fiet_nav_fallback() {
	$links = array(
		home_url( '/' )                 => 'El teléfono',
		home_url( '/que-es-la-trata/' ) => 'Qué es la trata',
		home_url( '/prevencion/' )      => 'Prevención',
		home_url( '/recursos/' )        => 'Recursos',
	);
	foreach ( $links as $url => $label ) {
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
}

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

	// Menú principal: crearlo con los enlaces por defecto y asignarlo a la
	// ubicación "principal" si aún no hay ninguno asignado. A partir de ahí es
	// editable desde Apariencia > Menús.
	$locations = get_theme_mod( 'nav_menu_locations' );
	if ( empty( $locations['principal'] ) ) {
		$menu_name = 'Menú principal';
		$menu      = wp_get_nav_menu_object( $menu_name );
		$menu_id   = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
		if ( ! is_wp_error( $menu_id ) ) {
			// Solo poblarlo si está vacío (no pisar ediciones previas).
			if ( ! wp_get_nav_menu_items( $menu_id ) ) {
				$items = array(
					array( 'El teléfono',    home_url( '/' ) ),
					array( 'Qué es la trata', home_url( '/que-es-la-trata/' ) ),
					array( 'Prevención',      home_url( '/prevencion/' ) ),
					array( 'Recursos',        home_url( '/recursos/' ) ),
				);
				foreach ( $items as $it ) {
					wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title'  => $it[0],
						'menu-item-url'    => $it[1],
						'menu-item-status' => 'publish',
					) );
				}
			}
			$locations             = (array) $locations;
			$locations['principal'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
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

	// Descargas con captación (Recursos y las guías de Prevención)
	if ( is_page( array( 'recursos', 'prevencion' ) ) ) {
		wp_enqueue_script( 'fiet-descargas', $uri . '/descargas.js', array(), filemtime( $dir . '/descargas.js' ), true );
		wp_localize_script( 'fiet-descargas', 'FIET_DL', array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'fiet_descarga' ),
		) );
	}
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

/** Contenido por defecto de los modales de recomendaciones (WYSIWYG) */
function fiet_reco_defaults() {
	$empleo = <<<HTML
<h2>Empleo seguro</h2>
<p>Antes de aceptar una oferta, sigue estas recomendaciones para reducir riesgos y mantenerte a salvo.</p>
<h3>Recomendaciones generales</h3>
<ul>
<li><strong>Infórmate sobre la empresa o persona empleadora.</strong> Comprueba si tiene presencia real, referencias o trayectoria verificable.</li>
<li><strong>Revisa con atención las condiciones laborales.</strong> Desconfía de ofertas con información incompleta, ambigua o poco clara.</li>
<li><strong>No entregues tu documento de identidad a terceros.</strong> Llévalo siempre contigo y conserva copias en un lugar seguro.</li>
<li>Comparte tu situación con alguien de confianza: dónde vas a trabajar, con quién has contactado, y facilita una foto reciente.</li>
<li>Establece una señal de emergencia con familiares o amistades para pedir ayuda de forma discreta.</li>
<li>Lleva contigo una pequeña cantidad de dinero para imprevistos si el empleo implica desplazarte.</li>
<li><strong>Aprende frases básicas del lugar al que vas.</strong> Asegúrate de saber pedir ayuda y localizar el hospital o comisaría más cercanos.</li>
<li><strong>Prepara todos tus documentos y lleva copias.</strong> Guarda una copia digital en un lugar seguro.</li>
<li>Lleva contigo datos de contacto clave: organizaciones de apoyo, servicios sociales y, si viajas, embajada o consulado.</li>
</ul>
<h3>Si necesitas firmar un contrato</h3>
<ul>
<li>Exige que el contrato esté en un idioma que entiendas bien.</li>
<li><strong>No firmes nada que no comprendas.</strong> Busca asesoría si el lenguaje es ambiguo o poco claro.</li>
</ul>
<h3>Si el empleo es en el extranjero</h3>
<ul>
<li>Confirma que la empresa está registrada y tiene autorización para contratar.</li>
<li>Asegúrate de tener el permiso de trabajo correspondiente.</li>
<li>Ten en cuenta que un visado de turista normalmente no permite trabajar.</li>
</ul>
HTML;

	$viaje = <<<HTML
<h2>Viaje seguro</h2>
<p>Viajar suele ser positivo, pero también implica riesgos. Algunas personas pueden convertirse en víctimas de delitos, e incluso de trata, cuando viajan.</p>
<h3>Antes y durante el viaje</h3>
<ul>
<li>Lleva siempre los datos de contacto de organizaciones que apoyan a personas extranjeras, y la dirección y teléfono de tu embajada o consulado.</li>
<li>Comparte con familiares o amistades tu itinerario, copias de tus documentos y pasaporte, y una foto reciente tuya y de quienes viajan contigo.</li>
<li>Acuerda una señal de emergencia con tu familia o amistades para pedir ayuda de forma discreta.</li>
<li>Prepara todos los documentos importantes y lleva copias, en papel y digitales.</li>
<li><strong>Nunca entregues tu documento de identidad a nadie.</strong> Llévalo siempre contigo.</li>
<li>Mantente alerta ante comportamientos o situaciones sospechosas e informa a alguien de confianza.</li>
<li>Lleva siempre una pequeña cantidad de dinero en efectivo para emergencias.</li>
</ul>
<h3>Comunicación y orientación</h3>
<ul>
<li>Aprende algunas frases básicas en el idioma del país que visitas.</li>
<li>Debes poder pedir ayuda, pedir direcciones y saber dónde está el hospital o comisaría más cercanos.</li>
<li>Memoriza el número de teléfono de al menos un familiar o amigo.</li>
<li>Infórmate del número de emergencias del país (por ejemplo, 112 en la UE).</li>
</ul>
<h3>Interacciones con desconocidos</h3>
<ul>
<li>Sé precavido/a al hablar con personas que no conoces.</li>
<li>Nunca compartas tu nombre completo ni dónde te estás alojando.</li>
<li>Si crees que alguien te sigue o acosa, ve a una zona concurrida y no dudes en llamar a la policía.</li>
</ul>
HTML;

	$internet = <<<HTML
<h2>Internet seguro</h2>
<p>Cada vez más tratantes usan internet para captar víctimas: es de fácil acceso, bajo coste y bajo riesgo. Suelen crear perfiles falsos o suplantar identidades para ganarse la confianza. Los más vulnerables son niños, niñas y adolescentes.</p>
<h3>Citas con personas conocidas por internet</h3>
<ul>
<li><strong>Quedar con alguien que has conocido online es una situación de alto riesgo.</strong> Queda de día, en un lugar público y concurrido.</li>
<li>Si la otra persona propone un piso, un lugar oscuro, aislado o un parque poco transitado, es una señal de alerta.</li>
<li>Antes de la cita, informa a alguien de confianza.</li>
<li>Ten un plan de seguridad: que alguien te llame a una hora acordada, o una palabra clave para indicar que necesitas ayuda.</li>
</ul>
<h3>Protege tu información personal</h3>
<ul>
<li>Nunca compartas tu identidad completa, dirección, centro educativo, empresa o datos de tus familiares con personas conocidas por internet.</li>
<li><strong>No abras mensajes con contenido vulgar, inapropiado, peligroso o insultante.</strong> Bloquea a quienes los envíen.</li>
<li>Nunca envíes fotos a personas que acabas de conocer por internet.</li>
<li>Ajusta la visibilidad de tus fotos e información para que solo la vean personas de confianza.</li>
</ul>
HTML;

	return array( 'empleo' => $empleo, 'viaje' => $viaje, 'internet' => $internet );
}

/** Campos editables por página */
require get_template_directory() . '/inc/acf-fields.php';
require get_template_directory() . '/inc/descargas.php';

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

	// Dominio para el remitente: solo el host, sin esquema ni puerto (el :8080
	// rompía la sintaxis del email). No incluimos Reply-To porque el campo
	// "correo" es opcional (anonimato) y quedaría vacío -> mailbox inválido.
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( ! $host ) { $host = 'localhost'; }

	// Cuerpo HTML del correo: cabecera con logo + campos ordenados.
	$logo = get_template_directory_uri() . '/logo_footer.png';
	$body = <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0;padding:0;background:#f4f2ec;">
  <tr><td align="center" style="padding:24px 12px;">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;background:#ffffff;border:1px solid #e7e4da;border-radius:14px;overflow:hidden;">
      <tr><td style="background:#212428;padding:22px 28px;text-align:center;">
        <img src="$logo" alt="FIET · Teléfono ACT" width="120" style="display:inline-block;width:120px;height:auto;border:0;outline:none;">
      </td></tr>
      <tr><td style="padding:26px 28px 4px;font-family:Arial,Helvetica,sans-serif;">
        <p style="margin:0;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#b8860b;">Informar una sospecha</p>
        <h1 style="margin:6px 0 0;font-family:Georgia,'Times New Roman',serif;font-size:22px;line-height:1.25;color:#212428;">Nueva comunicación recibida</h1>
        <p style="margin:8px 0 0;font-size:13px;color:#6b7078;">Recibida el [_date] a las [_time]</p>
      </td></tr>
      <tr><td style="padding:14px 28px 20px;font-family:Arial,Helvetica,sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;color:#212428;">
          <tr><td style="padding:12px 0;border-top:1px solid #eeece4;">
            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a8f98;margin-bottom:4px;">Descripción</span>
            <span style="display:block;line-height:1.55;">[descripcion]</span>
          </td></tr>
          <tr><td style="padding:12px 0;border-top:1px solid #eeece4;">
            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a8f98;margin-bottom:4px;">Nombre</span>
            <span style="display:block;">[nombre]</span>
          </td></tr>
          <tr><td style="padding:12px 0;border-top:1px solid #eeece4;">
            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a8f98;margin-bottom:4px;">Teléfono</span>
            <span style="display:block;">[telefono]</span>
          </td></tr>
          <tr><td style="padding:12px 0;border-top:1px solid #eeece4;">
            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a8f98;margin-bottom:4px;">Correo</span>
            <span style="display:block;"><a href="mailto:[correo]" style="color:#b8860b;text-decoration:none;">[correo]</a></span>
          </td></tr>
          <tr><td style="padding:12px 0;border-top:1px solid #eeece4;border-bottom:1px solid #eeece4;">
            <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a8f98;margin-bottom:4px;">Acepta comunicaciones</span>
            <span style="display:block;">[comunicaciones]</span>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding:4px 28px 26px;font-family:Arial,Helvetica,sans-serif;">
        <p style="margin:0;font-size:12px;color:#9aa0a8;line-height:1.5;">Mensaje enviado desde [_site_title]. Todas las comunicaciones son confidenciales.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
HTML;

	// Respuesta automática al remitente (Correo 2). Solo se envía si dejó correo.
	$telv = fiet_option( 'telefono_display', '900 759 759' );
	$tel  = fiet_option( 'telefono_tel', '900759759' );
	$reply_body = <<<HTML
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0;padding:0;background:#f4f2ec;">
  <tr><td align="center" style="padding:24px 12px;">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;background:#ffffff;border:1px solid #e7e4da;border-radius:14px;overflow:hidden;">
      <tr><td style="background:#212428;padding:22px 28px;text-align:center;">
        <img src="$logo" alt="FIET · Teléfono ACT" width="120" style="display:inline-block;width:120px;height:auto;border:0;outline:none;">
      </td></tr>
      <tr><td style="padding:26px 28px 4px;font-family:Arial,Helvetica,sans-serif;">
        <p style="margin:0;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#b8860b;">Teléfono de Ayuda Contra la Trata</p>
        <h1 style="margin:6px 0 0;font-family:Georgia,'Times New Roman',serif;font-size:22px;line-height:1.25;color:#212428;">Hemos recibido tu mensaje</h1>
      </td></tr>
      <tr><td style="padding:14px 28px 4px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#333338;">
        <p style="margin:0 0 14px;">Gracias por confiar en nosotros. Hemos recibido tu comunicación y nuestro equipo la revisará con la máxima confidencialidad.</p>
        <p style="margin:0 0 14px;">Si tu situación es urgente o tu integridad corre peligro, puedes llamarnos ahora mismo. La llamada es <strong>gratuita, confidencial y está disponible las 24 horas</strong>.</p>
      </td></tr>
      <tr><td style="padding:6px 28px 8px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
          <tr><td style="background:#FFD400;border-radius:12px;padding:18px 24px;text-align:center;font-family:Arial,Helvetica,sans-serif;">
            <span style="display:block;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#5a4a00;">Llamada gratuita · 24 horas</span>
            <a href="tel:$tel" style="display:inline-block;margin-top:4px;font-family:Georgia,'Times New Roman',serif;font-size:30px;font-weight:bold;color:#212428;text-decoration:none;">$telv</a>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding:14px 28px 4px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:#333338;">
        <p style="margin:0 0 14px;">Toda la información que compartas es confidencial y puedes permanecer en el anonimato. Si nos facilitaste tus datos de contacto y lo consentiste, es posible que un miembro de nuestro equipo se ponga en contacto contigo.</p>
        <p style="margin:0;">Estamos aquí para ayudarte.</p>
        <p style="margin:16px 0 0;font-weight:bold;color:#212428;">Equipo del Teléfono de Ayuda Contra la Trata · FIET</p>
      </td></tr>
      <tr><td style="padding:18px 28px 26px;font-family:Arial,Helvetica,sans-serif;">
        <p style="margin:0;font-size:12px;color:#9aa0a8;line-height:1.5;border-top:1px solid #eeece4;padding-top:16px;">Este es un mensaje automático; por favor, no respondas a este correo. Para cualquier consulta, escríbenos a <a href="mailto:$email" style="color:#9aa0a8;">$email</a> o llama al $telv.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
HTML;

	$cf7 = WPCF7_ContactForm::get_template();
	$cf7->set_title( 'Informar una sospecha' );
	$cf7->set_properties( array(
		'form' => fiet_cf7_form_markup(),
		'mail' => array(
			'active'             => true,
			'subject'            => 'Nueva comunicación · Teléfono contra la Trata',
			'sender'             => '[_site_title] <wordpress@' . $host . '>',
			'recipient'          => $email,
			'body'               => $body,
			'additional_headers' => '',
			'attachments'        => '',
			'use_html'           => 1,
			'exclude_blank'      => 0,
		),
		'mail_2' => array(
			'active'             => true,
			'subject'            => 'Hemos recibido tu mensaje · Teléfono de Ayuda Contra la Trata',
			'sender'             => '[_site_title] <wordpress@' . $host . '>',
			'recipient'          => '[correo]',
			'body'               => $reply_body,
			'additional_headers' => 'Reply-To: ' . $email,
			'attachments'        => '',
			'use_html'           => 1,
			'exclude_blank'      => 0,
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
			array( 'key' => 'field_privacidad', 'label' => 'Política de privacidad · URL', 'name' => 'privacidad_url', 'type' => 'url', 'instructions' => 'Enlace a la política de privacidad. Se usa en el consentimiento del formulario de descargas y en el pie de página. Vacío = el texto aparece sin enlace.', 'default_value' => '' ),
			array( 'key' => 'field_cookies', 'label' => 'Política de cookies · URL', 'name' => 'cookies_url', 'type' => 'url', 'instructions' => 'Enlace a la política de cookies (pie de página). Vacío = el texto aparece sin enlace.', 'default_value' => '' ),
			array( 'key' => 'field_donaciones', 'label' => 'Política y condiciones de donación · URL', 'name' => 'donaciones_url', 'type' => 'url', 'instructions' => 'Enlace a la política y condiciones de donación (pie de página). Vacío = el texto aparece sin enlace.', 'default_value' => '' ),
			array( 'key' => 'field_calidad', 'label' => 'Política de calidad · URL', 'name' => 'calidad_url', 'type' => 'url', 'instructions' => 'Enlace a la política de calidad (pie de página). Vacío = el texto aparece sin enlace.', 'default_value' => '' ),
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

	// --- Recomendaciones (contenido de los modales "Saber más" de Prevención) ---
	$rd = fiet_reco_defaults();
	acf_add_local_field_group( array(
		'key'    => 'group_fiet_reco',
		'title'  => 'FIET · Recomendaciones',
		'fields' => array(
			array( 'key' => 'f_reco_emp', 'label' => 'Empleo seguro', 'name' => 'rec_modal_empleo', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'default_value' => $rd['empleo'] ),
			array( 'key' => 'f_reco_via', 'label' => 'Viaje seguro', 'name' => 'rec_modal_viaje', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'default_value' => $rd['viaje'] ),
			array( 'key' => 'f_reco_int', 'label' => 'Internet seguro', 'name' => 'rec_modal_internet', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 0, 'default_value' => $rd['internet'] ),
			array( 'key' => 'f_reco_dl_emp', 'label' => 'Guía de empleo preventivo · URL de descarga', 'name' => 'rec_url_guia_empleo', 'type' => 'url', 'instructions' => 'Pega la URL del PDF (súbelo en Medios y copia su enlace). Se enlaza en la card "Empleo Seguro" del popup. Vacío = el enlace no se muestra.' ),
			array( 'key' => 'f_reco_dl_men', 'label' => 'Guía de seguridad digital para menores · URL de descarga', 'name' => 'rec_url_guia_digital_menores', 'type' => 'url', 'instructions' => 'PDF enlazado en la card "Internet Seguro". Vacío = el enlace no se muestra.' ),
			array( 'key' => 'f_reco_dl_adu', 'label' => 'Guía de seguridad digital para adultos · URL de descarga', 'name' => 'rec_url_guia_digital_adultos', 'type' => 'url', 'instructions' => 'PDF enlazado en la card "Internet Seguro". Vacío = el enlace no se muestra.' ),
		),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'fiet-ajustes' ) ) ),
	) );

	// Recursos: URL de descarga del material de cada sector
	acf_add_local_field_group( array(
		'key'    => 'group_fiet_recursos_urls',
		'title'  => 'FIET · Recursos (descargas)',
		'fields' => array(
			array( 'key' => 'f_recu_san', 'label' => 'Sector sanitario · URL de descarga',  'name' => 'rec_url_sanitario',  'type' => 'url', 'instructions' => 'Pega la URL del PDF/página. Puedes subir el archivo en Medios y copiar aquí su enlace. Si lo dejas vacío, el botón no se muestra.' ),
			array( 'key' => 'f_recu_hos', 'label' => 'Sector hostelero · URL de descarga',  'name' => 'rec_url_hostelero',  'type' => 'url' ),
			array( 'key' => 'f_recu_tra', 'label' => 'Sector transporte · URL de descarga', 'name' => 'rec_url_transporte', 'type' => 'url' ),
			array( 'key' => 'f_recu_con', 'label' => 'Sector consular · URL de descarga',   'name' => 'rec_url_consular',   'type' => 'url' ),
			array( 'key' => 'f_recu_edu', 'label' => 'Sector educativo · URL de descarga',  'name' => 'rec_url_educativo',  'type' => 'url' ),
			array( 'key' => 'f_recu_ter', 'label' => 'Tercer Sector · URL de descarga',    'name' => 'rec_url_tercer_sector', 'type' => 'url' ),
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
	$tabs = array( 'globales' => 'Ajustes globales', 'cuestionario' => 'Cuestionario', 'recomendaciones' => 'Prevención', 'recursos' => 'Recursos' );
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
		$group = 'group_fiet_global';
		if ( $tab === 'cuestionario' )      $group = 'group_fiet_quiz';
		elseif ( $tab === 'recomendaciones' ) $group = 'group_fiet_reco';
		elseif ( $tab === 'recursos' )        $group = 'group_fiet_recursos_urls';
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
