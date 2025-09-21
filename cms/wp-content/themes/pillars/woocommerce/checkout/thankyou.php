<?php

/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order">

	<?php
	if ($order) :

		do_action('woocommerce_before_thankyou', $order->get_id());
	?>

		<?php if ($order->has_status('failed')) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
				<?php if (is_user_logged_in()) : ?>
					<a href="<?= esc_url(wc_get_page_permalink('myaccount')); ?>" class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<div class="row">
				<div class="col-md-5 col-sm-6">
					<div class="block pillars-wc-thankyou-order-wrapper">
						<?php wc_get_template('checkout/order-received.php', array('order' => $order)); ?>

						<table class="pillars-wc-thankyou-order-details" role="presentation">
							<tbody>
								<tr>
									<td class="label"><?php esc_html_e('Order number:', 'woocommerce'); ?></td>
									<td class="value"><strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																?></strong></td>
								</tr>
								<tr>
									<td class="label"><?php esc_html_e('Date:', 'woocommerce'); ?></td>
									<td class="value"><strong><?php echo wc_format_datetime($order->get_date_created()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																?></strong></td>
								</tr>
								<?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
									<tr>
										<td class="label"><?php esc_html_e('Email:', 'woocommerce'); ?></td>
										<td class="value"><strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																	?></strong></td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="label"><?php esc_html_e('Total:', 'woocommerce'); ?></td>
									<td class="value"><strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																?></strong></td>
								</tr>
							</tbody>
						</table>

						<?php wc_get_template('checkout/order-details-customer.php', array('order' => $order)); ?>
					</div>
				</div>
				<div class="col-md-5 col-sm-6">
					<div class="block pillars-wc-thankyou-order-wrapper">
						<?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
						<?php do_action('woocommerce_thankyou', $order->get_id()); ?>
					</div>
				</div>
				<div class="col-md-2 hide-sm"></div>
			</div>

		<?php endif; ?>

	<?php else : ?>

		<?php wc_get_template('checkout/order-received.php', array('order' => false)); ?>

	<?php endif; ?>

</div>