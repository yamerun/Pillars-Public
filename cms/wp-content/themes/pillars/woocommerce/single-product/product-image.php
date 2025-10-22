<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (!function_exists('wc_get_gallery_image_html')) {
	return;
}

global $product;

$images = pillars_wc_get_product_gallery_images($product);
?>

<div class="pillars-wc-product-gallery">
	<?php do_action('woocommerce_product_thumbnails'); ?>
	<div class="pillars-wc-product-gallery__images">

		<?php pillars_wc_product_custom_tags_wrapper(); ?>

		<div class="pillars-wc-product-gallery__container --on-slide --right preload-items swiper-container">
			<figure class="pillars-wc-product-gallery__wrapper swiper-wrapper">
				<?php if ($images) {
					foreach ($images as $image) { ?>
						<div class="pillars-wc-product-gallery__image swiper-slide">
							<?php echo sprintf(
								'<a class="pillars-wc-product-gallery__image-link" data-fancybox="product-gallery" %s href="%s">
									<div class="pillars-wc-product-gallery__cover image-radius %s">
										<div class="media-ratio">%s</div>
										%s%s
									</div>
								</a>' . PHP_EOL,
								(isset($image['iframe'])) ? 'data-type="iframe"' : '',
								$image['link'],
								$image['class'],
								$image['image'],
								($image['content']) ? '<figcaption>' . $image['content'] . '</figcation>' : '',
								(isset($image['iframe'])) ? do_shortcode('[pillars_svg key="video-play"]') : '',
							); ?>
						</div>
					<?php }
				} else { ?>
					<div class="woocommerce-product-gallery__image--placeholder">
						<?php echo sprintf(
							'<img src="%s" alt="%s" class="wp-post-image" />',
							esc_url(wc_placeholder_img_src('woocommerce_single')),
							esc_html__('Awaiting product image', 'woocommerce')
						); ?>
					</div>
				<?php } ?>
			</figure>
			<div class="pillars-slider__navigations">
				<div class="pillars-slider__pagination"></div>
			</div>
			<div class="pillars-slider__buttons-wrapper">
				<div class="pillars-slider__buttons meniscus --bottom-right">
					<div class="pillars-slider__button-prev"></div>
					<div class="pillars-slider__button-next"></div>
				</div>
			</div>
		</div>
	</div>
</div>