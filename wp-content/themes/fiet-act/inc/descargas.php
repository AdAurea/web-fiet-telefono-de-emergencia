<?php
/**
 * Descargas de material con captación de nombre + correo.
 * - Modal en Recursos que pide nombre y correo antes de dar la descarga.
 * - Registro en tabla propia (nombre, correo, sector, fecha).
 * - Cada correo solo puede descargar cada material una vez.
 * - Página "Descargas" en el admin con borrado individual y en lote.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Sectores de material: clave => etiqueta legible. La URL vive en la opción rec_url_{clave}. */
function fiet_descargas_sectores() {
	return array(
		'sanitario'  => 'Sector sanitario',
		'hostelero'  => 'Sector hostelero',
		'transporte' => 'Sector transporte',
		'consular'   => 'Sector consular',
		'educativo'  => 'Sector educativo',
		'tercer_sector' => 'Tercer Sector',
		// Guías descargables de la página Prevención (mismo flujo de captación).
		'guia_empleo'          => 'Guía de empleo preventivo',
		'guia_digital_menores' => 'Guía de seguridad digital para menores',
		'guia_digital_adultos' => 'Guía de seguridad digital para adultos',
	);
}

function fiet_descargas_table() {
	global $wpdb;
	return $wpdb->prefix . 'fiet_descargas';
}

/** Crea/actualiza la tabla. Se ejecuta al activar el tema y bajo demanda si falta. */
function fiet_descargas_install() {
	global $wpdb;
	$table   = fiet_descargas_table();
	$charset = $wpdb->get_charset_collate();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$sql = "CREATE TABLE $table (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		nombre VARCHAR(191) NOT NULL,
		correo VARCHAR(191) NOT NULL,
		sector VARCHAR(191) NOT NULL,
		sector_key VARCHAR(64) NOT NULL,
		url TEXT NULL,
		ip VARCHAR(64) NULL,
		consentimiento TINYINT(1) NOT NULL DEFAULT 0,
		creado DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY correo (correo),
		KEY sector_key (sector_key)
	) $charset;";
	dbDelta( $sql );
	update_option( 'fiet_descargas_db', '2' );
}
add_action( 'after_switch_theme', 'fiet_descargas_install' );
add_action( 'admin_init', function () {
	if ( get_option( 'fiet_descargas_db' ) !== '2' ) fiet_descargas_install();
} );

/** IP del cliente (best-effort). */
function fiet_descargas_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	return sanitize_text_field( substr( $ip, 0, 64 ) );
}

/* ============================ Front-end (AJAX) ============================ */

add_action( 'wp_ajax_fiet_descarga', 'fiet_descargas_ajax' );
add_action( 'wp_ajax_nopriv_fiet_descarga', 'fiet_descargas_ajax' );
function fiet_descargas_ajax() {
	check_ajax_referer( 'fiet_descarga', 'nonce' );

	$sectores = fiet_descargas_sectores();
	$key    = isset( $_POST['sector'] ) ? sanitize_key( $_POST['sector'] ) : '';
	$nombre  = isset( $_POST['nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['nombre'] ) ) : '';
	$correo  = isset( $_POST['correo'] ) ? sanitize_email( wp_unslash( $_POST['correo'] ) ) : '';
	$consent = ! empty( $_POST['consent'] );

	if ( ! isset( $sectores[ $key ] ) )      wp_send_json_error( array( 'msg' => 'Material no válido.' ) );
	if ( $nombre === '' )                    wp_send_json_error( array( 'msg' => 'Introduce tu nombre.' ) );
	if ( ! is_email( $correo ) )             wp_send_json_error( array( 'msg' => 'Introduce un correo electrónico válido.' ) );
	if ( ! $consent )                        wp_send_json_error( array( 'msg' => 'Debes aceptar la política de privacidad para continuar.' ) );

	$url = fiet_option( 'rec_url_' . $key, '' );
	if ( ! $url ) wp_send_json_error( array( 'msg' => 'Este material aún no está disponible.' ) );

	global $wpdb;
	$table = fiet_descargas_table();

	// Un correo solo puede descargar cada material una vez.
	$ya = $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM $table WHERE correo = %s AND sector_key = %s LIMIT 1",
		$correo, $key
	) );
	if ( $ya ) {
		wp_send_json_error( array(
			'code' => 'dup',
			'msg'  => 'Este correo ya ha descargado este material anteriormente.',
		) );
	}

	$wpdb->insert(
		$table,
		array(
			'nombre'     => $nombre,
			'correo'     => $correo,
			'sector'     => $sectores[ $key ],
			'sector_key' => $key,
			'url'            => esc_url_raw( $url ),
			'ip'             => fiet_descargas_ip(),
			'consentimiento' => 1,
			'creado'         => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
	);

	wp_send_json_success( array( 'url' => esc_url_raw( $url ) ) );
}

/* ============================ Admin: página "Descargas" ============================ */

add_action( 'admin_menu', function () {
	add_menu_page(
		'Descargas', 'Descargas', 'manage_options',
		'fiet-descargas', 'fiet_descargas_render', 'dashicons-download', 60
	);
} );

function fiet_descargas_render() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}
	$table = new FIET_Descargas_List_Table();
	$table->prepare_items();

	echo '<div class="wrap"><h1 class="wp-heading-inline">Descargas de material</h1>';
	echo '<p style="max-width:60em;color:#50575e;">Registro de personas que han descargado material formativo. Cada correo puede descargar cada material una sola vez.</p>';
	if ( ! empty( $table->notice ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $table->notice ) . '</p></div>';
	}
	echo '<form method="post">';
	echo '<input type="hidden" name="page" value="fiet-descargas" />';
	$table->display();
	echo '</form></div>';
}

