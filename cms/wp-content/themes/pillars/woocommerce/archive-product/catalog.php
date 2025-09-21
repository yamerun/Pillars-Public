<?php

/**
 * Shop page
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;
?>
<div class="row">
	<div class="pillars-wc-catalog__sidebar hide-md">
		<div class="block">
			<?php echo pillars_theme_wc_get_product_cat_for_menu(array(
				'before'	=> '<nav class="pillars-wc-catalog__list"><ul>',
				'after'		=> '</ul></nav>',
				'groups'	=> true
			)); ?>
		</div>
	</div>
	<div class="pillars-wc-catalog">
		<?= pillars_theme_wc_get_product_cat_for_catalog() ?>
	</div>
</div>