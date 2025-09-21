<?php

defined('ABSPATH') || exit;

add_action('theplugin_woocommerce_breadcrumb', 'pillars_wc_breadcrumb', 20);

add_filter('woocommerce_post_class', 'pillars_wc_post_class_filter', 10, 2);
add_filter('loop_shop_per_page', 'pillars_wc_loop_shop_per_page', 20);
add_filter('loop_shop_columns', 'pillars_wc_loop_columns');

add_filter('woocommerce_output_related_products_args', 'pillars_wc_related_products_args');
// add_filter('woocommerce_add_to_cart_fragments', 'pillars_woocommerce_cart_link_fragment');

if (!function_exists('pillars_woocommerce_cart_link')) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function pillars_woocommerce_cart_link()
	{
		$count		= is_object(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
		$subtotal	= ($count) ? WC()->cart->get_cart_subtotal() : wc_price(0);
?>
		<a class="shopping-cart" href="<?= esc_url(wc_get_cart_url()) ?>" title="<?php esc_attr_e('View your shopping cart', 'pillars'); ?>">
			<?= pillars_theme_get_svg_symbol('shopping-cart') ?>
			<!--<span class="amount"><?= wp_kses_data($subtotal) ?></span>-->
			<span id="pillars_wc_cart_contents_count" class="count"><?= wp_kses_data($count) ?></span>
		</a>
<?php
	}
}

if (!function_exists('pillars_woocommerce_cart_link_fragment')) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function pillars_woocommerce_cart_link_fragment($fragments)
	{

		// TODO понять, как это работает
		ob_start();
		pillars_woocommerce_cart_link();
		$fragments['a.shopping-cart'] = ob_get_clean();

		return $fragments;
	}
}

/**
 * Определение количества Товаров на странице Категорий
 *
 * @return int
 */
function pillars_wc_loop_shop_per_page()
{
	return 24;
}

/**
 * Определение количества колонок при выводе списка Товаров
 *
 * @return int
 */
function pillars_wc_loop_columns()
{
	return 4;
}

/**
 * Определение количества колонок и количества Товаров при выводе в блоке `Похожие товары`
 *
 * @param [type] $args
 * @return array
 */
function pillars_wc_related_products_args($args)
{

	$args['posts_per_page'] = 8; 	// Count related products
	$args['columns'] = 4; 			// Product columns
	return $args;
}

/**
 * Вывод хлебных крошек для Каталога
 *
 * @param array $args
 * @return void
 */
function pillars_wc_breadcrumb($args = array())
{
	$args = wp_parse_args(
		$args,
		apply_filters(
			'woocommerce_breadcrumb_defaults',
			array(
				'delimiter'		=> '',
				'wrap_before'	=> '<ul class="breadcrumbs">',
				'wrap_after'	=> '</ul>',
				'before'		=> '',
				'after'			=> '',
				'home'			=> _x('Home', 'breadcrumb', 'woocommerce'),
			)
		)
	);
	$breadcrumbs = new WC_Breadcrumb();

	if (!empty($args['home'])) {
		$breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', home_url()));
	}
	if (!is_shop()) {
		$args['home'] = get_the_title(wc_get_page_id('shop'));
		$breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', get_permalink(wc_get_page_id('shop'))));
	}
	$args['breadcrumb'] = $breadcrumbs->generate();

	/**
	 * WooCommerce Breadcrumb hook
	 *
	 * @hooked WC_Structured_Data::generate_breadcrumblist_data() - 10
	 */
	do_action('pillars_wc_breadcrumb', $breadcrumbs, $args);
	wc_get_template('global/breadcrumb.php', $args);
}

/**
 * Получение ID товара, если это вариация, то передаём значение родителя
 *
 * @param integer $id
 * @return int
 */
function pillars_wc_get_product_id($id = 0)
{
	$id = absint($id);

	if (empty($id))
		return 0;

	$parent = get_post_ancestors($id);
	if (!empty($parent)) {
		return array_shift($parent);
	}

	return $id;
}

/**
 * Получение первого значение метки Товара из `product_tag` для вывода стоимости или анонса
 *
 * @param int $id
 * @param string $request
 * @return void
 */
function pillars_wc_get_product_price_request($id = 0)
{

	$post_tags = get_the_terms(pillars_wc_get_product_id($id), 'product_tag');

	if ($post_tags) {
		return $post_tags[0]->slug;
	} else {
		return false;
	}
}

/**
 * Вывод html-обёртки цены в засимости от меток Товара
 *
 * @param integer $id
 * @return string
 */
