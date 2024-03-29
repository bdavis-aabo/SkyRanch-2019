<?php
/**
 * Import class.
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
 * Import class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery
 * @author  Envira Team
 */
class Import {

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
	 * Holds any plugin error messages.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $errors = array();

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Import a gallery.
		$this->import_gallery();

		// Import a gallery via Envira Import.
		add_action( 'init', array( $this, 'import_gallery' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );

		// WordPress XML Import.
		add_action( 'import_post_meta', array( $this, 'change_xml_import' ), 10, 3 );

	}

	/**
	 * Hooks into XML Import
	 *
	 * @since 1.0.0
	 * @param int    $post_id Post ID.
	 * @param string $key Gallery Key.
	 * @param string $value Gallery Value.
	 * @return null
	 */
	public function change_xml_import( $post_id, $key, $value ) {

		if ( '_eg_gallery_data' !== $key || empty( $value ) || empty( $value['gallery'] ) ) {
			return;
		}

		// we should be changing the url in the meta_data.
		foreach ( $value['gallery'] as $id => $item_data ) {
			$value['gallery'][ $id ]['src']  = $this->parse_import_url( $value['gallery'][ $id ]['src'] );
			$value['gallery'][ $id ]['link'] = $this->parse_import_url( $value['gallery'][ $id ]['link'] );
		}

		// update the meta data.
		update_post_meta( $post_id, $key, $value );

	}

	/**
	 * Change an old Envira url to the new one
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL.
	 * @return null $new_url
	 */
	public function parse_import_url( $url ) {

		$parsed_src_array = explode( '/', $url );
		$old_wp_url       = array();
		foreach ( $parsed_src_array as $segment ) {
			if ( 'wp-content' === $segment ) {
				break;
			} else {
				$old_wp_url[] = $segment;
			}
		}
		$old_wp_url_string            = trailingslashit( implode( '/', $old_wp_url ) );
		$new_wp_url_string_no_content = trailingslashit( str_replace( 'wp-content', '', content_url() ) );
		return ( str_replace( $old_wp_url_string, $new_wp_url_string_no_content, $url ) );

	}

	/**
	 * Imports an Envira gallery.
	 *
	 * @since 1.0.0
	 *
	 * @return null Return early (possibly setting errors) if failing proper checks to import the gallery.
	 */
	public function import_gallery() {

		if ( ! $this->has_imported_gallery() ) {
			return;
		}

		if ( ! $this->verify_imported_gallery() ) {
			return;
		}

		if ( ! $this->can_import_gallery() ) {
			$this->errors[] = __( 'Sorry, but you lack the permissions to import a gallery to this post.', 'envira-gallery' );
			return;
		}

		if ( ! $this->post_can_handle_gallery() ) {
			$this->errors[] = __( 'Sorry, but the post ID you are attempting to import the gallery to cannot handle a gallery.', 'envira-gallery' );
			return;
		}

		if ( ! $this->has_imported_gallery_files() ) {
			$this->errors[] = __( 'Sorry, but there are no files available to import a gallery.', 'envira-gallery' );
			$this->errors   = array_unique( $this->errors );
			return;
		}

		if ( ! $this->has_correct_filename() ) {
			$this->errors[] = __( 'Sorry, but you have attempted to upload a gallery import file with an incompatible filename. Envira Gallery import files must begin with "envira-gallery".', 'envira-gallery' );
			return;
		}

		if ( ! $this->has_json_extension() ) {
			$this->errors[] = __( 'Sorry, but Envira Gallery import files must be in <code>.json</code> format.', 'envira-gallery' );
			return;
		}

		// Retrieve the JSON contents of the file. If that fails, return an error.
		$contents = $this->get_file_contents();
		if ( ! $contents ) {
			$this->errors[] = __( 'Sorry, but there was an error retrieving the contents of the gallery export file. Please try again.', 'envira-gallery' );
			return;
		}

		// Decode the settings and start processing.
		$data    = json_decode( $contents, true );
		$post_id = isset( $_POST['envira_post_id'] ) ? absint( $_POST['envira_post_id'] ) : null; // @codingStandardsIgnoreLine

		// If the post is an auto-draft (new post), make sure to save as draft first before importing.
		$this->maybe_save_draft( $post_id );

		// Delete any previous gallery data (if any) from the post that is receiving the new gallery.
		$this->remove_existing_gallery( $post_id );

		// Update the ID in the gallery data to point to the new post.
		$data['id'] = $post_id;

		// If the wp_generate_attachment_metadata function does not exist, load it into memory because we will need it.
		$this->load_metadata_function();

		// Prepare import.
		$this->prepare_import();

		// Import the gallery.
		$gallery = $this->run_import( $data, $post_id );

		// Cleanup import.
		$this->cleanup_import();

		// Update the in_gallery checker for the post that is receiving the gallery.
		update_post_meta( $post_id, '_eg_in_gallery', $gallery['in_gallery'] );

		// Unset any unncessary data from the final gallery holder.
		unset( $gallery['in_gallery'] );

		// Update the post title and slug itself.
		$gallery_post = array(
			'ID' => $post_id,
		);

		// Add action so third party plugins can do things while importing (like tags addon adding categories).
		do_action( 'envira_import_gallery_end', $data, $post_id );

		// Add filter in case developer or plugin wants to modify the imported data.
		$gallery_post = apply_filters( 'envira_import_gallery_post_data', $gallery_post );
		$gallery      = apply_filters( 'envira_import_gallery_metadata', $gallery );

		// Update the post into the database.
		wp_update_post( $gallery_post );

		// Update the meta for the post that is receiving the gallery.
		update_post_meta( $post_id, '_eg_gallery_data', $gallery );

	}

