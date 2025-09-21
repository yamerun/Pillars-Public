<?php

/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.7.0
 */

defined('ABSPATH') || exit;

$show_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address();
$item_totals = $order->get_order_item_totals();
?>

<table class="pillars-wc-thankyou-order-details" role="presentation">
	<tbody>
		<?php if (isset($item_totals['shipping'])) : ?>
			<tr>
				<td class="label"><?= $item_totals['shipping']['label'] ?></td>
				<td class="value"><strong><?= wp_kses_post($item_totals['shipping']['value']) ?></strong></td>
			</tr>
		<?php endif; ?>
		<?php if ($order->get_payment_method_title()) : ?>
			<tr>
				<td class="label"><?php esc_html_e('Payment method:', 'woocommerce'); ?></td>
				<td class="value"><strong><?= wp_kses_post($order->get_payment_method_title()) ?></strong></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="label"><?php esc_html_e('Billing address', 'woocommerce'); ?></td>
			<td class="value"><strong><?= wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?></strong></td>
		</tr>
		<?php if ($order->get_billing_phone()) : ?>
			<tr>
				<td class="label"><?php esc_html_e('Phone', 'woocommerce'); ?></td>
				<td class="value"><strong><?= esc_html($order->get_billing_phone()); ?></strong></td>
			</tr>
		<?php endif; ?>

		<?php if ($order->get_billing_email()) : ?>
			<tr>
				<td class="label"><?php esc_html_e('Email', 'woocommerce'); ?></td>
				<td class="value"><strong><?= esc_html($order->get_billing_email()); ?></strong></td>
			</tr>
		<?php endif; ?>

		<?php
		/**
		 * Action hook fired after an address in the order customer details.
		 *
		 * @since 8.7.0
		 * @param string $address_type Type of address (billing or shipping).
		 * @param WC_Order $order Order object.
		 */
		do_action('woocommerce_order_details_after_customer_address', 'billing', $order);
		?>
	</tbody>
</table>



<?php if ($show_shipping) : ?>

	<h2 class="woocommerce-column__title"><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>
	<address>
		<?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>

		<?php if ($order->get_shipping_phone()) : ?>
			<p class="woocommerce-customer-details--phone"><?php echo esc_html($order->get_shipping_phone()); ?></p>
		<?php endif; ?>

		<?php
		/**
		 * Action hook fired after an address in the order customer details.
		 *
		 * @since 8.7.0
		 * @param string $address_type Type of address (billing or shipping).
		 * @param WC_Order $order Order object.
		 */
		do_action('woocommerce_order_details_after_customer_address', 'shipping', $order);
		?>
	</address>

<?php endif; ?>

<?php do_action('woocommerce_order_details_after_customer_details', $order); ?>