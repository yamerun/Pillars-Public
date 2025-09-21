<?php

defined('ABSPATH') || exit;

/**
 * Класс создания заказа WooCommerce
 */
class THEPLUGIN_WC_Order
{

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	public $wc_order_data = [];

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	public $wc_errors = null;

	function __construct($args)
	{
		self::set_wc_order($args);
	}

	/**
	 * Функция создания заказа и добавления товаров
	 */
	public function set_wc_order($args = [])
	{

		$this->wc_errors        = new WP_Error;

		$data = '';

		$defaults = [
			'billing_first_name'    => '',
			'billing_last_name'     => '',
			'billing_company'       => '',
			'billing_email'         => '',
			'billing_phone'         => '',
			'billing_address_1'     => '',
			'billing_address_2'     => '',
			'billing_city'          => '',
			'billing_state'         => '',
			'billing_postcode'      => '',
			'billing_country'       => '',
			'payment_method'        => '',

			'shipping_first_name'   => '',
			'shipping_last_name'    => '',
			'shipping_company'      => '',
			'shipping_email'        => '',
			'shipping_phone'        => '',
			'shipping_address_1'    => '',
			'shipping_address_2'    => '',
			'shipping_city'         => '',
			'shipping_state'        => '',
			'shipping_postcode'     => '',
			'shipping_country'      => '',
			'shipping_method'       => '',
			'shipping_data'         => [],

			'note'                  => '',
			'note_customer'         => 0,

			'cart'                  => true,
			'product'               => [],
			'fees'					=> [],

			'mailer_customer'       => true,
			'mailer_invoice'        => false,
			'mailer_admin'          => true
		];

		$args = wp_parse_args($args, $defaults);

		$billing_address = array(
			'first_name' => $args['billing_first_name'],
			'last_name'  => $args['billing_last_name'],
			'company'    => $args['billing_company'],
			'email'      => $args['billing_email'],
			'phone'      => $args['billing_phone'],
			'address_1'  => $args['billing_address_1'],
			'address_2'  => $args['billing_address_2'],
			'city'       => $args['billing_city'],
			'state'      => $args['billing_state'],
			'postcode'   => $args['billing_postcode'],
			'country'    => $args['billing_country']
		);

		$shipping_address = array(
			'first_name' => $args['shipping_first_name'],
			'last_name'  => $args['shipping_last_name'],
			'company'    => $args['shipping_company'],
			'email'      => $args['shipping_email'],
			'phone'      => $args['shipping_phone'],
			'address_1'  => $args['shipping_address_1'],
			'address_2'  => $args['shipping_address_2'],
			'city'       => $args['shipping_city'],
			'state'      => $args['shipping_state'],
			'postcode'   => $args['shipping_postcode'],
			'country'    => $args['shipping_country']
		);

		// Устанавливаем способ доставки
		WC()->session->set('chosen_shipping_methods', array($args['shipping_method']));

		$wc_order = false;
		$wc_order = wc_create_order();

		if (is_wp_error($wc_order)) {
			self::set_wc_error($wc_order->get_error_code(), $wc_order->get_error_message());
			return false;
		}

		// Если пользователь авторизирован, то к нему привязываем заказ
		if (is_user_logged_in()) {
			$wc_order->set_customer_id(get_current_user_id());
		}

		$wc_order->set_address($billing_address,   'billing');
		$wc_order->set_address($shipping_address,  'shipping');

		// Задаём список товаров заказа
		$shipping_products_list = [];

		// Если стоит добавление товаров из Корзины, по умолчанию Да
		if ($args['cart']) {
			// Получить корзину
			$cart = WC()->cart;
			// Товары из корзины
			foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
				$_product     = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$product_id   = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
				$wc_order->add_product($_product, $cart_item['quantity'], [
					'variation' => $cart_item['variation'],
					'totals'    => [
						'subtotal'     => $cart_item['line_subtotal'],
						'subtotal_tax' => $cart_item['line_subtotal_tax'],
						'total'        => $cart_item['line_total'],
						'tax'          => $cart_item['line_tax'],
						'tax_data'     => $cart_item['line_tax_data']
					]
				]);

				// Добавляем в общий список товаров для доставки
				$shipping_products_list[] = $_product->get_name() . ' × ' . $cart_item['quantity'];
			}
		}

