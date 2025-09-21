<?php

defined('ABSPATH') || exit;

// TODO возможно не требуется

add_action('woocommerce_checkout_before_order_review', 'woocommerce_order_review', 10);
add_action('woocommerce_after_shipping_rate', 'art_action_after_shipping_rate', 20, 2);
add_action('wp_footer', 'awoohc_add_script_update_shipping_method', 99);
add_action('wp_footer', 'pillars_wc_checkout_async_upload_form', 99);

add_action('woocommerce_thankyou', 'pillars_wc_order_details_table', 10);

add_filter('woocommerce_update_order_review_fragments', 'pillars_wc_update_order_review_fragments_filter', 99);
add_filter('wc_add_to_cart_message_html', '__return_false'); // отключает оповещение `товар успешно добавлен в корзину`
add_filter('woocommerce_checkout_fields', 'pillars_wc_checkout_fields_filter');


/**
 * @snippet       Вспомогательная функция для вывода сообщения
 * @sourcecode    https://wpruse.ru/?p=4114
 * @testedwith    WooCommerce 3.9
 *
 * @param  string $desc Входящий параметр
 *
 * @author        Artem Abramovich
 */
function art_shipping_method_notice($desc = '')
{

	if (empty($desc)) {
		return;
	}
?>
	<div class="order-notice">
		<?= $desc ?>
	</div>
<?php

}

/**
 * Функция вывода сообщения для выбранного метода доставки
 *
 * @source			https://wpruse.ru/?p=4114
 * @testedwith		WooCommerce 3.9
 *
 * @param  object $method объект метода доставки
 * @param  int    $index  счетчик
 *
 * @author        Artem Abramovich
 */
function art_action_after_shipping_rate($method, $index)
{

	// Переменная для сообщения
	$notice = '';

	// Если корзина, то ничего не выводим
	if (is_cart()) {
		return;
	}

	// Получаем выбранный метод
	$chosen_methods = WC()->session->get('chosen_shipping_methods');

	// Самовывоз
	if ('local_pickup:3' === $chosen_methods[0] && 'local_pickup:3' === $method->id) {
		// Сообщение для конкретного способа доставки
		$notice = '<p>Адрес: ул. Фронтовых Бригад, 18 А, ул. Белинского, 83</p>';
	}

	// По тарифам ТК
	if ('flat_rate:4' === $chosen_methods[0] && 'flat_rate:4' === $method->id) {
		$notice = '<p>По тарифам ТК, оплачивается заказчиком.</p>';
	}

	// Бесплатная доставка
	if ('free_shipping:7' === $chosen_methods[0] && 'free_shipping:7' === $method->id) {
		$notice = '<p>Курьером в Екатеринбурге.</p>';
	}

	// За пределами СНГ
	if ('flat_rate:2' === $chosen_methods[0] && 'flat_rate:2' === $method->id) {
		$notice = '<p>Для обсуждения возможности доставки с Вами свяжется наш менеджер.</p>';
	}

	if ($notice) {
		// Вывод сообщения.
		art_shipping_method_notice($notice);
	}
}

/**
 * Добавляем часть формы к фрагменту
 */

function pillars_wc_update_order_review_fragments_filter($fragments)
{
	$checkout = WC()->checkout();
	ob_start();
?>
	<div class="woocommerce-billing-fields__field-wrapper">
		<div class="woocommerce-additional-fields__field-wrapper"></div>
		<?php
		$fields = $checkout->get_checkout_fields('billing');
		foreach ($fields as $key => $field) {
			if (isset($field['country_field'], $fields[$field['country_field']])) {
				$field['country'] = $checkout->get_value($field['country_field']);
			}
			woocommerce_form_field($key, $field, $checkout->get_value($key));
		}
		?>
	</div>

	<?php
	$art_add_update_form_billing              = ob_get_clean();
	$fragments['.woocommerce-billing-fields__field-wrapper'] = $art_add_update_form_billing;

	return $fragments;
}

