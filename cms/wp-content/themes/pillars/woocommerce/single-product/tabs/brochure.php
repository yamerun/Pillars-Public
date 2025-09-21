<?php

/**
 * Collective tab
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $post;

$path = sprintf(
	'%s/woocommerce/single-product/tabs/%s.php',
	get_template_directory(),
	get_post_meta($post->ID, '_product_section_collection', true)
);

if (file_exists($path)) {
	wc_get_template('single-product/tabs/' . get_post_meta($post->ID, '_product_section_collection', true) . '.php');
}
