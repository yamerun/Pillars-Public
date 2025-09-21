<?php

defined('ABSPATH') || exit;

/**
 * Copy Pages Between Sites in Multisite Network
 * @source https://rudrastyh.com/wordpress-multisite/copy-pages-between-sites.html
 *
 */
add_action('init', function () {
	$post = 'product';

	// Добавление кастомных массовых действий по мультисайту для таксономии/терминов
	add_filter("bulk_actions-edit-{$post}", 'theplugin_custom_bulk_multisite_post_actions');

	// Добавление обработчика события кастомных массовых действий по мультисайту
	add_filter("handle_bulk_actions-edit-{$post}", 'theplugin_custom_bulk_action_multisite_product_handler', 10, 3);
});

// show an appropriate notice
add_action('admin_notices', 'theplugin_custom_bulk_multisite_post_notices');

/**
 * Обработка постов на массовое действие по копированию/обновлению записей в мультисайте
 *
 * @param [type] $redirect
 * @param [type] $doaction
 * @param [type] $object_ids
 * @return array
 */
function theplugin_custom_bulk_action_multisite_product_handler($redirect, $doaction, $object_ids)
{
	// we need query args to display correct admin notices
	$redirect = remove_query_arg(array('theplugin_posts_moved', 'theplugin_blogid', 'theplugin_posts_results'), $redirect);

	// Проверяем наличие ярлыка на действие копирования записей
	if (strpos($doaction, 'copy_post_to_') === 0) {
		$blog_id = str_replace('copy_post_to_', '', $doaction); // get blog ID from action name
		$results = [
			'success'	=> [],
			'fail'		=> [],
			'meta'		=> []
		];

		foreach ($object_ids as $post_id) {
			// Обработка записи
			$success = theplugin_multisite_copy_product_to_site($post_id, $blog_id);
			$results[$success][] = $post_id;
		}

		$redirect = add_query_arg(array(
			'theplugin_posts_moved'		=> count($object_ids),
			'theplugin_posts_results'	=> $results,
			'theplugin_blogid'			=> $blog_id
		), $redirect);
	}

	return $redirect;
}

/**
 * Undocumented function
 *
 * @param [type] $post_id
 * @param [type] $blog_id
 * @return void
 */
function theplugin_multisite_copy_product_to_site($post_id, $blog_id)
{
	$success = theplugin_multisite_copy_post_to_site($post_id, $blog_id);

	get_post_meta($post_id, '_multisite_post_ids');
	theplugin_multisite_copy_product_variation_to_site($post_id, $blog_id);
	theplugin_multisite_copy_product_order_attributes($post_id, $blog_id);

	theplugin_multisite_copy_product_attributes_lookup($post_id, $blog_id);
	theplugin_multisite_copy_product_meta_lookup($post_id, $blog_id);

	theplugin_multisite_copy_product_meta_siblings($post_id, $blog_id, get_current_blog_id());

	return $success;
}

/**
 * Дублирование вариаций товаров
 *
 * @param [type] $post_id
 * @param [type] $blog_id
 * @param integer $current_blog_id
 * @return array
 */
function theplugin_multisite_copy_product_variation_to_site($post_id, $blog_id, $current_blog_id = 1)
{
	$variations = get_children([
		'post_parent'	=> $post_id,
		'post_type'		=> 'product_variation',
		'numberposts'	=> -1,
		'post_status'	=> 'any'
	], ARRAY_A);

	$data = [];

	if ($variations) {
		foreach ($variations as $id => $product) {
			$data[$id] = theplugin_multisite_copy_post_to_site($id, $blog_id);
		}
	}

	return $data;
}

/**
 * Обновление данных в таблице `wc_product_attributes_lookup` по переданному ID блога и товара
 *
 * @param [type] $product_id
 * @param [type] $blog_id
 * @return array
 */
function theplugin_multisite_copy_product_attributes_lookup($product_id, $blog_id)
{
	// wc_product_attributes_lookup

	global $wpdb;
	$table_name = 'wc_product_attributes_lookup';
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$table = $wpdb->prefix . $table_name;
	$data = [];
	$ids = [];

	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE product_id = %d", $product_id), ARRAY_A);
	if ($results) {
		foreach ($results as $i => $item) {
			$data[$i] = $item;
			foreach (['product_id', 'product_or_parent_id'] as $key) {
				if (!in_array($data[$i][$key], array_keys($ids))) {
					$multi = theplugin_multisite_post_get_sibling_id($data[$i][$key], $blog_id);
					if ($multi) {
						$ids[$data[$i][$key]] = $multi;
					}
				}
				$data[$i][$key] = strtr($data[$i][$key], $ids);
			}

			$data[$i]['term_id'] = theplugin_multisite_term_get_sibling_id($item['term_id'], $blog_id);
		}

		if ($data) {
			$table = $prefix . $table_name;
			$tax = [];
			foreach ($data as $i => $item) {
				$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE product_id = %d AND term_id = %d", $item['product_id'], $item['term_id']));

				if ($exists) {
					$tax[$i] = $wpdb->update($table, $item, [
						'product_id'	=> $item['product_id'],
						'term_id'		=> $item['term_id'],
					]);
				} else {
					$insert = $wpdb->insert($table, $item);
					$tax[$i] = ($insert) ? $wpdb->insert_id : false;
				}
			}

			return $tax;
		}
	}

	return $data;
}