// WP_List_Table solo está disponible en el admin; declaramos la clase ahí.
if ( is_admin() ) :

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class FIET_Descargas_List_Table extends WP_List_Table {
	public $notice = '';

	public function __construct() {
		parent::__construct( array(
			'singular' => 'descarga',
			'plural'   => 'descargas',
			'ajax'     => false,
		) );
	}

	public function get_columns() {
		return array(
			'cb'     => '<input type="checkbox" />',
			'nombre' => 'Nombre',
			'correo' => 'Correo',
			'sector' => 'Sector',
			'creado' => 'Fecha',
		);
	}

	public function get_sortable_columns() {
		return array(
			'nombre' => array( 'nombre', false ),
			'correo' => array( 'correo', false ),
			'sector' => array( 'sector', false ),
			'creado' => array( 'creado', true ),
		);
	}

	public function no_items() {
		echo 'Aún no hay descargas registradas.';
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d" />', (int) $item['id'] );
	}

	public function column_default( $item, $column ) {
		return isset( $item[ $column ] ) ? esc_html( $item[ $column ] ) : '';
	}

	public function column_creado( $item ) {
		return esc_html( mysql2date( 'd/m/Y H:i', $item['creado'] ) );
	}

	public function column_nombre( $item ) {
		$url = wp_nonce_url(
			add_query_arg( array( 'page' => 'fiet-descargas', 'action' => 'delete', 'id' => (int) $item['id'] ), admin_url( 'admin.php' ) ),
			'fiet_del_' . (int) $item['id']
		);
		$actions = array(
			'delete' => sprintf(
				'<a href="%s" onclick="return confirm(\'¿Eliminar este registro?\');" style="color:#b32d2e;">Eliminar</a>',
				esc_url( $url )
			),
		);
		return sprintf( '<strong>%s</strong>%s', esc_html( $item['nombre'] ), $this->row_actions( $actions ) );
	}

	public function get_bulk_actions() {
		return array( 'bulk-delete' => 'Eliminar' );
	}

	protected function process_actions() {
		global $wpdb;
		$table   = fiet_descargas_table();
		$deleted = 0;

		$action = $this->current_action();

		if ( 'delete' === $action ) {
			$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
			if ( $id && check_admin_referer( 'fiet_del_' . $id ) && current_user_can( 'manage_options' ) ) {
				$deleted = (int) $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
			}
		} elseif ( 'bulk-delete' === $action ) {
			check_admin_referer( 'bulk-' . $this->_args['plural'] );
			if ( current_user_can( 'manage_options' ) ) {
				$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();
				$ids = array_filter( $ids );
				if ( $ids ) {
					$in      = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
					$deleted = (int) $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id IN ($in)", $ids ) );
				}
			}
		}

		if ( $deleted > 0 ) {
			$this->notice = sprintf( '%d registro(s) eliminado(s).', $deleted );
		}
	}

	public function prepare_items() {
		$this->process_actions();

		global $wpdb;
		$table = fiet_descargas_table();

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page = 20;
		$paged    = $this->get_pagenum();
		$offset   = ( $paged - 1 ) * $per_page;

		$allowed_orderby = array( 'nombre', 'correo', 'sector', 'creado' );
		$orderby = ( isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], $allowed_orderby, true ) ) ? $_GET['orderby'] : 'creado';
		$order   = ( isset( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'asc' ) ? 'ASC' : 'DESC';

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table ORDER BY $orderby $order LIMIT %d OFFSET %d",
				$per_page, $offset
			),
			ARRAY_A
		);

		$this->set_pagination_args( array(
			'total_items' => $total,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total / $per_page ),
		) );
	}
}

endif; // is_admin()