/*
 * Убираем поля для конкретного способа доставки
 */

function pillars_wc_checkout_fields_filter($fields)
{
	// получаем выбранные метод доставки
	$chosen_methods = WC()->session->get('chosen_shipping_methods');

	// проверяем текущий метод и убираем не ненужные поля
	// Самовывоз
	if ('local_pickup:3' === $chosen_methods[0]) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_1']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_state']);
	}

	// По тарифам ТК
	if ('flat_rate:4' === $chosen_methods[0]) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_2']);

		$fields['billing']['billing_country']['class'][0] 	= 'col-md-6';
		$fields['billing']['billing_state']['class'][0] 		= 'col-md-6';
		$fields['billing']['billing_postcode']['class'][0] 	= 'col-md-4';
		$fields['billing']['billing_city']['class'][0] 		= 'col-md-8';
		$fields['billing']['billing_address_1']['class'][0] 	= 'col-12';
	}

	// Бесплатная доставка
	if ('free_shipping:7' === $chosen_methods[0]) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_state']);

		$fields['billing']['billing_postcode']['required'] 	= false;
	}

	// За пределами СНГ
	if ('flat_rate:2' === $chosen_methods[0]) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_2']);

		$fields['billing']['billing_country']['class'][0] 	= 'col-md-4';
		$fields['billing']['billing_state']['class'][0] 		= 'col-md-8';
		$fields['billing']['billing_postcode']['class'][0] 	= 'col-md-4';
		$fields['billing']['billing_city']['class'][0] 		= 'col-md-8';
		$fields['billing']['billing_address_1']['class'][0] 	= 'col-12';

		$fields['billing']['billing_state']['label'] 			= 'Страна';
	}

	if (!$chosen_methods[0]) {
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_1']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_state']);
	}

	return $fields;
}

/*
* Обновление формы
*/
function awoohc_add_script_update_shipping_method()
{
	if (is_checkout()) {
		$chosen_methods = WC()->session->get('chosen_shipping_methods');
	?>
		<!--Скроем поле Страна. Если используется поле Страна, то следует убрать скрытие-->
		<style>
			#billing_country_field {
				display: inherit !important;
			}
		</style>
		<!--Выполняем обновление полей при переключении доставки-->
		<script id="xhr_updated_shipping_method">
			$(document).ready(function() {
				var first_name = '',
					last_name = '',
					phone = '',
					email = '';

				$(document).on('updated_checkout updated_shipping_method', function(event, xhr, data) {
					$('input[name^="shipping_method"]').on('change', function() {
						first_name = $('#billing_first_name').val();
						last_name = $('#billing_last_name').val();
						phone = $('#billing_phone').val();
						email = $('#billing_email').val();
						$('.woocommerce-billing-fields__field-wrapper').block({
							message: null,
							overlayCSS: {
								background: '#fff',
								'z-index': 1000000,
								opacity: 0.3
							}
						});
					});

					$(".woocommerce-billing-fields__field-wrapper").html(xhr.fragments['.woocommerce-billing-fields__field-wrapper']);
					$(".woocommerce-billing-fields__field-wrapper").find('input[name="billing_first_name"]').val(first_name);
					$(".woocommerce-billing-fields__field-wrapper").find('input[name="billing_last_name"]').val(last_name);
					$(".woocommerce-billing-fields__field-wrapper").find('input[name="billing_phone"]').val(phone).maskPhone();
					$(".woocommerce-billing-fields__field-wrapper").find('input[name="billing_email"]').val(email);
					$('.woocommerce-billing-fields__field-wrapper').unblock();
				});
			});
		</script>
	<?php
	}
}

// Checkout

/**
 * Добавляем асинхронную загрузку формы оформления заказа
 *
 * @return void
 */
