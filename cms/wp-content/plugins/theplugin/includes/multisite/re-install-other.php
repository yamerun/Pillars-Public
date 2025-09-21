<?php

defined('ABSPATH') || exit;

add_action('wp', 'theplugin_multisite_reinstall_other_options_handler', 99);

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_reinstall_other_options_handler()
{
	if (isset($_GET['other-options'])) {

		$blog_id = absint($_GET['other-options']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			global $wpdb;

			$options = [
				'yametrika'	=> '%' . $wpdb->esc_like('yametrika') . '%',
				'gsitemap'	=>  $wpdb->esc_like('sm_') . '%',
				'taxorder'	=> '%' . $wpdb->esc_like('customtaxorder') . '%',
				'cat_group'	=> 'product_category_group',
				'tp_theme'	=> $wpdb->esc_like('tp_') . '%',
			];

			foreach ($options as $name => $option) {
				$results = theplugin_multisite_reinstall_other_options($blog_id, $option);
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
function theplugin_multisite_reinstall_other_options($blog_id = 0, $esc_like = '')
{
	if (!$esc_like)
		return false;

	$data = [];

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$options = theplugin_multisite_reinstall_wp_other_options($table, $esc_like);

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
function theplugin_multisite_reinstall_wp_other_options($table, $esc_like)
{
	global $wpdb;

	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE option_name LIKE %s ORDER BY option_id ASC", $esc_like));

	$data		= [];
	$exclude	= [];

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
