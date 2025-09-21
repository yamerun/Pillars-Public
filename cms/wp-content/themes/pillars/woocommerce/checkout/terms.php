<?php

/**
 * Checkout terms and conditions area.
 *
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

/* checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); // WPCS: input var ok, csrf ok. */

if (apply_filters('woocommerce_checkout_show_terms', true) && function_exists('wc_terms_and_conditions_checkbox_enabled')) {
	do_action('woocommerce_checkout_before_terms_and_conditions');

?>
	<div class="woocommerce-terms-and-conditions-wrapper">
		<?php
		/**
		 * Terms and conditions hook used to inject content.
		 *
		 * @since 3.4.0.
		 * @hooked wc_checkout_privacy_policy_text() Shows custom privacy policy text. Priority 20.
		 * @hooked wc_terms_and_conditions_page_content() Shows t&c page content. Priority 30.
		 */
		// do_action('woocommerce_checkout_terms_and_conditions');
		?>

		<?php if (wc_terms_and_conditions_checkbox_enabled()) : ?>
			<div class="woocommerce-terms-and-conditions-checkbox">
				<label class="confirm-wrapper">
					<input type="checkbox" <?php checked(1); ?> name="terms" id="terms" />
					<span></span>
					<div><?php pillars_wc_terms_and_conditions_checkbox_text(); ?></div>
				</label>
				<input type="hidden" name="terms-field" value="1" />
			</div>
		<?php endif; ?>
	</div>
<?php

	do_action('woocommerce_checkout_after_terms_and_conditions');
}
