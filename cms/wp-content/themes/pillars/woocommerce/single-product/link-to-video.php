<?php

/**
 * Single Product anchor-link to #tab-additional_information
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;

$videolink = get_post_meta($product->get_id(), '_product_video_review', true);
if (isset($videolink['cover_id']) && $videolink['cover_id']) {
	$covers = wp_get_attachment_image_url($videolink['cover_id'], 'thumbnail');
}

if ($videolink && isset($covers)) {
?>
	<div class="pillars-wc-tab__link-to-video">
		<a href="#tab-videoreview">
			<div class="pillars-wc-tab__link-to-video__cover">
				<div class="media-ratio">
					<img src="<?= $covers ?>" alt="Смотреть видео-обзор">
				</div>
			</div>
			<?= do_shortcode('[pillars_svg key="video-play"]') ?>
			<span>Смотреть видео-обзор</span>
		</a>
	</div>
<?php
} ?>