<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'pillars_woocommerce_setup');

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function pillars_woocommerce_setup()
{
	add_theme_support('woocommerce', array(
		// 'thumbnail_image_width' => 500,
		'gallery_thumbnail_image_width'	=> 150,
		// 'single_image_width' => 1000,
	));
	// add_theme_support( 'wc-product-gallery-zoom' );
	// add_theme_support('wc-product-gallery-lightbox');
	// add_theme_support('wc-product-gallery-slider');
}

require get_template_directory() . '/woocommerce/includes/wc-functions-remove.php';
require get_template_directory() . '/woocommerce/includes/wc-functions.php';
require get_template_directory() . '/woocommerce/includes/wc-archive-product.php';
require get_template_directory() . '/woocommerce/includes/wc-content-product.php';
require get_template_directory() . '/woocommerce/includes/wc-content-single-product.php';
require get_template_directory() . '/woocommerce/includes/wc-checkout.php';
require get_template_directory() . '/woocommerce/includes/wc-emails.php';
require get_template_directory() . '/woocommerce/includes/wc-filters.php';
