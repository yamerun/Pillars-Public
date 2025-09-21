<?php

/**
 * Video Review tab
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;

$videolink = get_post_meta($product->get_id(), '_product_video_review', true);
if (isset($videolink['cover_id'])) {
	$covers = wp_get_attachment_image_url($videolink['cover_id'], 'full');
	$videolink = $videolink['url'];
}

if ($videolink && isset($covers)) {
	$description = get_post_meta($product->get_id(), '_product_video_review_text', true);
?>
	<div class="col-lg-7">
		<div class="block">
			<a class="pillars-wc-product-tab__videoreview video-placeholder" target="_blank" href="<?= theplugin_get_video_embed_link($videolink) ?>">
				<div class="pillars-wc-product-tab__videoreview__cover">
					<div class="media-ratio">
						<img src="<?= $covers ?>" alt="Смотреть видео-обзор">
					</div>
				</div>
				<?= do_shortcode('[pillars_svg key="video-play" class="pillars-wc-product-tab__videoreview__btn"]') ?>
				<div class="pillars-wc-product-tab__videoreview__title">Видео-обзор</div>
			</a>
		</div>
	</div>
	<div class="col-lg-5">
		<?php if ($description) { ?>
			<div class="block wp-block">
				<?= apply_filters('the_content', $description) ?>
			</div>
		<?php } ?>
	</div>
<?php
}