	/**
	 * Loops through the data provided and imports items into the gallery.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data     Array of gallery data being imported.
	 * @param int   $post_id    The post ID the gallery is being imported to.
	 * @return array $gallery Modified gallery data based on imports.
	 */
	public function run_import( $data, $post_id ) {

		// Prepare variables.
		$gallery = false;
		$i       = 0;

		// Loop through the gallery items and import each item individually.
		foreach ( (array) $data['gallery'] as $id => $item ) {
			// If just starting, use the base data imported. Otherwise, use the updated data after each import.
			if ( 0 === $i ) {
				$gallery = $this->import_gallery_item( $id, $item, $data, $post_id );
			} else {
				$gallery = $this->import_gallery_item( $id, $item, $gallery, $post_id );
			}

			// Increment the iterator.
			$i++;
		}

		// Resort gallery if we have the values.
		if ( ! empty( $data['config']['sort_order'] ) && ! empty( $data['config']['sorting_direction'] ) ) {
			$gallery = envira_sort_gallery( $gallery, $data['config']['sort_order'], $data['config']['sorting_direction'] );
		}

		// Return the newly imported gallery data.
		return $gallery;

	}

	/**
	 * Imports an individual item into a gallery.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id        The image attachment ID from the import file.
	 * @param array $item    Data for the item being imported.
	 * @param array $data Array of gallery data being imported.
	 * @param int   $post_id   The post ID the gallery is being imported to.
	 * @return array $data   Modified gallery data based on import status of image.
	 */
	public function import_gallery_item( $id, $item, $data, $post_id ) {

		// If no image data was found, the image doesn't exist on the server.
		$image = wp_get_attachment_image_src( $id );
		if ( ! $image ) {
			// We need to stream our image from a remote source.
			if ( empty( $item['src'] ) && empty( $item['link'] ) ) {
				$this->errors[] = __( 'No valid URL or link found for the image ID #', 'envira-gallery' ) . $id . '.';

				// Unset it from the gallery data for meta saving.
				$data = $this->purge_image_from_gallery( $id, $data );
			} else {
				// Stream the image from a remote URL.
				if ( ! empty( $item['src'] ) ) {
					$data = $this->import_remote_image( $item['src'], $data, $item, $post_id, $id );
				} elseif ( ! empty( $item['link'] ) ) {
					$data = $this->import_remote_image( $item['link'], $data, $item, $post_id, $id );
				}
			}
		} else {
			// The image already exists. If the URLs don't match, stream the image into the gallery.
			if ( $image[0] !== $item['src'] ) {
				// Stream the image from a remote URL.
				$data = $this->import_remote_image( $item['src'], $data, $item, $post_id, $id );
			} else {
				// The URLs match. We can simply update data and continue.
				$this->update_gallery_checker( $attach_id, $post_id );
			}
		}

		// Return the modified gallery data.
		return apply_filters( 'envira_gallery_imported_image_data', $data, $id, $item, $post_id );

	}

