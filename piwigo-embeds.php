<?php
/**
 * The Piwigo Embeds plugin adds support for embedding photos from whitelisted Piwigo websites.
 *
 * @file
 * @package           piwigo-embed
 * @since             0.1.0
 *
 * @wordpress-plugin
 * Plugin Name:       Piwigo Embeds
 * Plugin URI:        https://samwilson.id.au/plugins/piwigo-embeds/
 * Description:       Embed photos from a whitelist of Piwigo websites.
 * Version:           0.2.0
 * Author:            Sam Wilson
 * Author URI:        https://samwilson.id.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       piwigo-embeds
 * Domain Path:       /languages
 */

define( 'PIWIGO_EMBEDS_VERSION', '0.2.0' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load the URLs and register embed handlers for them all.
$base_urls = explode( "\n", get_option( 'piwigo-embeds-urls' ) );
foreach ( $base_urls as $i => $base_url ) {
	$trimmed_base_url = trim( $base_url, "\t\n\r\0\x0B/" );
	wp_embed_register_handler(
		"piwigo_$i",
		"|($trimmed_base_url)/picture[^0-9]*([0-9]+)|i",
		function ( $matches, $attr, $url, $rawattr ) {
			$base_url = $matches[1];
			$image_id = $matches[2];
			try {
				$info = piwigo_embeds_get_image_info( $base_url, $image_id );
			} catch ( Exception $exception ) {
				// translators: a prefix to add to error messages.
				$msg = __( 'Error: %s', 'piwigo-embeds' );
				return '<p class="piwigo-embeds error">' . sprintf( $msg, $exception->getMessage() ) . '</p>';
			}
			$page_url      = $info['page_url'];
			$medium        = $info['derivatives']['medium'];
			$image_url     = $medium['url'];
			$title         = $info['name'];
			$description   = htmlspecialchars( $info['comment'] );
			$link_format   = '<a href="%s"><img src="%s" alt="%s" title="%s" /></a>';
			$img_link      = sprintf( $link_format, $page_url, $image_url, $description, $description );
			$caption_attrs = [
				'caption' => $title,
				'width'   => $medium['width'],
				'align'   => 'aligncenter',
			];
			return img_caption_shortcode( $caption_attrs, $img_link );
		}
	);
}

// Add the "Piwigo site URLs" option to the end of the general options page.
add_action( 'admin_init', function () {
	$option_group = 'writing';
	register_setting( $option_group, 'piwigo-embeds-urls', [ 'type' => 'string' ] );
	add_settings_field(
		'piwigo-embeds-urls',
		'Piwigo site URLs',
		function ( $args ) {
			$val = get_option( 'piwigo-embeds-urls' );
			echo '<textarea id="piwigo-embeds-urls" name="piwigo-embeds-urls" cols="80" rows="5">' . esc_html( $val ) . '</textarea>';
			// translators: help text for this plugin's configuration option.
			echo '<p class="description">' . esc_html( __( 'Base URLs of Piwigo sites, one per line.', 'piwigo-embeds' ) ) . '</p>';
		},
		$option_group
	); }
);

/**
 * Get information from a Piwigo site about a single image.
 * This function takes care of caching, and will only request new information every hour at most.
 *
 * @param string $base_url The base URL for the Piwigo site.
 * @param int    $image_id The Piwigo ID of the image to get information for.
 * @return string[][][]
 * @throws Exception If no data could be retrieved.
 */
function piwigo_embeds_get_image_info( $base_url, $image_id ) {
	$api_url        = "$base_url/ws.php?format=json&method=pwg.images.getInfo&image_id=$image_id";
	$transient_name = 'piwigo_embeds_site_' . md5( $base_url ) . '_' . $image_id;
	$cached         = get_transient( $transient_name );
	if ( $cached ) {
		return $cached;
	}
	$response = wp_remote_get( $api_url );
	if ( $response instanceof WP_Error ) {
		// translators: error message displayed when no response could be got from a Piwigo API call.
		$msg = __( 'Unable to retrieve photo %s', 'piwigo-embeds' );
		throw new Exception( sprintf( $msg, $image_id ) );
	} else {
		$info = json_decode( $response['body'], true );
		if ( ! isset( $info['result'] ) && isset( $info['message'] ) ) {
			// translators: error message displayed when an error was received from a Piwigo API call.
			$msg = __( 'Unable to retrieve photo %1$s (Piwigo said: %2$s)', 'piwigo-embeds' );
			throw new Exception( sprintf( $msg, $image_id, $info['message'] ) );
		}
		set_transient( $transient_name, $info['result'], 60 * 60 );
		return $info['result'];
	}
}