function pillars_wc_checkout_async_upload_form()
{
	if (is_checkout()) { ?>
		<script>
			$(document).ready(function() {
				setTimeout(function() {
					if ($('.woocommerce form[name="checkout"]').length) {

						var $woo = $('.woocommerce form[name="checkout"]').closest('.woocommerce');
						var $form = 'action=pillars_wc_checkout&form-type=get_checkout';
						$form += '&woocommerce-process-checkout-nonce=' + $('input#woocommerce-process-checkout-nonce').val();
						$form += '&_wp_http_referer=';

						$woo.addClass('block-loading');

						$.ajax({
							type: 'post',
							url: window.wp_data.ajax_url,
							data: $form,
							traditional: true,
							success: function(data) {
								var data = $.parseJSON(data);
								var $block = $woo.parent();

								console.log('data', data);

								$block.scrollToObj();

								switch (data.type) {
									case 'ok':
										$woo.remove();
										$block.append(data.message);

										break;
									default:
										$woo.find('form').removeAttr('style');
										$woo.removeClass('block-loading');
										console.warn('Результат не получен: ');
										console.warn(data);
										break;
								}
							},
							error: function(data) {
								$woo.find('form').removeAttr('style');
								$woo.removeClass('block-loading');
								console.warn('Ошибка: ');
								console.warn(data);
							}
						});
					}
				}, 200);
			});
		</script>
<?php }
}

/**
 * Показ деталей заказа на страницах его успешного добавления
 *
 * @param [type] $order_id
 * @return void
 */
function pillars_wc_order_details_table($order_id)
{
	if (!$order_id) {
		return;
	}

	$order = wc_get_order($order_id);

	if (!$order) {
		return;
	}

	wc_get_template(
		'checkout/order-details.php',
		array(
			'order_id'       => $order_id,
			/**
			 * Determines if the order downloads table should be shown (in the context of the order details
			 * template).
			 *
			 * By default, this is true if the order has at least one dowloadable items and download is permitted
			 * (which is partly determined by the order status). For special cases, though, this can be overridden
			 * and the downloads table can be forced to render (or forced not to render).
			 *
			 * @since 8.5.0
			 *
			 * @param bool     $show_downloads If the downloads table should be shown.
			 * @param WC_Order $order          The related order.
			 */
			'show_downloads' => apply_filters('woocommerce_order_downloads_table_show_downloads', ($order->has_downloadable_item() && $order->is_download_permitted()), $order),
		)
	);
}

/**
 * Перевод шаблона текста ссылки `Privacy Policy`
 *
 * @return string
 */
function pillars_wc_replace_policy_page_link_placeholders()
{
	$privacy_page_id	= wc_privacy_policy_page_id();
	$privacy_link		= $privacy_page_id ? '<a href="' . esc_url(get_permalink($privacy_page_id)) . '" class="woocommerce-privacy-policy-link" target="_blank">' . __('политикой конфиденциальности', 'woocommerce') . '</a>' : __('политикой конфиденциальности', 'woocommerce');

	$personal_data_policy_link = sprintf(
		'<a class="privacy-policy-link" href="%s" target="_blank">обработкой персональных данных</a>',
		pillars_get_personal_data_policy_url()
	);

	$promotional_newsletters_policy = sprintf(
		'<a class="privacy-policy-link" href="%s" target="_blank">получением рекламных рассылок</a>',
		pillars_get_promotional_newsletters_policy_url()
	);

	$text = wc_get_privacy_policy_text('checkout');

	$find_replace = array(
		'[privacy_policy]' => $privacy_link,
		'[personal_data_policy]' => $personal_data_policy_link,
		'[promotional_newsletters_policy]' => $promotional_newsletters_policy
	);

	return str_replace(array_keys($find_replace), array_values($find_replace), $text);
}

/**
 * Вывод переведённого текста ссылки `Privacy Policy`
 *
 * @return void
 */
function pillars_wc_terms_and_conditions_checkbox_text()
{
	echo wp_kses_post(pillars_wc_replace_policy_page_link_placeholders());
}
