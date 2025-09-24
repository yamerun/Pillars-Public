<?php

defined('ABSPATH') || exit;

add_filter('cron_schedules', 'theplugin_cron_add_interval');

/**
 * Добавляем в выборку интервалов CRON периоды
 *
 * @param array $schedules
 * @return array
 */
function theplugin_cron_add_interval($schedules)
{
	$schedules['threehours'] = array(
		'interval' 	=> 3 * HOUR_IN_SECONDS, // 10800 сек.
		'display' 	=> __('Every three hours')
	);

	$schedules['twicedaily'] = array(
		'interval' 	=> 12 * HOUR_IN_SECONDS, // 43200 сек.
		'display' 	=> __('Twice Daily')
	);

	$schedules['min'] = array(
		'interval' => 60,
		'display' => 'Раз в минуту'
	);

	$schedules['five_min'] = array(
		'interval' => 60 * 5,
		'display' => 'Раз в 5 минут'
	);

	$schedules['ten_min'] = array(
		'interval' => 60 * 10,
		'display' => 'Раз в 10 минут'
	);

	$schedules['half_hour'] = array(
		'interval' => 60 * 30,
		'display' => 'Раз в полчаса'
	);

	$schedules['oncequarter'] = array(
		'interval' => 122 * DAY_IN_SECONDS,
		'display' => 'Раз в квартал'
	);

	return $schedules;
}

require THEPLUGIN_DIR . '/cron/update-date-published.php';
require THEPLUGIN_DIR . '/cron/update-date-modified.php';
require THEPLUGIN_DIR . '/cron/update-indexing-wc-product.php';