function pillars_wc_get_product_price_html($id = 0)
{
	$price = pillars_wc_get_product_price_request($id);
	if ($price !== false) {
		switch ($price) {
			case 'price-new':
				$price = 'Скоро в продаже';
				break;
			default:
				$price = 'Цена по запросу';
				break;
		}
	} else {
		$price = pillars_wc_get_price_html('', $id);
	}

	return $price;
}

/**
 * Проверка на вывод `content-product` в сетке продуктов
 *
 * @return bool
 */
function pillars_wc_is_shop_loop_item()
{
	$is_loop = false;

	if (is_product_taxonomy()) {
		$is_loop = true;
	} else {
		$object_id	= get_queried_object_id();
		if ($object_id) {
			$page_template	= get_page_template_slug($object_id);
			// TODO добавить фильтр для получения списка шаблонов
			if (in_array($page_template, ['templates/template-search.php', 'templates/template-new-products.php'])) {
				$is_loop = true;
			}
		}
	}

	return $is_loop;
}

/**
 * Вывод доп информации для галереи
 *
 * @param string $product_id
 * @return void
 */
function pillars_wc_get_product_gallery_notice($product_id = '')
{
	if (empty($product_id))
		return '';

	$notice = get_post_meta($product_id, '_product_gallery_notice', true);
	if ($notice) {
		return '<span class="info pillars-wc-gallery-notice"><i>i</i>' . $notice . '</span>';
	}

	return '';
}

/**
 * Фильтр для обработки css-классов Товара
 *
 * @param [type] $classes
 * @param [type] $product
 * @return void
 */
function pillars_wc_post_class_filter($classes, $product)
{
	foreach ($classes as $i => $class) {
		if (strpos($class, 'product_cat-') !== false) {
			unset($classes[$i]);
		}

		if (in_array($class, ['has-default-attributes', 'has-post-thumbnail', 'featured', 'purchasable', 'type-product'])) {
			unset($classes[$i]);
		}
	}

	$classes = array_values($classes);

	if (pillars_wc_is_shop_loop_item()) {
		$classes[] = 'block';
	}

	return $classes;
}

/**
 * Получение списка изображений для карточки товара
 *
 * @param WC_Product|WC_Product_Variable $product
 * @return array
 */
function pillars_wc_get_product_gallery_images($product)
{
	$post_thumbnail_id	= $product->get_image_id();
	$attachment_ids		= $product->get_gallery_image_ids();

	$images	= array();

	$videos	= get_post_meta($product->get_id(), '_pillars_product_gallery_video', true);
	if ($videos && is_array($videos)) {
		if ($videos['cover_id'] && $videos['url']) {
			$images[] = array(
				'thumb' 	=> wp_get_attachment_image_url($videos['cover_id'], 'thumbnail'),
				'image' 	=> wp_get_attachment_image($videos['cover_id'], 'woocommerce_single'),
				'link'		=> theplugin_get_video_embed_link($videos['url']),
				'class'		=> '',
				'iframe'	=> true
			);
		}
	}
	$image_ids = array();
	if ($post_thumbnail_id) {
		$image_ids[] = $post_thumbnail_id;
	}
	if ($attachment_ids) {
		$image_ids = array_values(array_unique(array_merge($image_ids, $attachment_ids)));
	}
	if ($image_ids) {
		foreach ($image_ids as $attachment_id) {
			$images[] = array(
				'thumb' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
				'image' => wp_get_attachment_image($attachment_id, 'woocommerce_single'),
				'link'	=> wp_get_attachment_image_url($attachment_id, 'full'),
				'class'	=> ''
			);
		}
	}

	return $images;
}

/**
 * Получение цены Товара, если такой существует по слагу атрибута
 *
 * @param string $slug
 * @param boolean $json
 * @return array|string
 */
function pillars_wc_object_has_price($slug = '')
{
	if (!$slug)
		return '';

	$page = get_page_by_path($slug, OBJECT, 'product');
	if ($page) {
		$product = wc_get_product($page->ID);
		return $product->get_price();
	}

	return '';
}

/**
 * Форматирование цены в массив цены и html-обёртки
 *
 * @param integer $price
 * @return void
 */
function pillars_wc_object_get_price_attrs($price = 0)
{
	$price = preg_replace('#[^0-9]#', '', $price);
	if ($price) {
		return sprintf(
			'data-price="%s"',
			esc_attr(
				theplugin_json_encode([
					'price'			=> $price,
					'price_html'	=> strip_tags(wc_price($price))
				])
			)
		);
	}

	return '';
}
