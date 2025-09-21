<?php

/**
 * Usage Image block
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;

$usage_id	= get_post_meta($product->get_id(), '_pillars_product_image_dimensions', true);
$model_3D	= get_post_meta($product->get_id(), '_pillars_product_file_3d_model', true);

?>
<div class="pillars-wc-product-usage">
	<?php if ($usage_id) { ?>
		<h3 class="pillars-wc-product-tab__title">Чертёж изделия</h3>
		<a class="pillars-wc-product-usage__wrapper" data-fancybox="usage-gallery" href="<?= wp_get_attachment_image_url($usage_id, 'full') ?>">
			<?= wp_get_attachment_image($usage_id, 'medium') ?>
		</a>
	<?php } ?>
	<div class="pillars-wc-product-tab__model-3d">
		<?php echo do_shortcode(sprintf(
			'[get-popup id="%s" form="%s" text="%s" class="btn-1 btn-full" container="div" args="%s"]',
			'get-3d-model',
			'get3Dmodel',
			'Запросить 3D-модель',
			theplugin_array_to_args(['page_id' => $product->get_id()])
		)); ?>
	</div>
</div>