/**
 * Undocumented function
 *
 * @param [type] $product_id
 * @param [type] $blog_id
 * @return array
 */
function theplugin_multisite_copy_product_meta_lookup($product_id, $blog_id)
{
	// wc_product_meta_lookup

	global $wpdb;
	$table_name = 'wc_product_meta_lookup';
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$table = $wpdb->prefix . $table_name;
	$data = [];

	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE product_id = %d", $product_id), ARRAY_A);
	if ($results) {
		foreach ($results as $i => $item) {
			$data[$i] = $item;
			$multi = theplugin_multisite_post_get_sibling_id($data[$i]['product_id'], $blog_id);
			if ($multi) {
				$data[$i]['product_id'] = $multi;
			}
		}

		if ($data) {
			$table = $prefix . $table_name;
			$tax = [];
			foreach ($data as $i => $item) {
				$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE product_id = %d", $item['product_id']));

				if ($exists) {
					$item['total_sales'] = $exists->total_sales;
					$tax[$i] = $wpdb->update($table, $item, [
						'product_id'	=> $item['product_id'],
					]);
				} else {
					$insert = $wpdb->insert($table, $item);
					$tax[$i] = ($insert) ? $wpdb->insert_id : false;
				}

				@$wpdb->update(
					$prefix . 'postmeta',
					['meta_value' => $item['stock_status']],
					['post_id' => $item['product_id'], 'meta_key' => '_stock_status']
				);
			}

			return $tax;
		}
	}

	return $data;
}

/**
 * Обновление порядка атрибутов товара по мультисайту
 *
 * @param [type] $product_id
 * @param [type] $blog_id
 * @return bool|int|null
 */
function theplugin_multisite_copy_product_order_attributes($product_id, $blog_id)
{
	$meta = [
		'_product_attributes' => get_post_meta($product_id, '_product_attributes', true),
		'_default_attributes' => get_post_meta($product_id, '_default_attributes', true)
	];

	$multi_ids = get_post_meta($product_id, '_multisite_post_ids', true);
	$result = null;

	if ($meta && $multi_ids) {
		if (isset($multi_ids['blog_' . $blog_id])) {

			$result = [];

			// Переключаем ID сайта на переданный блог
			switch_to_blog($blog_id);
			// запомним текущее состояние кеша для решения проблемы переполнения памяти
			$was_suspended = wp_suspend_cache_addition();
			// отключаем кэширование
			wp_suspend_cache_addition(true);

			foreach ($meta as $key => $value) {
				$result[] = update_post_meta($multi_ids['blog_' . $blog_id], $key, $value);
			}

			// вернем прежнее состояние кэша обратно
			wp_suspend_cache_addition($was_suspended);
			restore_current_blog();
		}
	}

	return $result;
}

/**
 * Обновление мета-данных по сиблингам Товара по мультисайту
 *
 * @param [type] $product_id
 * @param [type] $blog_id
 * @param integer $current_blog_id
 * @return int|bool|null
 */
function theplugin_multisite_copy_product_meta_siblings($product_id, $blog_id, $current_blog_id = 1)
{
	$update = null;
	$siblings = get_post_meta($product_id, '_product_siblings', true);
	if ($siblings) {
		$ids = [];
		foreach ($siblings['values'] as $post_id => $label) {
			$_id = theplugin_multisite_post_get_sibling_id($post_id, $blog_id, $current_blog_id);
			if ($_id != $post_id) {
				$ids[$_id] = $label;
			}
		}

		$siblings['values'] = $ids;
		$_product_id = theplugin_multisite_post_get_sibling_id($product_id, $blog_id, $current_blog_id);

		// Переключаем ID сайта на переданный блог
		switch_to_blog($blog_id);
		// запомним текущее состояние кеша для решения проблемы переполнения памяти
		$was_suspended = wp_suspend_cache_addition();
		// отключаем кэширование
		wp_suspend_cache_addition(true);

		$update = update_post_meta($_product_id, '_product_siblings', $siblings);
		if ($update !== false) {
			$update = $siblings;
		}

		// вернем прежнее состояние кэша обратно
		wp_suspend_cache_addition($was_suspended);
		restore_current_blog();
	}

	return $update;
}
