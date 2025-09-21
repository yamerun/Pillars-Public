<?php

/**
 * Siblings products in form add to cart
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="form-style">
	<div class="form-style__row">
		<div class="form-style__full p-unleft p-unright">
			<?php
			echo do_shortcode(sprintf(
				'[get-popup id="%s" form="%s" text="%s" class="btn-1 btn-full alt" container="div" args="%s"]',
				'individual-production',
				'iproduction',
				'Индивидуальное производство',
				theplugin_array_to_args(['page_id' => $product->get_id()])
			));
			?>
		</div>
		<?= do_action('wc_product_production_after') ?>
	</div>
</div>