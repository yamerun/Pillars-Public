<?php

/**
 * Single Product advanteges
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;
$brochure = get_post_meta($product->get_id(), '_pillars_product_brochure', true);
?>

<div class="pillars-wc-product-advanategs">
	<ul class="pillars-wc-product-advanategs__wrapper">
		<li>Срок производства от 3 дней</li>
		<li>Гарантия 12 месяцев</li>
		<li>Доставка по всей России и СНГ</li>
	</ul>

	<?php if (is_array($brochure) && isset($brochure['url']) && $brochure['url']) { ?>
		<div class="pillars-wc-product-advanategs__wrapper --link" onclick="ym(60911305,'reachGoal','DOWNLOAD_BROCHURE'); return true;">
			<img src="<?= get_template_directory_uri() ?>/assets/images/icon-product-download-white.svg">
			<a href="<?= esc_url($brochure['url']) ?>" target=_blank><?= $brochure['text'] ?></a>
		</div>
	<?php } ?>
</div>