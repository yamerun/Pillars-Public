<?php

defined('ABSPATH') || exit;

add_action('admin_menu', 'theplugin_dashboard_add_menu_page', 25);
add_filter('site_transient_update_plugins', 'theplugin_filter_plugin_updates');

/**
 * Функция добавления страницы настройки плагина в Консоль -> Настройки
 *
 * @source https://misha.agency/wordpress/option-pages.html
 * @return void
 */
function theplugin_dashboard_add_menu_page()
{

	add_submenu_page(
		'options-general.php',
		'Настройки The Plugin', 			// тайтл страницы
		'The Plugin',						// текст ссылки в меню
		'manage_options',					// права пользователя, необходимые для доступа к странице
		'theplugin_settings',				// ярлык страницы
		'theplugin_dashboard_page_callback'	// функция, которая выводит содержимое страницы
	);
}

function theplugin_dashboard_page_callback()
{
	echo '<div class="wrap">
	<h1>' . get_admin_page_title() . '</h1>
	<form method="post" action="options.php">';

	settings_fields('theplugin_manage_settings'); // название настроек
	do_settings_sections('theplugin_settings'); // ярлык страницы, не более
	submit_button(); // функция для вывода кнопки сохранения

	echo '</form></div>';
}

/**
 * Отключение автообновления у указанных плагинов
 *
 * @param [type] $value
 * @return array
 */
function theplugin_filter_plugin_updates($value)
{
	unset($value->response['advanced-custom-fields-pro-master/acf.php']);
	return $value;
}
