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
 * Plugin URI:        https://samwilson.id.au/piwigo-embeds
 * Description:       Embed photos from a whitelist of Piwigo websites.
 * Version:           0.1.0
 * Author:            Sam Wilson
 * Author URI:        https://samwilson.id.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       piwigo-embeds
 * Domain Path:       /languages
 */

define( 'PIWIGO_EMBEDS_VERSION', '0.1.0' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load the URLs and register embed handlers for them all.
$base_urls = explode("\n", get_option('piwigo-embeds-urls'));
foreach ($base_urls as $i => $base_url) {
	$trimmed_base_url = trim($base_url);
	wp_embed_register_handler(
		"piwigo_$i",
		"|($trimmed_base_url)/picture[^0-9]*([0-9]+)|i",
		function ( $matches, $attr, $url, $rawattr ) {
			$base_url = $matches[1];
			$image_id = $matches[2];
			$api_url = "$base_url/ws.php?format=php&method=pwg.images.getInfo&image_id=$image_id";
			$response = wp_remote_get( $api_url );
			if ( $response instanceof WP_Error || ! is_serialized( $response['body'] ) ) {
				return "<p class='error aligncenter'>[Error: Unable to retrieve photo $image_id]</p>";
			} else {
				$info = unserialize($response['body']);
				if (!isset($info['result'])) {
					return "<p class='error aligncenter'>"
						."[Error: Unable to retrieve photo $image_id"
						." (Piwigo said: '".$info['message']."')]</p>";
				}
				$page_url = $info['result']['page_url'];
				$medium = $info['result']['derivatives']['medium'];
				$image_url = $medium['url'];
				$title = $info['result']['name'];
				$description = htmlspecialchars($info['result']['comment']);
				$link_format = '<a href="%s"><img src="%s" alt="%s" title="%s" /></a>';
				$img_link = sprintf($link_format, $page_url, $image_url, $description, $description);
				$caption_attrs = [
					'caption' => $title,
					'width' => $medium['width'],
					'align' => 'aligncenter',
				];
				return img_caption_shortcode($caption_attrs, $img_link);
			}
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
		function ($args) {
			$val = get_option('piwigo-embeds-urls');
			echo '<textarea id="piwigo-embeds-urls" name="piwigo-embeds-urls" cols="80" rows="5">'.$val.'</textarea>';
			echo '<p class="description">'.__('', 'piwigo-embeds').'</p>';
		},
		$option_group
	);
} );