	/**
	 * Helper method to stream and import an image from a remote URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src       The URL of the remote image to stream and import.
	 * @param array  $data       The data to use for importing the remote image.
	 * @param array  $item       The gallery image item to import.
	 * @param int    $post_id      The post ID receiving the remote image.
	 * @param int    $id           The image attachment ID to target (if available).
	 * @param bool   $stream_only Whether or not to only stream and import or actually add to gallery.
	 * @return array $data      Data with updated import information.
	 */
	public function import_remote_image( $src, $data, $item, $post_id, $id = 0, $stream_only = false ) {

		// Prepare variables.
		$stream   = wp_remote_get( $src, array( 'timeout' => 60 ) );
		$type     = wp_remote_retrieve_header( $stream, 'content-type' );
		$filename = basename( $src );
		$fileinfo = pathinfo( $filename );

		// If the filename doesn't have an extension on it, determine the filename to use to save this image to the Media Library
		// This fixes importing URLs with no file extension e.g. http://placehold.it/300x300 (which is a PNG).
		if ( ! isset( $fileinfo['extension'] ) || empty( $fileinfo['extension'] ) ) {
			switch ( $type ) {
				case 'image/jpeg':
					$filename = $filename . '.jpeg';
					break;
				case 'image/jpg':
					$filename = $filename . '.jpg';
					break;
				case 'image/gif':
					$filename = $filename . '.gif';
					break;
				case 'image/png':
					$filename = $filename . '.png';
					break;
			}
		}

		// If we cannot get the image or determine the type, skip over the image.
		if ( is_wp_error( $stream ) ) {
			if ( $id ) {
				$data = $this->purge_image_from_gallery( $id, $data );
			}

			// If only streaming, return the error.
			if ( $stream_only ) {
				return $stream;
			}
		} elseif ( ! $type || strpos( $type, 'text/html' ) !== false ) {
			// Unset it from the gallery data for meta saving.
			if ( $id ) {
				$data = $this->purge_image_from_gallery( $id, $data );
			}

			// If only streaming, return the error.
			if ( $stream_only && isset( $stream['response']['code'] ) && 401 === $stream['response']['code'] ) {
				return envira_wp_error( 'envira_gallery_import_remote_image_error', __( '401 Error (Unauthorized) when attempting to retrieve  ', 'envira-gallery' ) . $src . '.' . __( ' This site might be behind a firewall or password-protected area.', 'envira-gallery' ) );
			} elseif ( $stream_only ) {
				return envira_wp_error( 'envira_gallery_import_remote_image_error', __( 'Could not retrieve a valid image from the URL ', 'envira-gallery' ) . $src . '.' );
			}
		} else {
			// It is an image. Stream the image.
			$mirror = wp_upload_bits( $filename, null, wp_remote_retrieve_body( $stream ) );

			// If there is an error, bail.
			if ( ! empty( $mirror['error'] ) ) {
				// Unset it from the gallery data for meta saving.
				if ( $id ) {
					$data = $this->purge_image_from_gallery( $id, $data );
				}

				// If only streaming, return the error.
				if ( $stream_only ) {
					return new \ WP_Error( 'envira_gallery_import_remote_image_error', $mirror['error'] );
				}
			} else {
				// Check if the $item has title, caption, alt specified
				// If so, store those values against the attachment so they're included in the Gallery
				// If not, fallback to the defaults.
				$attachment = array(
					'post_title'     => ( ( isset( $item['title'] ) && ! empty( $item['title'] ) ) ? $item['title'] : urldecode( $filename ) ), // Title.
					'post_mime_type' => $type,
					'post_excerpt'   => ( ( isset( $item['caption'] ) && ! empty( $item['caption'] ) ) ? $item['caption'] : '' ), // Caption.
				);
				$attach_id  = wp_insert_attachment( $attachment, $mirror['file'], $post_id );
				if ( ( isset( $item['alt'] ) && ! empty( $item['alt'] ) ) ) {
					update_post_meta( $attach_id, '_wp_attachment_image_alt', $item['alt'] );
				}

				// Generate and update attachment metadata.
				if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
					require ABSPATH . 'wp-admin/includes/image.php';
				}

				// Generate and update attachment metadata.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				// Unset it from the gallery data for meta saving now that we have a new image in its place.
				if ( $id ) {
					$data = $this->purge_image_from_gallery( $id, $data );
				}

				// Add the attachment id to the $mirror result.
				$mirror['attachment_id'] = $attach_id;

				// If only streaming and importing the image from the remote source, return it now.
				if ( $stream_only ) {
					return apply_filters( 'envira_gallery_remote_image_import_only', $mirror, $attach_data, $attach_id );
				}

				// Add the new attachment ID to the in_gallery checker.
				$data['in_gallery'][] = $attach_id;

				// Now update the attachment reference checker.
				$this->update_gallery_checker( $attach_id, $post_id );

				// Get the imported image URL.
				$image_url = wp_get_attachment_image_src( $attach_id, 'full' );

				// Update the image's src, to reflects its new URL
				// Maybe update the image's link, if the original image's link is an image
				// This ensures that links to pages, videos etc do not get 'lost' in the import
				// process.
				if ( $item['src'] === $item['link'] ) {
					$item['src']  = $image_url[0];
					$item['link'] = $image_url[0];
				} else {
					// Just update the src.
					$item['src'] = $image_url[0];
				}

				// Add the new attachment to the gallery.
				$data = $this->update_attachment_meta( $data, $attach_id, $item );
			}
		}

