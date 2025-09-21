<?php

defined('ABSPATH') || exit;

// add_action('customize_register', 'pillars_customize_register');
add_filter('get_custom_logo', 'pillars_get_custom_logo_filter', 10, 2);

/**
 * Настройка Customize для theme_mods_pillars
 *
 * @param [type] $wp_customize
 * @return void
 */
function pillars_customize_register($wp_customize)
{
	$setting 		= array(
		'default' 		=> '',
		'transport' 	=> 'postMessage'
	);
	$control 			= array(
		'label'			=> 'Label:',
		'section'		=> '',
		'settings'		=> '',
		'type'			=> 'text'
	);

	// PRICE & CATALOGUE
	$section_name 		= 'price_section';
	$control['section']	= $section_name;
	$wp_customize->add_section($section_name, array(
		'title'     => __('Каталоги и прайсы', 'ledmebel'),
		'priority'  => 35,
	));
	// Catalog
	$setting_key = 'file-upload-catalog';
	$wp_customize->add_setting($setting_key,	wp_parse_args(array(), $setting));
	$wp_customize->add_control($setting_key,	wp_parse_args(array('label' => __('Файл каталога:', 'ledmebel'), 'settings' => $setting_key), $control));
	// Price
	$setting_key = 'file-upload-price';
	$wp_customize->add_setting($setting_key,	wp_parse_args(array(), $setting));
	$wp_customize->add_control($setting_key,	wp_parse_args(array('label' => __('Файл прайса:', 'ledmebel'), 'settings' => $setting_key), $control));

	$setting_key = 'file-upload-rent';
	$wp_customize->add_setting($setting_key);
	$wp_customize->add_control(
		new WP_Customize_Upload_Control(
			$wp_customize,
			$setting_key,
			array('label' => __('Загрузка сдачи в аренду:', 'ledmebel'), 'section' => $section_name, 'settings' => $setting_key)
		)
	);

	// MEDIA
	$section_name 		= 'media_section';
	$control['section']	= $section_name;
	$wp_customize->add_section($section_name, array(
		'title'		=> __('Медиа', 'ledmebel'),
		'priority'	=> 40,
	));
	// Gallery
	$setting_key = 'gallery_page';
	$wp_customize->add_setting($setting_key,	wp_parse_args(array('default' => 'Укажите ID страницы'), $setting));
	$wp_customize->add_control($setting_key,	wp_parse_args(array('label' => __('Страница галереи:', 'ledmebel'), 'type' => 'dropdown-pages', 'settings' => $setting_key), $control));
	// Background Repost meta
	$setting_key = 'file-background-repost';
	$wp_customize->add_setting($setting_key, wp_parse_args(array('default' => 0), $setting));
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			$setting_key,
			array('label' => 'Загрузка фона для репостов:', 'section' => $section_name, 'settings' => $setting_key)
		)
	);
}

/**
 * Добавляем в логотип представление в svg-коде для оптимизированной загрузки
 *
 * @param string $html    Custom logo HTML output.
 * @param int    $blog_id ID of the blog to get the custom logo for.
 *
 * @return string
 */
function pillars_get_custom_logo_filter($html, $blog_id)
{
	if (mb_strpos($html, '/></') !== false) {
		$logo = pillars_theme_get_svg('logo', '', null);
		if ($logo) {
			$html = str_replace('/></', '/>' . pillars_theme_get_svg_symbol('logo') . '</', $html);
			$html = str_replace('class="custom-logo"', 'class="custom-logo visuallyhidden"', $html);
		}
	}

	return $html;
}
