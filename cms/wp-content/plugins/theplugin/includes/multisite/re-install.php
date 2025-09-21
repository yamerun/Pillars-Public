<?php

defined('ABSPATH') || exit;

/**
 * Перенос данных из одной таблицы в другую по мультисайту
 *
 * @param string $base таблица переносимых данных
 * @param string $site таблица получаемых данных
 * @param string $name ключ поля опции
 * @return array
 */
function theplugin_multisite_reinstall_db_table($base = '', $site = '', $name = '')
{
	global $wpdb;

	$data = [];

	$results = $wpdb->get_results("SELECT * FROM $base", 'ARRAY_A');
	if ($results) {
		foreach ($results as $item) {
			if ($name) {
				$data[$item[$name]] = $item;
			} else {
				$data[] = $item;
			}
		}
	}

	$insert = [];

	if ($data) {
		foreach ($data as $key => $item) {

			$new = true;

			if ($name) {
				$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $site WHERE `$name`= %s", $key));

				if ($exists) {
					unset($item[$name]);
					$insert[$key] = $wpdb->update(
						$site,
						$item,
						[$name => $key]
					);
					$new = false;
				}
			}

			if ($new) {
				$insert[$key] = $wpdb->insert(
					$site,
					$item,
				);
			}
		}
	}

	return $insert;
}

/**
 * Обновление настроек `options` сайта поддомена по переданным данным
 *
 * @param array $args
 * @return null|array
 */
function theplugin_multisite_reinstall_wp_options($args = [])
{
	global $wpdb;

	$defaults = array(
		'base'		=> $wpdb->prefix . 'options',
		'site'		=> '',
		'keys'		=> [],
		'exclude'	=> []
	);

	$args = wp_parse_args($args, $defaults);

	if (!$args['site'] || count($args['keys']) < 1)
		return null;

	$data = [];

	foreach ($args['keys'] as $key => $value) {

		if (!$value)
			$value = theplugin_multisite_get_option($args['base'], $key);

		if (is_array($value)) {
			$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$args['site']} WHERE option_name = %s ORDER BY option_id DESC", $key));

			if ($exists) {
				$data[$key] = $wpdb->update(
					$args['site'],
					$value,
					['option_name' => $key]
				);
			} else {
				$data[$key] = $wpdb->insert(
					$args['site'],
					array_merge(['option_name' => $key], $value),
				);
			}
		}
	}

	return $data;
}

/**
 * Получение значения опции сайта с параметром автозагрузки
 *
 * @param string $table
 * @param string $key
 * @return null|string
 */
function theplugin_multisite_get_option($table = '', $key = '')
{

	if (!$table || !$key)
		return null;

	global $wpdb;

	$query = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE option_name = %s ORDER BY option_id DESC", $key));

	if ($query) {
		return [
			'option_value'	=> $query->option_value,
			'autoload'		=> $query->autoload,
		];
	}

	return false;
}
