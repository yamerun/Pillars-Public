<?php

/**
 * Individual prodaction for price product
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
			<div class="iproduction__content">
				<div class="iproduction-tag">Оптовым клиентам</div>
				<div class="iproduction__wrapper m-untop">
					<div class="iproduction__wrapper-description">Скидка до 30%</div>
					<?= do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2 btn-full alt" container="div" args="%s"]',
						'individual-production',
						'iproduction',
						'Узнать цену',
						theplugin_array_to_args(['page_id' => $product->get_id()])
					)) ?>
				</div>
				<div class="iproduction-note">Получите персональные условия сотрудничества</div>
			</div>
		</div>
		<?= do_action('wc_product_production_after') ?>
	</div>
</div>