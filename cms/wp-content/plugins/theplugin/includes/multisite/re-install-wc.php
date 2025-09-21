<?php

defined('ABSPATH') || exit;

add_action('wp', 'theplugin_multisite_reinstall_wc_options_handler', 99);
add_action('wp', 'theplugin_multisite_reinstall_wc_attribute_taxonomies_handler', 99);
add_action('wp', 'theplugin_multisite_reinstall_wc_product_tax_handler', 99);

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_reinstall_wc_options_handler()
{
	if (isset($_GET['wc-options'])) {

		$blog_id = absint($_GET['wc-options']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			$results = theplugin_multisite_reinstall_wc_options($blog_id);

			global $wpdb;

			$results = [];
			$tables = [
				'woocommerce_shipping_zones',
				'woocommerce_shipping_zone_locations',
				'woocommerce_shipping_zone_methods'
			];
			$prefix = theplugin_multisite_get_blog_prefix($blog_id);

			foreach ($tables as $table) {
				$results[$table] = theplugin_multisite_reinstall_db_table($wpdb->prefix . $table, $prefix . $table);
			}
		}
	}
}

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @return array
 */
function theplugin_multisite_reinstall_wc_options($blog_id = 0)
{
	$data = [];

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$options = theplugin_multisite_reinstall_wp_wc_options($table);

	if ($options) {
		$args = [
			'base'		=> $table,
			'site'		=> theplugin_multisite_get_blog_prefix($blog_id) . 'options',
			'keys'		=> $options,
			'exclude'	=> []
		];
		$data = theplugin_multisite_reinstall_wp_options($args);
	}

	return $data;
}

/**
 * Получение списка настроек WooCommerce в переданной таблицы опций
 *
 * @param string $table
 * @return array
 */
function theplugin_multisite_reinstall_wp_wc_options($table)
{
	global $wpdb;

	$esc_like = '%' . $wpdb->esc_like('woocommerce') . '%';
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE option_name LIKE %s ORDER BY option_id ASC", $esc_like));

	$data		= [];
	$exclude	= [
		'woocommerce_shop_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_terms_page_id',
		'_transient_timeout_woocommerce_blocks_asset_api_script_data_ssl',
		'_transient_woocommerce_blocks_asset_api_script_data_ssl',
		'woocommerce_store_id',
		'_transient_timeout_woocommerce_admin_remote_inbox_notifications_specs',
		'_transient_woocommerce_admin_remote_inbox_notifications_specs',
		'woocommerce_share_key'
	];

	if ($results) {
		foreach ($results as $item) {
			if (!in_array($item->option_name, $exclude)) {
				$data[$item->option_name] = [
					'option_value'	=> $item->option_value,
					'autoload'		=> $item->autoload,
				];
			}
		}
	}

	return $data;
}

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_reinstall_wc_attribute_taxonomies_handler()
{
	if (isset($_GET['wc-tax'])) {

		$blog_id = absint($_GET['wc-tax']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			$results = theplugin_multisite_reinstall_wc_attribute_taxonomies($blog_id);
		}
	}
}

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @return array|WP_Error
 */
function theplugin_multisite_reinstall_wc_attribute_taxonomies($blog_id = 0)
{

	$blog_id = absint($blog_id);
	$current_blog_id = get_current_blog_id();
	$data = [];

	if ($blog_id == $current_blog_id && $blog_id)
		return new WP_Error('missing_blog_id', 'Не указан ID блога для переноса или совпадает с текущим.', array('status' => 400));;

	global $wpdb;

	$table = sprintf(
		'%s%swoocommerce_attribute_taxonomies',
		$wpdb->prefix,
		($current_blog_id != 1) ? $current_blog_id . '_' : ''
	);
	$current_attrs = $wpdb->get_results("SELECT * FROM $table ORDER BY attribute_id");

	if ($current_attrs) {
		foreach ($current_attrs as $item) {
			$data[$item->attribute_name] = theplugin_multisite_reinstall_wc_create_attribute($blog_id, $item->attribute_id);
		}
	}

	return $data;
}

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_reinstall_wc_product_tax_handler()
{
	if (isset($_GET['wc-product-tax'])) {

		$blog_id = absint($_GET['wc-product-tax']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			$results = theplugin_multisite_reinstall_wc_product_tax($blog_id);
		}
	}
}

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @return void
 */
function theplugin_multisite_reinstall_wc_product_tax($blog_id = 0)
{
	$current_blog_id = get_current_blog_id();
	$blog_id = absint($blog_id);
	$logs = [
		$current_blog_id => [],
		$blog_id => []
	];

	$current_ids = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
	$site_ids = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

	foreach ($current_ids as $i => $id) {
		$multisite_term_ids = get_term_meta($id, '_multisite_term_ids', true);
		if (!$multisite_term_ids) {
			$multisite_term_ids = [];
		}
		// Задаём перекрестное значение ID постов-близнецов для обновляемой записи
		$multisite_term_ids['blog_' . $current_blog_id] = $id;
		$multisite_term_ids['blog_' . $blog_id] = $site_ids[$i];

		$logs[] = [
			'blog'		=> $current_blog_id,
			'meta'		=> $multisite_term_ids,
			'update'	=> update_term_meta($id, '_multisite_term_ids', $multisite_term_ids)
		];

		// Переключаем ID сайта на переданный блог
		switch_to_blog($blog_id);
		// запомним текущее состояние кеша для решения проблемы переполнения памяти
		$was_suspended = wp_suspend_cache_addition();
		// отключаем кэширование
		wp_suspend_cache_addition(true);

		$logs[] = [
			'blog'		=> get_current_blog_id(),
			'meta'		=> $multisite_term_ids,
			'update'	=> update_term_meta($site_ids[$i], '_multisite_term_ids', $multisite_term_ids)
		];

		// отключаем кэширование
		wp_suspend_cache_addition(true);
		// вернем прежнее состояние кэша обратно
		wp_suspend_cache_addition($was_suspended);
		restore_current_blog();
	}

	return $logs;
}

/**
 * Обработка массива исключений категорий Магазина для сохранения в опции другого блога
 *
 * @param int $blog_id
 * @param string $name
 * @return array
 */
function theplugin_multisite_wc_catalog_option_ids($blog_id, $name = 'wc_catalog_exclude_category')
{
	$excludes	= get_option($name);
	$ids		= [];

	foreach ($excludes as $id) {
		$metas = get_term_meta($id, '_multisite_term_ids', true);

		if (isset($metas['blog_' . $blog_id])) {
			$ids[] = absint($metas['blog_' . $blog_id]);
		}
	}

	return serialize($ids);
}
