<?php

defined('ABSPATH') || exit;

add_action('init', 'pillars_wc_get_related_products');

add_action('pillars_wc_single_product_title', 'woocommerce_template_single_title', 5);

add_action('pillars_wc_single_product_images_before', 'pillars_wc_product_custom_tags_wrapper', 5);

add_action('pillars_wc_single_product_additional', 'pillars_wc_single_product_advantages', 10);
add_action('pillars_wc_single_product_additional', 'pillars_wc_single_product_link_to_certificate', 15);
add_action('pillars_wc_single_product_additional', 'pillars_wc_single_product_link_to_tab', 15);
add_action('pillars_wc_single_product_additional', 'pillars_wc_single_product_link_to_video', 20);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 5);

add_action('woocommerce_before_add_to_cart_form', 'pillars_wc_product_has_siblings', 10);
add_action('woocommerce_before_add_to_cart_form', 'pillars_wc_product_has_backlight', 10);
add_action('woocommerce_single_variation', 'pillars_wc_single_variation_add_to_cart_button', 20);

add_action('woocommerce_after_quantity_input_field', 'pillars_wc_single_product_discount_info', 5);

add_action('wc_product_production_after', 'pillars_wc_single_simple_after_add_to_cart_button', 5);

add_action('woocommerce_after_single_product_summary', 'pillars_wc_output_related_products', 20);

add_action('woocommerce_after_single_product', 'pillars_wc_single_product_news_slider', 5);

add_filter('pillars_wc_attribute_options_additional_filter', 'pillars_wc_attribute_options_additional', 5, 1);
add_filter('pillars_wc_attribute_label',		'pillars_wc_attribute_label_filter', 10, 3);
add_filter('woocommerce_product_tabs',			'pillars_wc_product_tabs');
add_filter('woocommerce_product_additional_information_heading', 'pillars_wc_product_additional_information_heading');
add_filter('woocommerce_attribute',				'pillars_wc_product_woocommerce_attribute_filter', 10, 3);
add_filter('wc_product_enable_dimensions_display', 'pillars_wc_product_enable_dimensions_display_filter', 10, 1);

/**
 * Шаблон вывода списка приемуществ на странице Товара
 *
 * @return void
 */
function pillars_wc_single_product_advantages()
{
	wc_get_template('single-product/advantages.php');
}

/**
 * Шаблон вывода ссылки на Сертификат на странице Товара
 *
 * @return void
 */
function pillars_wc_single_product_link_to_certificate()
{
	wc_get_template('single-product/link-to-certificate.php');
}

/**
 * Шаблон вывода ссылки-якоря на Харакетристики на странице Товара
 *
 * @return void
 */
function pillars_wc_single_product_link_to_tab()
{
	wc_get_template('single-product/link-to-tab.php');
}

/**
 * Шаблон вывода ссылки-якоря на видео-обзор на странице Товара
 *
 * @return void
 */
function pillars_wc_single_product_link_to_video()
{
	wc_get_template('single-product/link-to-video.php');
}

/**
 * Вывод родственных товаров по параметру
 *
 * @return void
 */
function pillars_wc_product_has_siblings()
{
	global $product;

	$siblings = get_post_meta($product->get_id(), '_product_siblings', true);
	if ($siblings && isset($siblings['label']) && isset($siblings['values'])) {
		$siblings['id'] = $product->get_id();
		foreach ($siblings['values'] as $id => $item) {
			if (get_post_status($id) != 'publish') {
				unset($siblings['values'][$id]);
			}
		}
		wc_get_template('single-product/add-to-cart/siblings.php', $siblings);
	}
}

/**
 * Вывод родственных товаров по подстветке у несветовых
 *
 * @return void
 */
function pillars_wc_product_has_backlight()
{
	global $product;

	$backlight = get_post_meta($product->get_id(), '_product_backlight', true);
	if ($backlight && isset($backlight['label']) && isset($backlight['values'])) {
		$backlight['id'] = $product->get_id();
		foreach ($backlight['values'] as $id => $item) {
			if (get_post_status($id) != 'publish') {
				unset($backlight['values'][$id]);
			}
		}
		wc_get_template('single-product/add-to-cart/backlight.php', $backlight);
	}
}

