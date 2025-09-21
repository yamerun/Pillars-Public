<?php

defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'theplugin_multi_product_price_meta_box');

/**
 * Мета-бокс для ввыода данных по поддоменам
 *
 * @return void
 */
function theplugin_multi_product_price_meta_box()
{
	add_meta_box('multi_product_prices', 'Синхронизация цен', 'theplugin_multi_product_price_meta_add', ['product'], 'side');
}

/**
 * Undocumented function
 *
 * @param [type] $post
 * @return void
 */
function theplugin_multi_product_price_meta_add($post)
{
	$multi	= get_post_meta(get_the_ID(), '_multisite_post_ids', true);
	$prices	= get_post_meta(get_the_ID(), '_multisite_product_prices', true);
	if (!$multi) {
		$values = '–';
	} else {

		echo theplugin_get_components_panel([
			'post_id' 		=> $post->ID,
			'post_meta' 	=> '_multisite_product_prices',
			'input_id' 		=> 'pillars_multisite_product_prices_field',
			'input_type'	=> 'hidden',
			'input_default' => 'yes',
			'input_value'	=> 'yes',
		]);

		foreach ($multi as $blog => $id) {
			$blog_id = absint(str_replace('blog_', '', $blog));
			if ($blog_id !== get_current_blog_id()) {
				echo theplugin_get_components_panel([
					'post_id' 		=> $post->ID,
					'post_meta' 	=> '_multisite_product_price_' . $blog,
					'input_id' 		=> 'pillars_multisite_product_price_field[' . $blog . ']',
					'input_type'	=> 'hidden',
					'input_default' => 'no',
					'input_value'	=> 'no',
				]);

				echo theplugin_get_components_panel([
					'post_id' 		=> $post->ID,
					'post_meta' 	=> '_multisite_product_price_' . $blog,
					'input_id' 		=> 'pillars_multisite_product_price_field[' . $blog . ']',
					'input_type'	=> 'checkbox',
					'input_default' => 'yes',
					'input_value'	=> (isset($prices[$blog])) ? $prices[$blog] : null,
					'label' 		=> theplugin_multisite_get_site_option($blog_id, 'blogname')
				]);
			}
		}
	}
}

/**
 * Обновляем данные last-modified Главной, когда информер обновляется
 */
add_action('save_post', function ($post_id) {

	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	if (!isset($_POST['theplugin_post_meta_noncename']))
		return;
	if (!wp_verify_nonce($_POST['theplugin_post_meta_noncename'], 'theplugin_post_meta_action'))
		return;

	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id))
		return;

	if (get_post_type($post_id) == 'product' && isset($_POST['pillars_multisite_product_price_field'])) {

		$prices	= theplugin_multi_product_price_get_data($post_id, get_current_blog_id());
		$data	= [];

		foreach ($_POST['pillars_multisite_product_price_field'] as $blog => $flag) {
			$blog_id = absint(str_replace('blog_', '', $blog));
			$data[$blog] = sanitize_text_field($flag);
			if ($flag == 'yes') {

				$updatedate = [];

				foreach ($prices as $meta_key => $product_ids) {
					foreach ($product_ids as $product_id => $price) {
						$multi_ids = get_post_meta($product_id, '_multisite_post_ids', true);
						if ($multi_ids && isset($multi_ids[$blog])) {
							$update = theplugin_multisite_update_post_meta($blog_id, $multi_ids[$blog], $meta_key, $price);
							if ($update === false || is_wp_error($update)) {
								// log
							} else {
								$updatedate[] = $multi_ids[$blog];
							}
						}
					}
				}

				// TODO добавить обновление в `wc_product_meta_lookup`

				$updatedate = array_unique($updatedate);
				if ($updatedate) {
					foreach ($updatedate as $_id) {
						$blog_id = absint(str_replace('blog_', '', $blog));
						$postdate = theplugin_multisite_update_postdate($_id, $blog_id);
						if ($postdate === false || is_null($postdate)) {
							// log
						}
					}
				}
			}
		}
		update_post_meta($post_id, '_multisite_product_prices', $data);
	}
}, 99);


/**
 * Получение списка цен переданного товара по поддомену
 *
 * @param integer $product_id
 * @param integer $blog_id
 * @return array
 */
function theplugin_multi_product_price_get_data($product_id = 0, $blog_id = 1)
{
	global $wpdb;
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$tablename = 'posts';
	$table = $prefix . $tablename;

	$_products = [];
	$meta_keys = ['_price', '_regular_price', '_sku'];
	foreach ($meta_keys as $meta_key) {
		$value = theplugin_multisite_post_get_meta($blog_id, $product_id, $meta_key);
		if (!is_wp_error($value)) {
			$_products[$meta_key][$product_id] = $value;
		}
	}

	$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE post_parent = %d AND post_type = %s", $product_id, 'product_variation'));
	if ($result) {
		foreach ($result as $item) {
			foreach ($meta_keys as $meta_key) {
				$value = theplugin_multisite_post_get_meta($blog_id, $item->ID, $meta_key);
				if (!is_wp_error($value)) {
					$_products[$meta_key][$item->ID] = $value;
				}
			}
		}
	}

	return $_products;
}
