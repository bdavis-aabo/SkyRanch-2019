<?php
/**
 * Export class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author  Envira Team
 */

namespace Envira\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Export class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author  Envira Team
 */
class Export {

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the base class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Export a gallery.
		add_action( 'admin_init', array( $this, 'export_gallery' ), 10, 2 );

	}

	/**
	 * Exports an Envira gallery.
	 *
	 * @since 1.0.0
	 *
	 * @return null Return early if failing proper checks to export the gallery.
	 */
	public function export_gallery() {

		if ( ! $this->has_exported_gallery() ) {
			return;
		}

		if ( ! $this->verify_exported_gallery() ) {
			return;
		}

		if ( ! $this->can_export_gallery() ) {
			return;
		}

		// Ignore the user aborting the action.
		ignore_user_abort( true );

		// Grab the proper data.
		$post_id = ( isset( $_POST['envira_post_id'] ) ) ? absint( $_POST['envira_post_id'] ) : false; // @codingStandardsIgnoreLine
		$data    = get_post_meta( $post_id, '_eg_gallery_data', true );

		// Append the in_gallery data checker to the data array.
		$data['in_gallery'] = get_post_meta( $post_id, '_eg_in_gallery', true );

		// Allow Addons to add to the Gallery export.
		$data = apply_filters( 'envira_gallery_export_gallery_data', $data, $post_id );

		// Set the proper headers.
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=envira-gallery-' . $post_id . '-' . date( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		// Make the settings downloadable to a JSON file and die.
		die( wp_json_encode( $data ) );

	}

	/**
	 * Helper method to determine if a gallery export is available.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if an exported gallery is available, false otherwise.
	 */
	public function has_exported_gallery() {

		return ! empty( $_POST['envira_export'] ); // @codingStandardsIgnoreLine

	}

	/**
	 * Helper method to determine if a gallery export nonce is valid and verified.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the nonce is valid, false otherwise.
	 */
	public function verify_exported_gallery() {

		return isset( $_POST['envira-gallery-export'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['envira-gallery-export'] ) ), 'envira-gallery-export' );

	}

	/**
	 * Helper method to determine if the user can actually export the gallery.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the user can export the gallery, false otherwise.
	 */
	public function can_export_gallery() {

		$manage_options = current_user_can( 'manage_options' );
		return apply_filters( 'envira_gallery_export_cap', $manage_options );

	}

}