/**
 * Вывод выбор опций вариативного товара для использования в формах корзины.
 *
 * @param array $args
 * @return void
 */
function pillars_wc_dropdown_variation_attribute_options($args = array())
{
	$args = wp_parse_args(
		apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args),
		array(
			'options'			=> false,
			'attribute'			=> false,
			'product'			=> false,
			'selected'			=> false,
			'required'			=> false,
			'name'				=> '',
			'id'				=> '',
			'class'				=> '',
			'show_option_none'	=> __('Choose an option', 'woocommerce'),
		)
	);

	// Get selected value.
	if (false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
		$selected_key = 'attribute_' . sanitize_title($args['attribute']);
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	$options				= $args['options'];
	$product				= $args['product'];
	$attribute				= $args['attribute'];
	$name					= $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
	$id						= $args['id'] ? $args['id'] : sanitize_title($attribute);
	$class					= $args['class'];
	$required				= (bool) $args['required'];
	$show_option_none		= (bool) $args['show_option_none'];
	$show_option_none_text	= $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce'); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

	if (empty($options) && !empty($product) && !empty($attribute)) {
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[$attribute];
	}

	$html  = sprintf(
		'<select id="%s" class="%s" name="%s" data-attribute_name="attribute_%s" data-show_option_none="%s"%s>',
		esc_attr($id),
		esc_attr($class),
		esc_attr($name),
		esc_attr(sanitize_title($attribute)),
		'no',
		$required ? ' required' : ''
	);
	$html .= '<option disabled value="">' . esc_html($show_option_none_text) . '</option>';

	if (!empty($options)) {
		if ($product && taxonomy_exists($attribute)) {
			// Get terms if this is a taxonomy - ordered. We need the names too.
			$terms = wc_get_product_terms(
				$product->get_id(),
				$attribute,
				array(
					'fields' => 'all',
				)
			);

			foreach ($terms as $term) {
				if (in_array($term->slug, $options, true)) {
					$html .= sprintf(
						'<option value="%s" %s %s>%s</option>',
						esc_attr($term->slug),
						selected(sanitize_title($args['selected']), $term->slug, false),
						join(' ', apply_filters('pillars_wc_attribute_options_additional_filter', $term)),
						esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product))
					);
				}
			}
		} else {
			foreach ($options as $option) {
				// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
				$html    .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr($option),
					sanitize_title($args['selected']) === $args['selected'] ? selected($args['selected'], sanitize_title($option), false) : selected($args['selected'], $option, false),
					esc_html(apply_filters('woocommerce_variation_option_name', $option, null, $attribute, $product))
				);
			}
		}
	}

	$html .= '</select>';

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters('woocommerce_dropdown_variation_attribute_options_html', $html, $args);
}

/**
 * Получения дополнительных параметров переданного термина (опции атрибута товара)
 *
 * @param WP_Term $term
 * @return array
 */
function pillars_wc_attribute_options_additional($term)
{
	$data = array();

	// Есть ли товар с таким же слагом, как у термина
	$has_price = pillars_wc_object_has_price($term->slug);
	if ($has_price) {
		$data[] = pillars_wc_object_get_price_attrs($has_price);
	}

	// Есть ли описание термина
	if ($term->description) {
		$data[] = 'data-description="' . esc_attr($term->description) . '"';
	}

	if (get_term_meta($term->term_id, 'color_hexcode', true)) {
		$data[] = 'data-color="' . esc_attr(get_term_meta($term->term_id, 'color_hexcode', true)) . '"';
	}

	return $data;
}

/**
 * Вывод кнопки тега заказа по шорткоду
 *
 * @param string $price
 * @return void
 */
