<?php

defined('ABSPATH') || exit;

add_action('pillars_wc_before_shop_loop_item', 'pillars_wc_before_shop_loop_item_col_open', 5);

add_action('woocommerce_before_shop_loop_item_title', 'pillars_wc_template_loop_product_thumbnail', 10);

add_action('pillars_wc_after_shop_loop_item', 'pillars_wc_after_shop_loop_item_col_close', 5);

function pillars_wc_before_shop_loop_item_col_open()
{

	if (pillars_wc_is_shop_loop_item()) {
		switch (wc_get_loop_prop('columns')) {
			case 4:
				$class = 'col-sm-3 col-6';
				break;
			case 3:
				$class = 'col-sm-4 col-6';
				break;
			default:
				$class = 'col-6';
				break;
		}

		echo '<div class="' . $class . '">';
	}
}

/**
 * HTML-обёртка вывода кастомных тегов Товара
 *
 * @return string
 */
function pillars_wc_product_custom_tags_wrapper($echo = true)
{
	global $product;

	$wrapper = '';

	$tags = [];
	$keys = [
		'_product_certificate_id'	=> ['class' => 'certificate', 'label' => 'Сертификат', 'link' => false],
		'_product_in_stock'			=> ['class' => 'instock', 'label' => 'В наличии', 'link' => false],
		'_product_featured'			=> ['class' => 'featured', 'label' => 'Хит продаж', 'link' => false],
		'_product_module_sys_link'	=> ['class' => 'module', 'label' => 'Модульная система', 'link' => true],
	];

	foreach ($keys as $key => $label) {
		$meta = get_post_meta($product->get_id(), $key, true);
		if ($meta) {
			$tags[$key] = $label;
			// Если активен флаг `link`, то в параметр записываем значение мета-данных
			if ($label['link'] && !theplugin_is_mobile()) {
				$tags[$key]['link'] = $meta;
			}
		}
	}

	// Проверка категории на модульную систему
	if (!isset($tags['_product_module_sys_link'])) {
		foreach ($product->get_category_ids() as $cat_id) {
			$product_cat = get_term($cat_id);
			if (in_array($product_cat->slug, ['modulnye-skamejki', 'modulnye-pergoly'])) {
				$tags['_product_module_sys_link'] = ['class' => 'module', 'label' => 'Модульная система', 'link' => false];
			}
		}
	}

	if ($tags) {
		$wrapper .= '<div class="pillars-wc-product-tags">';
		foreach ($tags as $label) {
			if ($label['link']) {
				$wrapper .= sprintf('<span data-href="%s" class="pillars-wc-product-tag --%s">%s</span>', $label['link'], $label['class'], $label['label']);
			} else {
				$wrapper .= sprintf('<span class="pillars-wc-product-tag --%s">%s</span>', $label['class'], $label['label']);
			}
		}
		$wrapper .= '</div>';
	}

	if ($echo) {
		echo $wrapper;
	} else {
		return $wrapper;
	}
}

if (!function_exists('pillars_wc_template_loop_product_thumbnail')) {
	/**
	 * Get the product thumbnail for the loop.
	 */
	function pillars_wc_template_loop_product_thumbnail()
	{ ?>
		<div class="cover loader-bg">
			<?php

			pillars_wc_product_custom_tags_wrapper();

			global $product;

			$cover	= absint(get_post_meta($product->get_id(), '_pillars_product_image_alter_view', true));
			$size	= 'woocommerce_thumbnail';

			if ($cover) {
				echo wp_get_attachment_image($cover, $size, true, ['class' => '', 'loading' => 'lazy']);
			} else {
				echo woocommerce_get_product_thumbnail(); // WPCS: XSS ok.
			}

			if (!theplugin_is_mobile()) {
				$video	=  absint(get_post_meta($product->get_id(), '_pillars_product_image_video_view', true));
				if ($video && false) {
					echo sprintf(
						'<video class="hover-light" style="width:%s;" preload="auto" autoplay loop muted><source src="%s" type=\'video/webm; codecs="vp8, vorbis"\' /></video>',
						'100%',
						wp_get_attachment_url($video)
					);
					// echo "<script>document.addEventListener('DOMContentLoaded', (event) => {document.querySelector('video').play();});</script>";
				} else {
					$cover	= absint(get_post_meta($product->get_id(), '_pillars_product_image_second_view', true));
					if ($cover) {
						echo wp_get_attachment_image($cover, $size, true, ['class' => 'hover-light', 'loading' => 'lazy']);
					}
				}
			}
			?></div>
<?php
	}
}