		// Если есть товары, которые добавляются вне Корзины
		if (!empty($args['product'])) {
			foreach ($args['product'] as $_product_item) {
				$_product       = wc_get_product($_product_item['id']);

				if ($_product && !is_wp_error($_product)) {
					// Проверяем наличие дополнительных параметров товара
					$_product_args  = array();
					if (isset($_product_item['args'])) {
						$_product_args = $_product_item['args'];
					}
					// Обновляем цену товара, если есть запрос
					if (isset($_product_item['price'])) {
						$_product->set_price($_product_item['price']);
					}
					// Дополняем название товара, если есть запрос
					if (isset($_product_item['name'])) {
						$_product->set_name($_product->get_name() . ' – ' . $_product_item['name']);
					}

					// Дополняем изображение товара
					if (isset($_product_item['image'])) {
						$_product->set_image_id($_product_item['image']);
					}

					// Добавляем товар в заказ
					$wc_order->add_product($_product, $_product_item['quantity'], $_product_args);
					// Добавляем в общий список товаров для доставки
					$shipping_products_list[] = $_product->get_name() . ' × ' . $_product_item['quantity'];
				}

				// Удаляем переменную продукта
				unset($_product);
			}
		}

		if ($args['fees']) {
			foreach ($args['fees'] as $item) {
				$item_fee = new WC_Order_Item_Fee();
				$item_fee->set_name($item['label']);
				$item_fee->set_amount($item['discount']);
				$item_fee->set_total($item['discount']);

				$wc_order->add_item($item_fee);
			}
		}

		// Устанавливаем способ доставки
		$shipping_data						= self::get_shipping_data($args['shipping_method'], $args);
		$shipping_data[0][__('Позиции')]	= implode(', ', $shipping_products_list);
		$set_shipping						= self::set_shipping_line($wc_order, $shipping_data[0]);

		self::set_wc_error('set_shipping', $set_shipping);

		// Устанавливаем способ оплаты
		foreach (WC()->payment_gateways->payment_gateways() as $method_id => $method_object) {
			if ($method_id === $args['payment_method']) {
				$wc_order->set_payment_method($method_object);
				break;
			}
		}

		// Добавляем заметки
		if (!empty($args['note'])) {
			$wc_order->add_order_note($args['note'], $args['note_customer']);
		}

		// Обновляем данные по заказу
		$wc_order->calculate_totals();

		// Очистить корзину
		if ($args['cart']) {
			$cart->empty_cart();
		}

		$set_shipping_tax = self::set_shipping_tax($wc_order, ['shipping_method' => $args['shipping_method']]);

