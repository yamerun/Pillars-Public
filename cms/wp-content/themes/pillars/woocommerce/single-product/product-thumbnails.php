<?php

/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (!function_exists('wc_get_gallery_image_html')) {
	return;
}

global $product;

$images = pillars_wc_get_product_gallery_images($product);

if (!theplugin_is_mobile() && $images) { ?>
	<div class="pillars-wc-product-thumbnails__images">
		<div class="pillars-wc-product-thumbnails__container swiper-container">
			<figure class="pillars-wc-product-thumbnails__wrapper swiper-wrapper">
				<?php foreach ($images as $image) { ?>
					<div class="pillars-wc-product-gallery__image swiper-slide">
						<?php echo sprintf(
							'<a class="pillars-wc-product-gallery__image-link %s" href="%s"><img src="%s" /></a>',
							// $image['class'],
							'',
							$image['link'],
							$image['thumb'],
						); ?>
					</div>
				<?php } ?>
			</figure>

			<div class="pillars-slider__button-prev"></div>
			<div class="pillars-slider__button-next"></div>
		</div>
	</div>
<?php } ?>