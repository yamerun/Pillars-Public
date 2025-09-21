<?php

defined('ABSPATH') || exit;

add_filter('woocommerce_order_status_completed', 'pillars_wc_order_status_completed_notice');

function pillars_wc_get_company_card_url($order_id = 0)
{
	if (get_post_meta($order_id, '_wc_order_company_card')) {
		printf(__('<p><a href="%s" target=\'_blank\'>Карточка предприятия</a></p>', 'pillars'), get_post_meta($order_id, '_wc_order_company_card', true));
	}
}

/**
 * Отправляем WC админу данные о выполнении заказа
 *
 * @param [type] $order_id
 * @return int
 */
function pillars_wc_order_status_completed_notice($order_id)
{
	$WC_Emails	= new WC_Emails();
	$emails		= $WC_Emails->get_emails();

	update_post_meta($order_id, '_wc_order_mail_admin_send', theplugin_send_mail(
		$emails['WC_Email_New_Order']->recipient,
		'Подтверждение оплаты заказа #' . $order_id,
		'Статус заказа #' . $order_id . ' обновлён на «Выполнен».',
		true
	));

	return $order_id;
}