if (!function_exists('pillars_wc_get_price_html')) {
	/**
	 * Returns the price in html format for the loop.
	 *
	 * @param string $deprecated
	 * @param int $product
	 * @return string
	 */
	function pillars_wc_get_price_html($deprecated = '', $product_id = null)
	{
		if (isset($GLOBALS['product'])) {
			global $product;
		} elseif (absint($product_id)) {
			$product = wc_get_product($product_id, $deprecated);
		}

		if (!is_a($product, 'WC_Product'))
			return '';

		if ($product->get_type() != 'variable') {
			if ('' === $product->get_price()) {
				$price = apply_filters('woocommerce_empty_price_html', '', $product);
			} elseif ($product->is_on_sale()) {
				$price = wc_format_sale_price('', wc_get_price_to_display($product)) . $product->get_price_suffix();
			} else {
				$price = wc_price(wc_get_price_to_display($product)) . $product->get_price_suffix();
			}
		} else {
			$prices 		= $product->get_variation_prices(true);
			$min_price  	= current($prices['price']);
			$max_price 		= end($prices['price']);
			$min_reg_price 	= current($prices['regular_price']);
			$max_reg_price 	= end($prices['regular_price']);

			if ($min_price !== $max_price) {
				$price = 'от ' . wc_price($min_price) . $product->get_price_suffix();
			} elseif ($product->is_on_sale() && $min_reg_price === $max_reg_price) {
				$price = wc_format_sale_price(wc_price($max_reg_price), wc_price($min_price));
			} else {
				$price = wc_price($min_price) . $product->get_price_suffix();
			}

			/**
			 * Проверяем наличие скидок на товар при определённом количестве
			 */
			$discounts	= array(
				'count'	=> get_post_meta($product->get_id(), '_product_discounts_count', true),
				'per'	=> get_post_meta($product->get_id(), '_product_discounts_per', true)
			);

			if (!empty($discounts['count']) && !empty($discounts['per'])) {
				$price = preg_replace('#[^0-9]#', '', $min_price);
				$price = $price * (100 - $discounts['per']) / 100;
				$price = 'от ' . wc_price(ceil($price)) . $product->get_price_suffix();
			}
		}

		return apply_filters('woocommerce_get_price_html', $price, $product);
	}
}

function pillars_wc_after_shop_loop_item_col_close()
{
	if (pillars_wc_is_shop_loop_item()) {
		echo '</div>';
	}
}

/**
 * Undocumented function
 *
 * @param [type] $item
 * @param array $args
 * @return void|string
 */
function pillars_wc_display_item_meta($item, $args = array())
{
	$strings	= array();
	$html		= '';
	$args		= wp_parse_args(
		$args,
		array(
			'wrapper_before' => '<dl class="wc-item-meta">',
			'wrapper_after'	=> '</dl>',
			'before'		=> '<dd>',
			'after'			=> '</dd>',
			'echo'			=> true,
			'autop'			=> false,
			'label_before'	=> '<dt class="wc-item-meta-label %s">',
			'label_after'	=> '</dt> ',
		)
	);

	foreach ($item->get_all_formatted_meta_data() as $meta_id => $meta) {
		$value		= $args['autop'] ? wp_kses_post($meta->display_value) : wp_kses_post(make_clickable(trim($meta->display_value)));
		$strings[]	= sprintf(
			'%s%s%s' . PHP_EOL . '%s%s%s',
			sprintf($args['label_before'], $meta_id),
			wp_kses_post($meta->display_key),
			$args['label_after'],
			$args['before'],
			$value,
			$args['after'],
		);
	}

	if ($strings) {
		$html = $args['wrapper_before'] . implode($args['separator'], $strings) . $args['wrapper_after'];
	}

	$html = apply_filters('woocommerce_display_item_meta', $html, $item, $args);

	if ($args['echo']) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	} else {
		return $html;
	}
}