		// Return the remote image import data.
		return apply_filters( 'envira_gallery_remote_image_import', $data, $src, $id );

	}

	/**
	 * Purge image data from a gallery.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id      The image attachment ID to target for purging.
	 * @param array $data  The data to purge.
	 * @return array $data Purged data.
	 */
	public function purge_image_from_gallery( $id, $data ) {

		// Remove the image ID from the gallery data.
		unset( $data['gallery'][ $id ] );
		if ( isset( $data['in_gallery'] ) ) {
			$key = array_search( $id, (array) $data['in_gallery'], true );
			if ( false !== $key ) {
				unset( $data['in_gallery'][ $key ] );
			}
		}

		// Return the purged data.
		return apply_filters( 'envira_gallery_image_purged', $data, $id );

	}

	/**
	 * Update the attachment with a reference to the gallery that
	 * it has been assigned to.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attach_id The image attachment ID to target.
	 * @param int $post_id   The post ID the attachment should reference.
	 */
	public function update_gallery_checker( $attach_id, $post_id ) {

		$has_gallery = get_post_meta( $attach_id, '_eg_has_gallery', true );
		if ( empty( $has_gallery ) ) {
			$has_gallery = array();
		}

		$has_gallery[] = $post_id;
		update_post_meta( $attach_id, '_eg_has_gallery', $has_gallery );

	}

	/**
	 * Update the image metadata for Envira.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data       The data to use for importing the remote image.
	 * @param int   $attach_id  The image attachment ID to target.
	 * @param array $item       The original image item with metadata.
	 * @return array    $data       Data with updated meta information.
	 */
	public function update_attachment_meta( $data, $attach_id, $item ) {

		return envira_gallery_ajax_prepare_gallery_data( $data, $attach_id, $item );

	}

	/**
	 * Determines if a gallery import is available.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if an imported gallery is available, false otherwise.
	 */
	public function has_imported_gallery() {

		return ! empty( $_POST['envira_import'] ); // @codingStandardsIgnoreLine

	}

	/**
	 * Determines if a gallery import nonce is valid and verified.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the nonce is valid, false otherwise.
	 */
	public function verify_imported_gallery() {

		return isset( $_POST['envira-gallery-import'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['envira-gallery-import'] ) ), 'envira-gallery-import' );

	}

	/**
	 * Determines if the user can actually import the gallery.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the user can import the gallery, false otherwise.
	 */
	public function can_import_gallery() {

		$manage_options = current_user_can( 'manage_options' );
		return apply_filters( 'envira_gallery_import_cap', $manage_options );

	}

	/**
	 * Determines if the post ID can handle a gallery (revision or not).
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the post ID is not a revision, false otherwise.
	 */
	public function post_can_handle_gallery() {

		return isset( $_POST['envira_post_id'] ) && ! wp_is_post_revision( $_POST['envira_post_id'] ); // @codingStandardsIgnoreLine

	}

	/**
	 * Determines if gallery import files are available.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the imported gallery files are available, false otherwise.
	 */
	public function has_imported_gallery_files() {

		return ! empty( $_FILES['envira_import_gallery']['name'] ) || ! empty( $_FILES['envira_import_gallery']['tmp_name'] );

	}

	/**
	 * Determines if a gallery import file has a proper filename.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the imported gallery file has a proper filename, false otherwise.
	 */
	public function has_correct_filename() {

		return preg_match( '#^envira-gallery#i', wp_unslash( $_FILES['envira_import_gallery']['name'] ) ); // @codingStandardsIgnoreLine

	}

	/**
	 * Determines if a gallery import file has a proper file extension.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the imported gallery file has a proper file extension, false otherwise.
	 */
	public function has_json_extension() {

		$file_array = isset( $_FILES['envira_import_gallery']['name'] ) ? explode( '.', $_FILES['envira_import_gallery']['name'] ) : null; // @codingStandardsIgnoreLine
		$extension  = end( $file_array );
		return 'json' === $extension;

	}

	/**
	 * Retrieve the contents of the imported gallery file.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool JSON contents string if successful, false otherwise.
	 */
	public function get_file_contents() {

		$file = isset( $_FILES['envira_import_gallery']['tmp_name'] ) ? wp_unslash( $_FILES['envira_import_gallery']['tmp_name'] ) : false; // @codingStandardsIgnoreLine
		return file_get_contents( $file );

	}

	/**
	 * Move a new post to draft mode before importing a gallery.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The current post ID handling the gallery import.
	 */
	public function maybe_save_draft( $post_id ) {

		$post = get_post( $post_id );
		if ( 'auto-draft' === $post->post_status ) {
			$draft = array(
				'ID'          => $post_id,
				'post_status' => 'draft',
			);
			wp_update_post( $draft );
		}

	}

	/**
	 * Helper method to remove existing gallery data when a gallery is imported.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The current post ID handling the gallery import.
	 */
	public function remove_existing_gallery( $post_id ) {

		delete_post_meta( $post_id, '_eg_gallery_data' );
		delete_post_meta( $post_id, '_eg_in_gallery' );

	}

	/**
	 * Load the wp_generate_attachment_metadata function if necessary.
	 *
	 * @since 1.0.0
	 */
	public function load_metadata_function() {

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

	}

	/**
	 * Set timeout to 0 and suspend cache invalidation while importing a gallery.
	 *
	 * @since 1.0.0
	 */
	public function prepare_import() {

		set_time_limit( $this->get_max_execution_time() );
		wp_suspend_cache_invalidation( true );

	}

	/**
	 * Reset cache invalidation and flush the internal cache after importing a gallery.
	 *
	 * @since 1.0.0
	 */
	public function cleanup_import() {

		wp_suspend_cache_invalidation( false );
		wp_cache_flush();

	}

	/**
	 * Helper method to return the max execution time for scripts.
	 *
	 * @since 1.0.0
	 */
	public function get_max_execution_time() {

		$time = ini_get( 'max_execution_time' );
		return ! $time || empty( $time ) ? (int) 0 : $time;

	}

	/**
	 * Outputs any errors or notices generated by the class.
	 *
	 * @since 1.0.0
	 */
	public function notices() {

		if ( ! empty( $this->errors ) ) :
			?>
		<div id="message" class="error">
			<p><?php echo implode( '<br>', $this->errors ); // @codingStandardsIgnoreLine ?></p>
		</div>
			<?php
		endif;

		// If a gallery has been imported, create a notice for the import status.
		if ( empty( $this->errors ) && isset( $_GET['envira-gallery-imported'] ) && $_GET['envira-gallery-imported'] ) : // @codingStandardsIgnoreLine
			?>
		<div id="message" class="updated">
			<p><?php esc_html_e( 'Envira gallery imported. Please check to ensure all images and data have been imported properly.', 'envira-gallery' ); ?></p>
		</div>
			<?php
		endif;

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Envira_Gallery_Import object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Import ) ) {
			self::$instance = new Envira_Gallery_Import();
		}

		return self::$instance;

	}

}
