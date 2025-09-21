<?php

/**
 * Single Product link to certificate
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;
$image_id = get_post_meta($product->get_id(), '_product_certificate_id', true);

if ($image_id) {
	$image	= wp_get_attachment_image_url($image_id, 'full');
	$text	= get_post_meta($product->get_id(), '_product_certificate_text', true);
	$text	= ($text) ? $text : 'Сертификат';
?>
	<div class="pillars-wc-tab__link">
		<a data-fancybox="certificate" href="<?= esc_url($image) ?>">
			<span><?= $text ?></span>
		</a>
	</div>
<?php
}
