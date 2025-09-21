<?php

/**
 * Email Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 5.6.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$text_align		= is_rtl() ? 'right' : 'left';
$address		= $order->get_formatted_billing_address();
$shipping		= $order->get_formatted_shipping_address();
$item_totals	= $order->get_order_item_totals();
$style_td		= "text-align:" . esc_attr($text_align) . "; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; vertical-align: top;";

?><table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<h2><?php echo wp_kses_post($item_totals['shipping']['label']); ?></h2>
			<p><?php echo wp_kses_post($item_totals['shipping']['value']); ?></p>
		</td>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<h2><?php echo wp_kses_post($item_totals['payment_method']['label']); ?></h2>
			<p><?php echo wp_kses_post($item_totals['payment_method']['value']); ?></p>
		</td>
	</tr>

	<tr>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<h2><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>
			<?php if (!wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping) : ?>

				<address class="address">
					<?php echo wp_kses_post($shipping); ?>
					<?php if ($order->get_shipping_phone()) : ?>
						<br /><?php echo wc_make_phone_clickable($order->get_shipping_phone()); ?>
					<?php endif; ?>
				</address>
			<?php else : ?>

				<p>Нет</p>

			<?php endif; ?>
		</td>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<h2><?php esc_html_e('Billing address', 'woocommerce'); ?></h2>

			<address class="address">
				<?php if ($order->get_billing_company()) : ?>
					<?php echo wp_kses_post($order->get_billing_company()); ?>
				<?php endif; ?>
			</address>
		</td>
	</tr>

	<tr>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<h2><?php esc_html_e('Customer details', 'woocommerce'); ?></h2>

			<address class="address">
				<?php echo wp_kses_post($address ? $address : esc_html__('N/A', 'woocommerce')); ?>
				<?php if ($order->get_billing_phone()) : ?>
					<br /><?php echo wc_make_phone_clickable($order->get_billing_phone()); ?>
				<?php endif; ?>
				<?php if ($order->get_billing_email()) : ?>
					<br /><?php echo esc_html($order->get_billing_email()); ?>
				<?php endif; ?>
			</address>
		</td>
		<td class="td" style="<?= $style_td ?>" valign="top" width="50%">
			<?php
			/**
			 * Отображаем ссылку на файл карточки предприятия, если файл есть
			 */
			pillars_wc_get_company_card_url($order->get_id());
			?>
		</td>
	</tr>
</table>