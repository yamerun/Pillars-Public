<?php

defined('ABSPATH') || exit;

add_action('admin_init',	'theplugin_dashboard_seo_cities_fields');

/**
 * Список параметров для склонения города в SEO-описании
 *
 * @return void
 */
function theplugin_dashboard_seo_cities_fields()
{
	// Контакты
	add_settings_section(
		'tp_section_seo_cities_id',	// ID секции, пригодится ниже
		'Склонение города для SEO',	// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	$args = [
		'city1'	=> 'Именительный:',
		'city2'	=> 'Родительный:',
		'city3'	=> 'Датильный:',
		'city4'	=> 'Винительный:',
		'city5'	=> 'Творительный:',
		'city6'	=> 'Предложный:',
	];

	foreach ($args as $key => $label) {
		theplugin_dashboard_fields_customize(array(
			'field_id'			=> $key,
			'field_label'		=> $label,
			'section_id'		=> 'tp_section_seo_cities_id',
			'args'				=> array('description' => 'Ключ: {{' . $key . '}}')
		));
	}
}
