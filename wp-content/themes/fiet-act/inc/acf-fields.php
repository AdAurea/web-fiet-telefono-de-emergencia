<?php
/**
 * Campos editables por página (ACF, grupos locales del tema).
 * La ubicación se resuelve por slug de página en tiempo de ejecución.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Importa una imagen del tema a la Biblioteca de medios (una sola vez) y
 * devuelve su ID de adjunto. Deduplica por meta _fiet_theme_src.
 */
function fiet_import_theme_image( $filename ) {
	$found = get_posts( array(
		'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids',
		'meta_key' => '_fiet_theme_src', 'meta_value' => $filename,
	) );
	if ( $found ) return (int) $found[0];

	$src = trailingslashit( get_template_directory() ) . $filename;
	if ( ! file_exists( $src ) ) return 0;

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $filename, null, file_get_contents( $src ) );
	if ( ! empty( $upload['error'] ) ) return 0;

	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( is_wp_error( $id ) || ! $id ) return 0;

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_fiet_theme_src', $filename );
	return (int) $id;
}

/** Precarga el campo de imagen de portada de una página con la imagen del tema (si está vacío). */
function fiet_seed_portada( $page_id, $field, $filename ) {
	if ( ! $page_id || ! function_exists( 'get_field' ) ) return;
	if ( get_field( $field, $page_id ) ) return;               // ya tiene imagen: respetar
	$att = fiet_import_theme_image( $filename );
	if ( $att ) update_field( $field, $att, $page_id );
}

/** Al entrar al admin (una vez), deja las imágenes de portada por defecto en la biblioteca y precargadas. */
add_action( 'admin_init', function () {
	if ( get_option( 'fiet_portadas_seeded' ) ) return;
	if ( ! function_exists( 'update_field' ) ) return;
	$front = (int) get_option( 'page_on_front' );
	$prev  = get_page_by_path( 'prevencion' );
	$rec   = get_page_by_path( 'recursos' );
	fiet_seed_portada( $front,               'tel_portada_img',  'foto_portada_telefono.png' );
	fiet_seed_portada( $prev ? $prev->ID : 0, 'prev_portada_img', 'foto_portada_como_mantenerse_a_salvo.png' );
	fiet_seed_portada( $rec ? $rec->ID : 0,   'rec_portada_img',  'foto_portada_recursos_y_servicios.png' );
	update_option( 'fiet_portadas_seeded', 1 );
} );

/** Atajo para definir un campo de texto/área (con ancho opcional para columnas) */
function fiet_f( $key, $label, $name, $default = '', $type = 'text', $width = '' ) {
	$f = array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => $type, 'default_value' => $default );
	if ( $type === 'textarea' ) { $f['rows'] = 3; $f['new_lines'] = ''; }
	if ( $width !== '' ) { $f['wrapper'] = array( 'width' => $width ); }
	return $f;
}

/** Campo de imagen (portada): muestra miniatura + editar/quitar. Devuelve URL. */
function fiet_img_f( $key, $name, $label = 'Imagen de portada', $instr = '' ) {
	return array(
		'key' => $key, 'label' => $label, 'name' => $name, 'type' => 'image',
		'return_format' => 'url', 'preview_size' => 'medium', 'library' => 'all',
		'mime_types' => 'jpg,jpeg,png,webp',
		'instructions' => $instr !== '' ? $instr : 'Si la dejas vacía se usa la imagen por defecto del tema.',
	);
}

