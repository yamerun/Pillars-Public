<?php

/**
 * "Order received" message.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-received.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.8.0
 *
 * @var WC_Order|false $order
 */

defined('ABSPATH') || exit;
?>

<div class="pillars-wc-thankyou-order-received">
	<div class="pillars-wc-thankyou-order-icon">
		<img alt="" src="data:image/png;base64,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" />
	</div>
	<?php
	/**
	 * Filter the message shown after a checkout is complete.
	 *
	 * @since 2.2.0
	 *
	 * @param string         $message The message.
	 * @param WC_Order|false $order   The order created during checkout, or false if order data is not available.
	 */

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo sprintf('<p>%s</p>', apply_filters(
		'woocommerce_thankyou_order_received_text',
		esc_html(__('Благодарим вас за оформление заказа!', 'woocommerce')),
		$order
	));

	echo sprintf('<p>%s</p>', apply_filters(
		'woocommerce_thankyou_order_received_text',
		esc_html(__('Наши менеджеры свяжутся с вами в ближайшее время.', 'woocommerce')),
		$order
	));
	?>
</div>