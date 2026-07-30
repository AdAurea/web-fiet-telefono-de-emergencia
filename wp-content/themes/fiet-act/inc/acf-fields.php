<?php
/**
 * Campos editables por página (ACF, grupos locales del tema).
 * La ubicación se resuelve por slug de página en tiempo de ejecución.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Atajo para definir un campo de texto/área */
function fiet_f( $key, $label, $name, $default = '', $type = 'text' ) {
	$f = array( 'key' => $key, 'label' => $label, 'name' => $name, 'type' => $type, 'default_value' => $default );
	if ( $type === 'textarea' ) { $f['rows'] = 3; $f['new_lines'] = ''; }
	return $f;
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
				fiet_f( 'f_prev_eye', 'Portada · Sobretítulo', 'prev_eyebrow', 'Prevención' ),
				fiet_f( 'f_prev_tit', 'Portada · Título', 'prev_titulo', '¿Cómo mantenerse a salvo?' ),
				fiet_f( 'f_prev_par', 'Portada · Párrafo', 'prev_parrafo', 'La trata puede comenzar en situaciones cotidianas como la búsqueda de empleo, un viaje o el uso de internet. Conocer los riesgos y saber identificarlos es clave para protegerte.', 'textarea' ),
				fiet_f( 'f_prev_btn', 'Portada · Botón', 'prev_boton', 'Ver recomendaciones' ),
				fiet_f( 'f_prev_peye', 'Popup · Sobretítulo', 'prev_pop_eyebrow', 'Prevención' ),
				fiet_f( 'f_prev_ptit', 'Popup · Título', 'prev_pop_titulo', 'Recomendaciones para mantenerte seguro' ),
				fiet_f( 'f_prev_ppar', 'Popup · Párrafo', 'prev_pop_parrafo', 'La trata puede empezar en un empleo, un viaje o en internet. Estas son las claves para reducir riesgos e identificar señales de alerta en cada situación.', 'textarea' ),
				fiet_f( 'f_prev_c1t', 'Card 1 · Título', 'prev_card1_titulo', 'Empleo Seguro' ),
				fiet_f( 'f_prev_c1d', 'Card 1 · Descripción', 'prev_card1_desc', 'Verifica la oferta y a quien contrata, nunca entregues tus documentos y comparte con alguien de confianza dónde y con quién vas a trabajar.', 'textarea' ),
				fiet_f( 'f_prev_c2t', 'Card 2 · Título', 'prev_card2_titulo', 'Viaje Seguro' ),
				fiet_f( 'f_prev_c2d', 'Card 2 · Descripción', 'prev_card2_desc', 'Lleva copias de tus documentos, comparte tu itinerario y ten a mano los contactos de tu embajada y de organizaciones de ayuda.', 'textarea' ),
				fiet_f( 'f_prev_c3t', 'Card 3 · Título', 'prev_card3_titulo', 'Internet Seguro' ),
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
				fiet_f( 'f_rec_eye', 'Portada · Sobretítulo', 'rec_eyebrow', 'Recursos' ),
				fiet_f( 'f_rec_tit', 'Portada · Título', 'rec_titulo', 'Recursos y servicios.' ),
				fiet_f( 'f_rec_par', 'Portada · Párrafo', 'rec_parrafo', 'El Teléfono ACT ofrece formaciones gratuitas a profesionales y sectores con mayor riesgo de detectar situaciones de trata, además de una amplia red de derivación y materiales especializados.', 'textarea' ),
				fiet_f( 'f_rec_btn', 'Portada · Botón', 'rec_boton', 'Ver formaciones' ),
				fiet_f( 'f_rec_stit', 'Sección servicios · Título', 'rec_serv_titulo', 'Una amplia gama de servicios a tu disposición' ),
				fiet_f( 'f_rec_s1t', 'Servicio 1 · Título', 'rec_serv1_titulo', 'Derivaciones' ),
				fiet_f( 'f_rec_s1d', 'Servicio 1 · Descripción', 'rec_serv1_desc', 'Conectamos a quien llama con servicios especializados: gestión de casos, alojamiento seguro, transporte, asistencia legal y apoyo psicológico y de salud mental.', 'textarea' ),
				fiet_f( 'f_rec_s2t', 'Servicio 2 · Título', 'rec_serv2_titulo', 'Formación a profesionales' ),
				fiet_f( 'f_rec_s2d', 'Servicio 2 · Descripción', 'rec_serv2_desc', 'Formación y asistencia técnica a fuerzas de seguridad, profesionales sanitarios, personal aeroportuario y organismos públicos. Fortalecemos protocolos locales y nacionales.', 'textarea' ),
				fiet_f( 'f_rec_s3t', 'Servicio 3 · Título', 'rec_serv3_titulo', 'Informar una sospecha' ),
				fiet_f( 'f_rec_s3d', 'Servicio 3 · Descripción', 'rec_serv3_desc', 'Recibimos información sobre posibles situaciones de trata. Todas las comunicaciones son confidenciales y la persona puede permanecer en el anonimato.', 'textarea' ),
				fiet_f( 'f_rec_s4t', 'Servicio 4 · Título', 'rec_serv4_titulo', 'Asistencia a víctimas' ),
				fiet_f( 'f_rec_s4d', 'Servicio 4 · Descripción', 'rec_serv4_desc', 'Apoyo en crisis mediante planes de seguridad, acompañamiento emocional y conexión con servicios de emergencia y entidades especializadas.', 'textarea' ),
				fiet_f( 'f_rec_s5t', 'Servicio 5 · Título', 'rec_serv5_titulo', 'Verificación de empleo' ),
				fiet_f( 'f_rec_s5d', 'Servicio 5 · Descripción', 'rec_serv5_desc', 'Servicio gratuito de verificación de ofertas de empleo. Revisamos el registro de la empresa, antecedentes y opiniones, y elaboramos una evaluación de riesgo.', 'textarea' ),
				fiet_f( 'f_rec_peye', 'Popup · Sobretítulo', 'rec_pop_eyebrow', 'Recursos' ),
				fiet_f( 'f_rec_ptit', 'Popup · Título', 'rec_pop_titulo', 'Formación especializada.' ),
				fiet_f( 'f_rec_pp1', 'Popup · Párrafo 1', 'rec_pop_p1', 'Proporcionamos conocimientos clave sobre la magnitud y las formas de la trata, los indicadores específicos según cada ámbito profesional y los protocolos de actuación necesarios en situaciones de sospecha o identificación.', 'textarea' ),
				fiet_f( 'f_rec_pp2', 'Popup · Párrafo 2', 'rec_pop_p2', 'Contamos con una amplia base de datos de organizaciones que imparten formaciones, campañas y charlas en todo el territorio, además de un catálogo de materiales especializados.', 'textarea' ),
				fiet_f( 'f_rec_sec1', 'Sector 1 · Título', 'rec_sec1_titulo', 'Sector sanitario' ),
				fiet_f( 'f_rec_sec2', 'Sector 2 · Título', 'rec_sec2_titulo', 'Sector hostelero' ),
				fiet_f( 'f_rec_sec3', 'Sector 3 · Título', 'rec_sec3_titulo', 'Sector transporte' ),
				fiet_f( 'f_rec_sec4', 'Sector 4 · Título', 'rec_sec4_titulo', 'Sector consular' ),
				fiet_f( 'f_rec_sec5', 'Sector 5 · Título', 'rec_sec5_titulo', 'Sector educativo' ),
				fiet_f( 'f_rec_mat', 'Sectores · Texto del enlace', 'rec_mat_boton', 'Descarga el material' ),
				fiet_f( 'f_rec_foot', 'Popup · Párrafo final', 'rec_pop_foot', 'Nuestro equipo de especialistas ofrece orientación para la elaboración e implementación de protocolos de actuación. Contáctanos para más información o asesoramiento específico.', 'textarea' ),
			),
			'location' => array( array( array( 'param' => 'page', 'operator' => '==', 'value' => $rec->ID ) ) ),
		) );
	}
} );
