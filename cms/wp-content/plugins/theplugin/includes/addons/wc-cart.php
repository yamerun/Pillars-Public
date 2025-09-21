<?php

defined('ABSPATH') || exit;

/**
 * Undocumented function
 *
 * @param boolean $total
 * @return void
 */
function theplugin_wc_cart_total_fees($total = false)
{
	$discounts	= WC()->session->get('pillars_discount');
	$totals		= array();

	if (!is_null($discounts)) {
		foreach ($discounts as $product_id => $items) {
			foreach ($items as $item) {
				$totals[$product_id]['label']		= $item['label'];
				$totals[$product_id]['discount'][]	= $item['discount'];
			}
		}

		if ($totals) {
			foreach ($totals as $product_id => $item) {
				$totals[$product_id]['discount'] = array_sum($totals[$product_id]['discount']);
			}
		}

		if ($total) {
			$discount = 0;
			foreach ($totals as $product_id => $item) {
				$discount += $item['discount'];
			}

			return array(
				'label'		=> 'Скидка: ',
				'discount'	=> $discount
			);
		}
	}

	return $totals;
}

function theplugin_wc_email_order_fees($fees = array()) {

}