function pillars_wc_single_has_price_request_shortcode($price = '', $product_id = 0, $tag_echo = false)
{
	switch ($price) {
		case 'price-new':
			$form		= 'price';
			$price_txt	= 'Скоро в продаже';
			break;
		default:
			$form		= 'price';
			$price_txt	= 'Цена по запросу';
			break;
	}

	echo ($tag_echo) ? '<div class="pillars-wc-product-price__request">' . $price_txt . '</div>' : '';
	echo do_shortcode(sprintf(
		'[get-popup id="%s" form="%s" text="%s" class="submit" container="a" args="%s"]',
		$price,
		$form,
		$price_txt,
		theplugin_array_to_args(['page_id' => $product_id])
	));
}

/**
 * Вывод кнопки заказа в вариативных товарах
 *
 * @return void
 */
function pillars_wc_single_variation_add_to_cart_button()
{
	global $product;

	$price = pillars_wc_get_product_price_request($product->get_id());
	if ($price !== false) {
		pillars_wc_single_has_price_request_shortcode($price, $product->get_id(), false);
	} else {
		wc_get_template('single-product/add-to-cart/variation-add-to-cart-button.php');
	}

	wc_get_template('single-product/add-to-cart/prodaction.php');
}

/**
 * Вывод кнопки на `Индивидуальное производство` в простых товарах
 *
 * @return void
 */
function pillars_wc_single_simple_after_add_to_cart_button()
{
	global $product;

	echo do_shortcode(sprintf(
		'[get-popup id="%s" form="%s" text="%s" class="btn-1 alt btn-full" container="div" args="%s"]',
		'get-3d-model',
		'get3Dmodel',
		'Запросить 3D-модель',
		theplugin_array_to_args(['page_id' => $product->get_id()])
	));
}

/**
 * Undocumented function
 *
 * @return void
 */
function pillars_wc_single_production_after_3d_model()
{
	global $product;

	wc_get_template('single-product/add-to-cart/prodaction.php');
}

/**
 * Вывод информации по скидки товара, если есть данные
 *
 * @return void
 */
function pillars_wc_single_product_discount_info()
{
	if (is_product()) {
		global $product;

		$count	= get_post_meta($product->get_id(), '_product_discounts_count', true);
		$per	= get_post_meta($product->get_id(), '_product_discounts_per', true);

		if (!$count || !$per)
			return '';

		echo sprintf(
			'<div class="pillars-wc-quantity__discounts product-id-%d" data-product_discounts="%s">Скидка %s (от %d шт.)</div>',
			$product->get_id(),
			esc_attr(json_encode(array('count' => absint($count), 'per' => absint($per)))),
			$per . '%',
			$count
		);
	}
}

/**
 * Вывод секции новостей
 *
 * @return void
 */
function pillars_wc_single_product_news_slider()
{
	echo do_shortcode('[tp-get-part part="news-slider"]');
}

/**
 * Получение наименование атрибута продукта
 *
 * @param [type] $name
 * @param string $product
 * @return string
 */
function pillars_wc_attribute_label($name, $product = '')
{
	$label = wc_attribute_label($name, $product);
	return apply_filters('pillars_wc_attribute_label', $label, $name, $product);
}

/**
 * Фильтр на вывод наименование атрибутов вариаций товара
 *
 * @param  $label
 * @param  $name
 * @param  $product
 *
 * @return
 */
function pillars_wc_attribute_label_filter($label, $name, $product)
{

	$label = strtr($label, [
		'Типы питания'					=> 'Питание',
		'Типы подсветки'				=> 'Подсветка',
		'Цвет металлического покрытия'	=> 'Цвет металла'
	]);

	return $label;
}

/**
 * Получение списка табов для вывода в Товаре
 *
 * @param [type] $tabs
 * @return array
 */
