<?php

/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 *
 * @var bool   $readonly If the input should be set to readonly mode.
 * @var string $type     The input type attribute.
 */

defined('ABSPATH') || exit;

/* translators: %s: Quantity. */
$label = !empty($args['product_name']) ? sprintf(esc_html__('%s quantity', 'woocommerce'), wp_strip_all_tags($args['product_name'])) : esc_html__('Quantity', 'woocommerce');

?>
<div class="pillars-wc-quantity">
	<?php
	/**
	 * Hook to output something before the quantity input field.
	 *
	 * @since 7.2.0
	 */
	do_action('woocommerce_before_quantity_input_field');
	?>
	<div class="pillars-wc-quantity__wrapper">
		<label class="visuallyhidden screen-reader-text" for="<?= esc_attr($input_id) ?>"><?= esc_attr($label) ?></label>
		<div class="pillars-wc-quantity__minus">–</div>
		<?php echo sprintf(
			'<input type="%s" %s id="%s" class="%s" name="%s" value="%d" aria-label="%s" size="4" min="%s" max="%s" %s>',
			'text', // esc_attr($type)
			$readonly ? 'readonly="readonly"' : '',
			esc_attr($input_id),
			esc_attr(join(' ', (array) $classes)),
			esc_attr($input_name),
			esc_attr($input_value),
			esc_attr__('Product quantity', 'woocommerce'),
			esc_attr($min_value),
			esc_attr(0 < $max_value ? $max_value : ''),
			(!$readonly) ? sprintf('step="%s" placeholder="%s" inputmode="%s" autocomplete="%s"', esc_attr($step), esc_attr($placeholder), esc_attr($inputmode), esc_attr(isset($autocomplete) ? $autocomplete : 'on')) : ''
		); ?>
		<div class="pillars-wc-quantity__plus">+</div>
	</div>
	<?php
	/**
	 * Hook to output something after quantity input field
	 *
	 * @hooked pillars_wc_single_product_discount_info - 5
	 *
	 * @since 3.6.0
	 */
	do_action('woocommerce_after_quantity_input_field');
	?>
</div>
<?php