/** Desplegable de etiqueta HTML (SEO) en columna estrecha, junto a su texto */
function fiet_tagf( $key, $name, $default = 'p', $width = '30' ) {
	return array(
		'key' => $key, 'label' => 'Etiqueta HTML', 'name' => $name, 'type' => 'select',
		'choices' => function_exists( 'fiet_tag_choices' ) ? fiet_tag_choices() : array(),
		'default_value' => $default,
		'instructions' => 'Etiqueta HTML para SEO. No cambia el diseño.',
		'wrapper' => array( 'width' => $width ),
	);
}

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	/* ===================== PREVENCIÓN ===================== */
	$prev = get_page_by_path( 'prevencion' );
	if ( $prev ) {
		acf_add_local_field_group( array(
			'key'    => 'group_fiet_prevencion',
			'title'  => 'Prevención · Textos',
			'fields' => array(
				fiet_img_f( 'f_prev_img', 'prev_portada_img', 'Portada · Imagen', 'Imagen de fondo de la portada. Vacío = imagen por defecto del tema.' ),
				fiet_f( 'f_prev_eye', 'Portada · Sobretítulo', 'prev_eyebrow', 'Prevención', 'text', '70' ),
				fiet_tagf( 'f_prev_eye_tag', 'prev_eyebrow_tag', 'span' ),
				fiet_f( 'f_prev_tit', 'Portada · Título', 'prev_titulo', '¿Cómo mantenerse a salvo?', 'text', '70' ),
				fiet_tagf( 'f_prev_tit_tag', 'prev_titulo_tag', 'h2' ),
				fiet_f( 'f_prev_par', 'Portada · Párrafo', 'prev_parrafo', 'La trata puede comenzar en situaciones cotidianas como la búsqueda de empleo, un viaje o el uso de internet. Conocer los riesgos y saber identificarlos es clave para protegerte.', 'textarea' ),
				fiet_f( 'f_prev_btn', 'Portada · Botón', 'prev_boton', 'Ver recomendaciones' ),
				fiet_f( 'f_prev_peye', 'Popup · Sobretítulo', 'prev_pop_eyebrow', 'Prevención' ),
				fiet_f( 'f_prev_ptit', 'Popup · Título', 'prev_pop_titulo', 'Recomendaciones para mantenerte seguro', 'text', '70' ),
				fiet_tagf( 'f_prev_ptit_tag', 'prev_pop_titulo_tag', 'h2' ),
				fiet_f( 'f_prev_ppar', 'Popup · Párrafo', 'prev_pop_parrafo', 'La trata puede empezar en un empleo, un viaje o en internet. Estas son las claves para reducir riesgos e identificar señales de alerta en cada situación.', 'textarea' ),
				fiet_f( 'f_prev_c1t', 'Card 1 · Título', 'prev_card1_titulo', 'Empleo Seguro', 'text', '70' ),
				fiet_tagf( 'f_prev_c1t_tag', 'prev_card1_titulo_tag', 'h3' ),
				fiet_f( 'f_prev_c1d', 'Card 1 · Descripción', 'prev_card1_desc', 'Verifica la oferta y a quien contrata, nunca entregues tus documentos y comparte con alguien de confianza dónde y con quién vas a trabajar.', 'textarea' ),
				fiet_f( 'f_prev_c2t', 'Card 2 · Título', 'prev_card2_titulo', 'Viaje Seguro', 'text', '70' ),
				fiet_tagf( 'f_prev_c2t_tag', 'prev_card2_titulo_tag', 'h3' ),
				fiet_f( 'f_prev_c2d', 'Card 2 · Descripción', 'prev_card2_desc', 'Lleva copias de tus documentos, comparte tu itinerario y ten a mano los contactos de tu embajada y de organizaciones de ayuda.', 'textarea' ),
				fiet_f( 'f_prev_c3t', 'Card 3 · Título', 'prev_card3_titulo', 'Internet Seguro', 'text', '70' ),
				fiet_tagf( 'f_prev_c3t_tag', 'prev_card3_titulo_tag', 'h3' ),
				fiet_f( 'f_prev_c3d', 'Card 3 · Descripción', 'prev_card3_desc', 'Protege tus datos personales, desconfía de perfiles desconocidos y extrema la precaución si conciertas una cita con alguien conocido por internet.', 'textarea' ),
				fiet_f( 'f_prev_cbtn', 'Cards · Texto del botón', 'prev_card_boton', 'Saber más' ),
			),
			'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => $prev->ID ) ) ),
		) );
	}

	/* ===================== RECURSOS ===================== */
	$rec = get_page_by_path( 'recursos' );
	if ( $rec ) {
		acf_add_local_field_group( array(
			'key'    => 'group_fiet_recursos',
			'title'  => 'Recursos · Textos',
			'fields' => array(
				fiet_img_f( 'f_rec_img', 'rec_portada_img', 'Portada · Imagen', 'Imagen de fondo de la portada. Vacío = imagen por defecto del tema.' ),
				fiet_f( 'f_rec_eye', 'Portada · Sobretítulo', 'rec_eyebrow', 'Recursos', 'text', '70' ),
				fiet_tagf( 'f_rec_eye_tag', 'rec_eyebrow_tag', 'span' ),
				fiet_f( 'f_rec_tit', 'Portada · Título', 'rec_titulo', 'Recursos y servicios.', 'text', '70' ),
				fiet_tagf( 'f_rec_tit_tag', 'rec_titulo_tag', 'h2' ),
				fiet_f( 'f_rec_par', 'Portada · Párrafo', 'rec_parrafo', 'El Teléfono ACT ofrece formaciones gratuitas a profesionales y sectores con mayor riesgo de detectar situaciones de trata, además de una amplia red de derivación y materiales especializados.', 'textarea' ),
				fiet_f( 'f_rec_btn', 'Portada · Botón', 'rec_boton', 'Ver formaciones' ),
				fiet_f( 'f_rec_stit', 'Sección servicios · Título', 'rec_serv_titulo', 'Una amplia gama de servicios a tu disposición', 'text', '70' ),
				fiet_tagf( 'f_rec_stit_tag', 'rec_serv_titulo_tag', 'h2' ),
				fiet_f( 'f_rec_s1t', 'Servicio 1 · Título', 'rec_serv1_titulo', 'Derivaciones', 'text', '70' ),
				fiet_tagf( 'f_rec_s1t_tag', 'rec_serv1_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s1d', 'Servicio 1 · Descripción', 'rec_serv1_desc', 'Conectamos a quien llama con servicios especializados: gestión de casos, alojamiento seguro, transporte, asistencia legal y apoyo psicológico y de salud mental.', 'textarea' ),
				fiet_f( 'f_rec_s2t', 'Servicio 2 · Título', 'rec_serv2_titulo', 'Formación a profesionales', 'text', '70' ),
				fiet_tagf( 'f_rec_s2t_tag', 'rec_serv2_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s2d', 'Servicio 2 · Descripción', 'rec_serv2_desc', 'Formación y asistencia técnica a fuerzas de seguridad, profesionales sanitarios, personal aeroportuario y organismos públicos. Fortalecemos protocolos locales y nacionales.', 'textarea' ),
				fiet_f( 'f_rec_s3t', 'Servicio 3 · Título', 'rec_serv3_titulo', 'Informar una sospecha', 'text', '70' ),
				fiet_tagf( 'f_rec_s3t_tag', 'rec_serv3_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s3d', 'Servicio 3 · Descripción', 'rec_serv3_desc', 'Recibimos información sobre posibles situaciones de trata. Todas las comunicaciones son confidenciales y la persona puede permanecer en el anonimato.', 'textarea' ),
				fiet_f( 'f_rec_s4t', 'Servicio 4 · Título', 'rec_serv4_titulo', 'Asistencia a víctimas', 'text', '70' ),
				fiet_tagf( 'f_rec_s4t_tag', 'rec_serv4_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s4d', 'Servicio 4 · Descripción', 'rec_serv4_desc', 'Apoyo en crisis mediante planes de seguridad, acompañamiento emocional y conexión con servicios de emergencia y entidades especializadas.', 'textarea' ),
				fiet_f( 'f_rec_s5t', 'Servicio 5 · Título', 'rec_serv5_titulo', 'Verificación de empleo', 'text', '70' ),
				fiet_tagf( 'f_rec_s5t_tag', 'rec_serv5_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s5d', 'Servicio 5 · Descripción', 'rec_serv5_desc', 'Servicio gratuito de verificación de ofertas de empleo. Revisamos el registro de la empresa, antecedentes y opiniones, y elaboramos una evaluación de riesgo.', 'textarea' ),
				fiet_f( 'f_rec_s6t', 'Servicio 6 · Título', 'rec_serv6_titulo', 'Servicio de interpretación', 'text', '70' ),
				fiet_tagf( 'f_rec_s6t_tag', 'rec_serv6_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_s6d', 'Servicio 6 · Descripción', 'rec_serv6_desc', 'Atención en más de 200 idiomas para facilitar la comunicación y la intervención de profesionales especializados en trata de personas.', 'textarea' ),
				fiet_f( 'f_rec_peye', 'Popup · Sobretítulo', 'rec_pop_eyebrow', 'Recursos' ),
				fiet_f( 'f_rec_ptit', 'Popup · Título', 'rec_pop_titulo', 'Formación especializada.', 'text', '70' ),
				fiet_tagf( 'f_rec_ptit_tag', 'rec_pop_titulo_tag', 'h2' ),
				fiet_f( 'f_rec_pp1', 'Popup · Párrafo 1', 'rec_pop_p1', 'Proporcionamos conocimientos clave sobre la magnitud y las formas de la trata, los indicadores específicos según cada ámbito profesional y los protocolos de actuación necesarios en situaciones de sospecha o identificación.', 'textarea' ),
				fiet_f( 'f_rec_pp2', 'Popup · Párrafo 2', 'rec_pop_p2', 'Contamos con una amplia base de datos de organizaciones que imparten formaciones, campañas y charlas en todo el territorio, además de un catálogo de materiales especializados.', 'textarea' ),
				fiet_f( 'f_rec_sec1', 'Sector 1 · Título', 'rec_sec1_titulo', 'Sector sanitario', 'text', '70' ),
				fiet_tagf( 'f_rec_sec1_tag', 'rec_sec1_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_sec2', 'Sector 2 · Título', 'rec_sec2_titulo', 'Sector hostelero', 'text', '70' ),
				fiet_tagf( 'f_rec_sec2_tag', 'rec_sec2_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_sec3', 'Sector 3 · Título', 'rec_sec3_titulo', 'Sector transporte', 'text', '70' ),
				fiet_tagf( 'f_rec_sec3_tag', 'rec_sec3_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_sec4', 'Sector 4 · Título', 'rec_sec4_titulo', 'Sector consular', 'text', '70' ),
				fiet_tagf( 'f_rec_sec4_tag', 'rec_sec4_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_sec5', 'Sector 5 · Título', 'rec_sec5_titulo', 'Sector educativo', 'text', '70' ),
				fiet_tagf( 'f_rec_sec5_tag', 'rec_sec5_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_sec6', 'Sector 6 · Título', 'rec_sec6_titulo', 'Tercer Sector', 'text', '70' ),
				fiet_tagf( 'f_rec_sec6_tag', 'rec_sec6_titulo_tag', 'h3' ),
				fiet_f( 'f_rec_mat', 'Sectores · Texto del enlace', 'rec_mat_boton', 'Descarga el material' ),
				fiet_f( 'f_rec_foot', 'Popup · Párrafo final', 'rec_pop_foot', 'Nuestro equipo de especialistas ofrece orientación para la elaboración e implementación de protocolos de actuación. Contáctanos para más información o asesoramiento específico.', 'textarea' ),
			),
			'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => $rec->ID ) ) ),
		) );
	}

	/* ===================== QUÉ ES LA TRATA ===================== */
	$que = get_page_by_path( 'que-es-la-trata' );
	if ( $que ) {
		acf_add_local_field_group( array(
			'key'    => 'group_fiet_quees',
			'title'  => 'Qué es la trata · Textos',
			'fields' => array(
				fiet_f( 'f_qet_eye', 'Escena 1 · Sobretítulo', 'qet_eyebrow', '¿Qué es la trata?' ),
				fiet_f( 'f_qet_def', 'Escena 1 · Definición', 'qet_def', 'La trata de personas Es un delito que consiste en la captación, traslado y explotación de personas mediante engaño, abuso de vulnerabilidad o violencia, con fines como la explotación sexual, laboral u otras formas de explotación.', 'textarea' ),
				fiet_f( 'f_qet_eeye', 'España · Sobretítulo', 'qet_esp_eyebrow', 'La trata en España' ),
				fiet_f( 'f_qet_etit', 'España · Título', 'qet_esp_titulo', 'España es un país de origen, tránsito y destino de la trata de seres humanos.', 'textarea', '70' ),
				fiet_tagf( 'f_qet_etit_tag', 'qet_esp_titulo_tag', 'h2' ),
				fiet_f( 'f_qet_epar', 'España · Párrafo 1', 'qet_esp_parrafo', 'Se han detectado casos en todas las comunidades autónomas y, además, el país se sitúa entre los mayores consumidores de prostitución del mundo.', 'textarea' ),
				fiet_f( 'f_qet_epar2', 'España · Párrafo 2 (emergente)', 'qet_esp_parrafo2', 'España es además uno de los países europeos con mayor demanda de prostitución, un factor que favorece la explotación sexual. Según el Ministerio del Interior, en 2024 el 56 % de las víctimas detectadas fueron mujeres y el 44 % hombres.', 'textarea' ),
				fiet_f( 'f_qet_itag', 'No estás sola · Sobretítulo', 'qet_intro_tag', 'Confidencial · Gratuito · Disponible 24/7', 'text', '70' ),
				fiet_tagf( 'f_qet_itag_tag', 'qet_intro_eyebrow_tag', 'span' ),
				fiet_f( 'f_qet_itit', 'No estás sola · Título', 'qet_intro_titulo', 'No estás sola', 'text', '70' ),
				fiet_tagf( 'f_qet_itit_tag', 'qet_intro_titulo_tag', 'h2' ),
				fiet_f( 'f_qet_ipar0', 'No estás sola · Párrafo 1', 'qet_intro_parrafo0', 'El 900 759 759 es el Teléfono de Ayuda Contra la Trata en España. Está disponible 24/7 y es atendido por profesionales especializados que ofrecen una respuesta rápida, segura y confidencial.', 'textarea' ),
				fiet_f( 'f_qet_ipar', 'No estás sola · Párrafo 2', 'qet_intro_parrafo', 'Si crees que tú o alguien que conoces puede estar en una situación de trata, contacta. Puedes permanecer en el anonimato.', 'textarea' ),
				fiet_f( 'f_qet_ibtn', 'No estás sola · Botón', 'qet_intro_boton', 'Línea de asistencia 24h' ),
				fiet_f( 'f_qet_qtag', 'Cuestionario · Sobretítulo', 'qet_quiz_tag', 'Autoevaluación confidencial' ),
				fiet_f( 'f_qet_qtit', 'Cuestionario · Título', 'qet_quiz_titulo', '¿Podrías estar en una situación de trata?', 'text', '70' ),
				fiet_tagf( 'f_qet_qtit_tag', 'qet_quiz_titulo_tag', 'h2' ),
				fiet_f( 'f_qet_qpar', 'Cuestionario · Párrafo', 'qet_quiz_parrafo', 'Responde a estas preguntas para identificar posibles señales de alerta. El resultado es orientativo y no sustituye el asesoramiento profesional.', 'textarea' ),
				fiet_f( 'f_qet_qbtn', 'Cuestionario · Botón', 'qet_quiz_boton', 'Comprueba tu situación' ),
				fiet_f( 'f_qet_leye', 'Tres elementos · Sobretítulo', 'qet_el_eyebrow', 'Los tres elementos del delito' ),
				fiet_f( 'f_qet_ltit', 'Tres elementos · Título', 'qet_el_titulo', 'La existencia de estos tres elementos constituye el delito de trata.', 'textarea', '70' ),
				fiet_tagf( 'f_qet_ltit_tag', 'qet_el_titulo_tag', 'h2' ),
				fiet_f( 'f_qet_e1t', 'Elemento 1 · Título', 'qet_el1_titulo', 'La acción', 'text', '70' ),
				fiet_tagf( 'f_qet_e1t_tag', 'qet_el1_titulo_tag', 'h3' ),
				fiet_f( 'f_qet_e1d', 'Elemento 1 · Descripción', 'qet_el1_desc', 'Captación, transporte, traslado, acogida o recepción de personas.', 'textarea' ),
				fiet_f( 'f_qet_e2t', 'Elemento 2 · Título', 'qet_el2_titulo', 'Los medios', 'text', '70' ),
				fiet_tagf( 'f_qet_e2t_tag', 'qet_el2_titulo_tag', 'h3' ),
				fiet_f( 'f_qet_e2d', 'Elemento 2 · Descripción', 'qet_el2_desc', 'Engaño, abuso de una situación de vulnerabilidad, coacción o violencia.', 'textarea' ),
				fiet_f( 'f_qet_e3t', 'Elemento 3 · Título', 'qet_el3_titulo', 'El fin', 'text', '70' ),
				fiet_tagf( 'f_qet_e3t_tag', 'qet_el3_titulo_tag', 'h3' ),
				fiet_f( 'f_qet_e3d', 'Elemento 3 · Descripción', 'qet_el3_desc', 'La explotación de la persona para obtener un beneficio económico.', 'textarea' ),
				fiet_f( 'f_qet_tptit', 'Tipos de trata · Título', 'qet_tipos_titulo', 'Existen diferentes tipos de trata', 'textarea', '70' ),
				fiet_tagf( 'f_qet_tptit_tag', 'qet_tipos_titulo_tag', 'h2' ),
				fiet_f( 'f_qet_tp1', 'Tipo 1', 'qet_tipo1', 'Explotación sexual' ),
				fiet_f( 'f_qet_tp2', 'Tipo 2', 'qet_tipo2', 'Explotación laboral' ),
				fiet_f( 'f_qet_tp3', 'Tipo 3', 'qet_tipo3', 'Mendicidad forzada' ),
				fiet_f( 'f_qet_tp4', 'Tipo 4', 'qet_tipo4', 'Matrimonio forzado' ),
				fiet_f( 'f_qet_tp5', 'Tipo 5', 'qet_tipo5', 'Criminalidad forzada' ),
				fiet_f( 'f_qet_tp6', 'Tipo 6', 'qet_tipo6', 'Extracción de órganos' ),
			),
			'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => $que->ID ) ) ),
		) );
	}

	/* ===================== PORTADA · EL TELÉFONO ===================== */
	acf_add_local_field_group( array(
		'key'    => 'group_fiet_front',
		'title'  => 'El teléfono (portada) · Textos',
		'fields' => array(
			fiet_img_f( 'f_tel_img', 'tel_portada_img', 'Portada · Imagen', 'Imagen de fondo de la portada. Vacío = imagen por defecto del tema.' ),
			fiet_f( 'f_tel_eye', 'Sobretítulo', 'tel_eyebrow', 'Confidencial · Gratuito · Disponible 24/7 · Sin rastro en la factura', 'text', '70' ),
			fiet_tagf( 'f_tel_eye_tag', 'tel_eyebrow_tag', 'span' ),
			fiet_f( 'f_tel_sub', 'Subtítulo', 'tel_sub', 'El Teléfono de Ayuda Contra la Trata funciona 24/7 y está atendido por profesionales especializados que siguen protocolos internacionales para responder con rapidez y seguridad. Financiado y operado por la ONG FIET.', 'textarea' ),
			fiet_f( 'f_tel_hint', 'Indicador de scroll', 'tel_hint', 'Desplázate para llamar' ),
			fiet_f( 'f_tel_clab', 'Pantalla · Etiqueta', 'tel_call_label', 'Llamada saliente' ),
			fiet_f( 'f_tel_cnam', 'Pantalla · Nombre', 'tel_call_name', 'Teléfono contra la Trata' ),
			fiet_f( 'f_tel_tlab', 'Proceso · Título', 'tel_topic_label', '¿Qué pasa cuando llamas?' ),
			fiet_f( 'f_tel_t1', 'Proceso · Texto 1', 'tel_topic1', 'Una víctima o testigo contacta con el 900 759 759 a cualquier hora y en cualquier momento. Puede contactar en su idioma nativo si quiere...', 'textarea' ),
			fiet_f( 'f_tel_t2', 'Proceso · Texto 2', 'tel_topic2', 'Fiet incorpora un sistema de traducción automática de la llamada que permite a un profesional especializado entender a la víctima y comunicarse con ella en su idioma en tiempo real.', 'textarea' ),
			fiet_f( 'f_tel_t3', 'Proceso · Texto 3', 'tel_topic3', 'Una vez atendida a la víctima, se revisa en la base de datos si hubiese casos conectados.', 'textarea' ),
			fiet_f( 'f_tel_t4', 'Proceso · Texto 4', 'tel_topic4', 'Con esta información se procede a dar la respuesta más adecuada y segura.', 'textarea' ),
			fiet_f( 'f_tel_pill', 'Píldora', 'tel_pill', 'Traducción automática' ),
			fiet_f( 'f_tel_st1', 'Paso 1', 'tel_step1', 'Llamada de ayuda' ),
			fiet_f( 'f_tel_st2', 'Paso 2', 'tel_step2', 'Atención especializada' ),
			fiet_f( 'f_tel_st3', 'Paso 3', 'tel_step3', 'Revisión de datos' ),
			fiet_f( 'f_tel_st4', 'Paso 4', 'tel_step4', 'Plan de acción' ),
			fiet_f( 'f_tel_ptag', 'Popup · Sobretítulo', 'tel_pop_tag', 'Cómo contactar' ),
			fiet_f( 'f_tel_ptit', 'Popup · Título', 'tel_pop_titulo', 'Tres vías. Todas confidenciales.', 'text', '70' ),
			fiet_tagf( 'f_tel_ptit_tag', 'tel_pop_titulo_tag', 'h2' ),
			fiet_f( 'f_tel_ppar', 'Popup · Párrafo', 'tel_pop_parrafo', 'Hay una persona al otro lado. No te juzga, no comparte nada sin tu consentimiento (salvo obligación legal) y puedes permanecer en el anonimato. Elige el canal que te resulte más seguro.', 'textarea' ),
			fiet_f( 'f_tel_c1t', 'Card 1 · Título', 'tel_c1_titulo', 'Teléfono', 'text', '70' ),
			fiet_tagf( 'f_tel_c1t_tag', 'tel_c1_titulo_tag', 'h3' ),
			fiet_f( 'f_tel_c1d', 'Card 1 · Descripción', 'tel_c1_desc', 'Marca el 900 759 759. Gratuito, 24/7. Profesionales que hablan español e inglés, con interpretación en más de 200 idiomas.', 'textarea' ),
			fiet_f( 'f_tel_c1b', 'Card 1 · Botón', 'tel_c1_boton', 'Llamar' ),
			fiet_f( 'f_tel_c2t', 'Card 2 · Título', 'tel_c2_titulo', 'Formulario', 'text', '70' ),
			fiet_tagf( 'f_tel_c2t_tag', 'tel_c2_titulo_tag', 'h3' ),
			fiet_f( 'f_tel_c2d', 'Card 2 · Descripción', 'tel_c2_desc', 'Describe una situación de sospecha a través del formulario de contacto. Puedes hacerlo de forma anónima o dejar un contacto para que te llamen.', 'textarea' ),
			fiet_f( 'f_tel_c2b', 'Card 2 · Botón', 'tel_c2_boton', 'Abrir formulario' ),
			fiet_f( 'f_tel_c3t', 'Card 3 · Título', 'tel_c3_titulo', 'Correo', 'text', '70' ),
			fiet_tagf( 'f_tel_c3t_tag', 'tel_c3_titulo_tag', 'h3' ),
			fiet_f( 'f_tel_c3d', 'Card 3 · Descripción', 'tel_c3_desc', 'Escríbenos con los detalles de tu situación o tu consulta. Te responderá el equipo especializado.', 'textarea' ),
		),
		'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ),
	) );
} );
