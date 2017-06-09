<?php

/**
 * Import Fusion elements/templates
 */
function fusion_builder_importer() {

	check_ajax_referer( 'fusion_import_nonce', 'fusion_import_nonce' );

	if ( isset( $_FILES ) && '' != $_FILES[0] ) {

		$file = $_FILES[0]['tmp_name'];

		if ( current_user_can( 'manage_options' ) ) {

			// we are loading importers.
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true );
			}

			// if main importer class doesn't exist.
			if ( ! class_exists( 'WP_Importer' ) ) {
				$wp_importer = wp_normalize_path( ABSPATH . '/wp-admin/includes/class-wp-importer.php' );
				include $wp_importer;
			}

			if ( ! class_exists( 'WP_Import' ) ) {
				$wp_import = wp_normalize_path( FUSION_BUILDER_PLUGIN_DIR . '/inc/importer/wordpress-importer.php' );
				include $wp_import;
			}

			// check for main import class and wp import class.
			if ( class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) {

				if ( isset( $file ) && ! empty( $file ) ) {

					$importer = new WP_Import();

					// Import data.
					$importer->fetch_attachments = true;
					ob_start();
					$importer->import( $file );
					ob_end_clean();

				}

				exit;
			}
		}
	}

	die();
}
add_action( 'wp_ajax_fusion_builder_importer', 'fusion_builder_importer' );


/**
 * Export Fusion elements/templates
 */
function fusion_export_xml() {

	if ( isset( $_GET['page'] ) && 'fusion-builder-options' == $_GET['page'] ) {

		$action = filter_input( INPUT_GET, 'fusion_action', FILTER_SANITIZE_STRING );
		$post_type = filter_input( INPUT_GET, 'fusion_export_type', FILTER_SANITIZE_STRING );

		if ( 'export' == $action ) {

			if ( isset( $post_type ) && ! empty( $post_type ) ) {

				if ( current_user_can( 'export' ) ) {

					/** Load WordPress export API */
					require_once wp_normalize_path( ABSPATH . 'wp-admin/includes/export.php' );

					$args = array( 'content' => $post_type );
					export_wp( $args );
					exit();
				}
			}
		}
	}
}
add_action( 'admin_init', 'fusion_export_xml' );

/**
 * Export Filename for elements/templates
 *
 * @param string $wp_filename Export file name.
 * @return string $wp_filename New export file name depends on the post type
 */
function fusion_export_filename( $wp_filename ) {

	if ( isset( $_GET['page'] ) && 'fusion-builder-options' == $_GET['page'] ) {

		$post_type = filter_input( INPUT_GET, 'fusion_export_type', FILTER_SANITIZE_STRING );
		$wp_filename = $post_type . '-' . $wp_filename;
		return $wp_filename;
	}
}
add_filter( 'export_wp_filename', 'fusion_export_filename' );