function pillars_wc_product_tabs($tabs)
{
	unset($tabs['reviews']);

	global $post;

	// Характеристики
	if (isset($tabs['additional_information'])) {
		$tabs['additional_information']['title']	= apply_filters('woocommerce_product_additional_information_heading', '');
		$tabs['additional_information']['priority']	= 10;
	}

	// Информация об упаковке
	if (get_post_meta($post->ID, '_tab_packing')) {
		$tabs['guarantee'] = array(
			'title' 	=> __pl('Информация об упаковке'),
			'priority' 	=> 20,
			'callback' 	=> 'pillars_wc_product_tabs_packing'
		);
	}

	// Способы доставки
	$tabs['shipping'] = array(
		'title' 	=> __pl('Доставка'),
		'priority'	=> 30,
		'callback'	=> 'pillars_wc_product_tabs_shipping'
	);

	// Описание
	if (isset($tabs['description'])) {
		$tabs['description']['priority'] = 40;
	}

	// Почему стоит выбрать нас?
	$tabs['advantages'] = array(
		'title' 	=> __pl('Почему стоит выбрать нас?'),
		'priority'	=> 45,
		'callback'	=> 'pillars_wc_product_tabs_advantages'
	);

	// Видео-обзор
	$post_meta_video = get_post_meta($post->ID, '_product_video_review', true);
	if (isset($post_meta_video['url']) && $post_meta_video['url']) {
		$tabs['videoreview'] = array(
			'title' 	=> __pl('Видео-обзор'),
			'priority' 	=> 50,
			'callback' 	=> 'pillars_wc_product_tabs_videoreview'
		);
	}

	// Коллекции
	$collection = get_post_meta($post->ID, '_product_section_collection', true);
	if ($collection) {
		$tabs['brochure'] = array(
			'title' 	=> __pl('О коллекции'),
			'priority'	=> 60,
			'callback'	=> 'pillars_wc_product_tabs_brochure'
		);
	}

	// Цветность
	$tags = wp_get_object_terms($post->ID, 'pa_material');
	if ($tags) {
		foreach ($tags as $tag) {
			if ($tag->slug == 'polietilen') {
				$tabs['chroma'] = array(
					'title' 	=> __pl('Расцветки'),
					'priority'	=> 60,
					'callback'	=> 'pillars_wc_product_tabs_chroma'
				);
			}
		}
	}

	// Режимы свечения
	$tabs['glowmodes'] = array(
		'title' 	=> __pl('Режимы свечения'),
		'priority'	=> 70,
		'callback'	=> 'pillars_wc_product_tabs_glowmodes'
	);

	// Галерея
	if (get_post_meta($post->ID, '_galereya_primeneniya') && false) {
		$tabs['gallery'] = array(
			'title' 	=> __pl('Галерея'),
			'priority' 	=> 80,
			'callback' 	=> 'pillars_wc_product_tabs_gallery'
		);
	}

	unset($tabs['glowmodes']);

	return $tabs;
}

/**
 * Undocumented function
 *
 * @return string
 */
function pillars_wc_product_additional_information_heading()
{
	return __pl('Характеристики');
}

/**
 * Вывод секции для табов "Информация об упаковке"
 *
 * @return void
 */
function pillars_wc_product_tabs_packing()
{
	wc_get_template('single-product/tabs/packing.php');
}

/**
 * Вывод секции для табов "Способы доставки"
 *
 * @return void
 */
function pillars_wc_product_tabs_shipping()
{
	wc_get_template('single-product/tabs/shipping.php');
}

/**
 * Вывод секции для табов "Видео-обзор"
 *
 * @return void
 */
function pillars_wc_product_tabs_videoreview()
{
	wc_get_template('single-product/tabs/videoreview.php');
}

/**
 * Вывод секции для табов "Брошюра"
 *
 * @return void
 */
function pillars_wc_product_tabs_brochure()
{
	wc_get_template('single-product/tabs/brochure.php');
}


/**
 * Вывод секции для табов "Цветность"
 *
 * @return void
 */
function pillars_wc_product_tabs_advantages()
{
	wc_get_template('single-product/tabs/advantages.php');
}

/**
 * Вывод секции для табов "Цветность"
 *
 * @return void
 */
function pillars_wc_product_tabs_chroma()
{
	wc_get_template('single-product/tabs/chroma.php');
}

/**
 * Вывод секции для табов "Режимы свечения"
 *
 * @return void
 */
function pillars_wc_product_tabs_glowmodes()
{
	wc_get_template('single-product/tabs/glowmodes.php');
}

/**
 * Вывод секции для табов "Галерея"
 *
 * @return void
 */