		$this->wc_order_data = $wc_order;
	}

	/**
	 * Возвращаем результат создания заказа
	 *
	 * @return WC_Order|WP_Error
	 */
	public function get_wc_order()
	{
		return $this->wc_order_data;
	}

	/**
	 * Отправляем письма от WC
	 *
	 * @param array $args
	 * @return void
	 */
	public function send_mails($args = array())
	{

		$defaults = [
			'mailer_customer'       => true,
			'mailer_invoice'        => false,
			'mailer_admin'          => true
		];

		$args = wp_parse_args($args, $defaults);

		$wc_order = $this->wc_order_data;

		$mailer = WC()->mailer();
		// Отправить письмо юзеру
		if ($args['mailer_customer']) {
			$email = $mailer->emails['WC_Email_Customer_Processing_Order'];
			$email->trigger($wc_order->get_id());
		}
		// Отправить письмо юзеру на оплату
		if ($args['mailer_invoice']) {
			$email = $mailer->emails['WC_Email_Customer_Invoice'];
			$email->trigger($wc_order->get_id());
		}
		// Отправить письмо админу
		if ($args['mailer_admin']) {
			$email = $mailer->emails['WC_Email_New_Order'];
			$email->trigger($wc_order->get_id());
		}
	}

	/**
	 * Сохраняем напрямую в БД список товаров к доставке
	 */
	private function set_shipping_line($wc_order, $args = [])
	{

		if ($wc_order->get_items('shipping')) {
			return '';
		}

		global $wpdb;

		$defaults = [
			'id'            => '',
			'label'         => '',
			'method_id'     => '',
			'instance_id'   => '',
			'cost'          => 0,
			'total_tax'     => 0,
			'taxes'         => '',
			'Позиции'       => ''
		];

		$args = wp_parse_args($args, $defaults);

		$table_name = $wpdb->prefix . 'woocommerce_order_items';
		$wpdb->insert(
			$table_name,
			array(
				'order_item_name'   => $args['label'],
				'order_item_type'   => 'shipping',
				'order_id'          => $wc_order->get_id()
			)
		);

		if ($wpdb->insert_id) {

			$order_item_id  = $wpdb->insert_id;
			$table_name     = $wpdb->prefix . 'woocommerce_order_itemmeta';

			$result['order_item_id'] = $order_item_id;

			foreach ($args as $meta_key => $meta_value) {
				if (!in_array($meta_key, array('id', 'label', 'package_key'))) {
					$wpdb->insert(
						$table_name,
						array(
							'order_item_id' => $order_item_id,
							'meta_key'      => $meta_key,
							'meta_value'    => $meta_value
						)
					);

					$result[$meta_key] = $wpdb->insert_id;
				}
			}

			return $result;
		}

		return false;
	}

	/**
	 * Определяем способ доставки
	 */
	private function get_shipping_data($method_id = '', $args = [])
	{

		$chosen_method = (isset(WC()->session->chosen_shipping_methods[0]) && empty($method_id)) ? WC()->session->chosen_shipping_methods[0] : $method_id;
		$shipping_data = array(); // Initializing

		self::set_wc_error('chosen_method', $chosen_method);

		// Get shipping packages keys from cart
		$packages_keys = (array) array_keys(WC()->cart->get_shipping_packages());

		self::set_wc_error('packages_keys', $packages_keys);

		// Проверка существования способов доставки
		if (empty($packages_keys[0])) {
			if (!empty($args['shipping_data'])) {
				$shipping_data[] = $args['shipping_data'];
			}
			return $shipping_data;
		}

		// Loop through shipping packages keys (when cart is split into many shipping packages)
		foreach ($packages_keys as $key) {
			// Get available shipping rates from WC_Session
			$shipping_rates = WC()->session->get('shipping_for_package_' . $key)['rates'];

			// Loop through shipping rates
			foreach ($shipping_rates as $rate_key => $rate) {
				// Set all related shipping rate data in the array
				if ($rate_key == $chosen_method) {
					$shipping_data[] = array(
						'id'          => $rate_key,
						'method_id'   => $rate->method_id,
						'instance_id' => (int) $rate->instance_id,
						'label'       => $rate->label,
						'cost'        => (float) $rate->cost,
						'taxes'       => (array) $rate->taxes,
						'package_key' => (int) $key,
					);
				}
			}
		}

		return $shipping_data;
	}

	/**
	 * Обновление стоимости доставки
	 */
	private function set_shipping_tax($wc_order, $args = [])
	{

		$calculate_tax_for = array(
			'country'  => $wc_order->get_shipping_country(),
			'state'    => $wc_order->get_shipping_state(), // (optional value)
			'postcode' => $wc_order->get_shipping_postcode(), // (optional value)
			'city'     => $wc_order->get_shipping_city(), // (optional value)
		);

		$changed = false;

		foreach ($wc_order->get_items('shipping') as $item_id => $item) {

			// Получием зону доставки клиента
			$shipping_zone = WC_Shipping_Zones::get_zone_by('instance_id', $item->get_instance_id());

			// Получием список доступных способов доставки для текущей зоны доставки
			$shipping_methods = $shipping_zone->get_shipping_methods();

			// Перебор доступных способов доставки
			foreach ($shipping_methods as $instance_id => $shipping_method) {

				// Определяем переданный способ доставки
				if ($shipping_method->is_enabled() && $shipping_method->get_rate_id() === $args['shipping_method']) {

					// Обновляем метод доставки
					$item->set_method_title($shipping_method->get_title());
					$item->set_method_id($shipping_method->get_rate_id());
					$item->set_total($shipping_method->cost);

					$item->calculate_taxes($calculate_tax_for);
					$item->save();

					$changed = true;

					break;
				}
			}
		}

		if ($changed) {
			// Calculate totals and save
			$wc_order->calculate_totals(); // the save() method is included
		}

		return $changed;
	}

	/**
	 * Добавление ошибки в объект
	 *
	 * @param string $label
	 * @param string $message
	 * @return void
	 */
	private function set_wc_error($label = '', $message = '')
	{
		if (empty($label) || $message == '')
			return '';

		$this->wc_errors->add($label, $message);
	}

	/**
	 * Получение объекта ошибки заказа
	 *
	 * @return WP_Error
	 */
	public function get_wc_error()
	{
		return $this->wc_errors;
	}
}