function pillars_wc_product_tabs_gallery()
{
	wc_get_template('single-product/tabs/gallery.php');
}

/**
 * Function for `woocommerce_attribute` filter-hook.
 *
 * @param  $wpautop
 * @param  $attribute
 * @param  $values
 *
 * @return
 */
function pillars_wc_product_woocommerce_attribute_filter($wpautop, $attribute, $values)
{
	return strip_tags($wpautop);
}

/**
 * Удаляем из характеристик Товара габариты и вес
 *
 * @param  $condition
 *
 * @return bool|string
 */
function pillars_wc_product_enable_dimensions_display_filter($condition)
{
	if (is_product())
		$condition = '';

	return $condition;
}

/**
 * Hook for output the related products
 *
 * @return void
 */
function pillars_wc_output_related_products()
{

	$args = array(
		'posts_per_page'	=> 4,
		'columns'			=> 4,
		'orderby'			=> 'rand', // @codingStandardsIgnoreLine.
	);

	pillars_wc_related_products(apply_filters('woocommerce_output_related_products_args', $args));
}

/**
 * Output the related products
 *
 * @param array $args
 * @return void
 */
function pillars_wc_related_products($args = array())
{
	global $product;

	if (!$product) {
		return;
	}

	$defaults = array(
		'posts_per_page'	=> 2,
		'columns'			=> 2,
		'orderby'			=> 'rand', // @codingStandardsIgnoreLine.
		'order'				=> 'desc',
	);

	$args = wp_parse_args($args, $defaults);

	// Get visible related products then sort them at random.
	$args['related_products'] = array_filter(array_map('wc_get_product', pillars_wc_get_related_products($product->get_id(), $args['posts_per_page'], $product->get_upsell_ids())), 'wc_products_array_filter_visible');

	// Handle orderby.
	$args['related_products'] = wc_products_array_orderby($args['related_products'], $args['orderby'], $args['order']);

	// Set global loop values.
	wc_set_loop_prop('name', 'related');
	wc_set_loop_prop('columns', apply_filters('woocommerce_related_products_columns', $args['columns']));

	wc_get_template('single-product/related.php', $args);
}

/**
 * Подбор товаров "Related Products" с отключением подбора по тегам
 *
 * @source https://opttour.ru/wordpress/pravilnyie-pohozhie-tovaryi-v-woocommerce/
 *
 * @param integer $id
 * @param integer $limit
 * @return array
 */
function pillars_wc_get_related_products($id, $limit = 5, $exclude_ids = array())
{
	global $woocommerce;

	// Related products are found from category and tag
	$tags_array = array(0);
	$cats_array = array(0);

	// Get tags
	// $terms = wp_get_post_terms($id, 'product_tag');
	// foreach ( $terms as $term ) $tags_array[] = $term->term_id;

	// Get categories (removed by NerdyMind)
	$terms = wp_get_post_terms($id, 'product_cat');
	foreach ($terms as $term) $cats_array[] = $term->term_id;
	// Don't bother if none are set
	if (sizeof($cats_array) == 1 && sizeof($tags_array) == 1) return array();

	// Meta query
	$meta_query = array();
	$meta_query[] = $woocommerce->query->visibility_meta_query();
	$meta_query[] = $woocommerce->query->stock_status_meta_query();

	// Get the posts
	$related_posts = get_posts(apply_filters('woocommerce_product_related_posts', array(
		'orderby'			=> 'rand',
		'posts_per_page'	=> $limit,
		'post_type'			=> 'product',
		'fields'			=> 'ids',
		'meta_query'		=> $meta_query,
		'exclude_ids'		=> $exclude_ids,
		'tax_query'			=> array(
			'relation'			=> 'OR',
			array(
				'taxonomy'		=> 'product_cat',
				'field'			=> 'id',
				'terms'			=> $cats_array
			),
			array(
				'taxonomy'		=> 'product_tag',
				'field'			=> 'id',
				'terms'			=> $tags_array
			)
		)
	)));

	$related_posts = array_diff($related_posts, array($id));

	return $related_posts;
}
