<?php

defined('ABSPATH') || exit;

add_action('after_setup_theme',			'pillars_setup_theme');
add_filter('image_size_names_choose',	'pillars_image_size_names_choose');
add_filter('intermediate_image_sizes',	'pillars_delete_intermediate_image_sizes');

/**
 * Установка базовых настроет темы при запуске
 *
 * @return void
 */
function pillars_setup_theme()
{
	load_theme_textdomain('pillars');

	add_theme_support('title-tag');
	add_theme_support('custom-logo', array(
		'width'			=> 200,
		'height'		=> 60,
		'flex-height'	=> true, // если гибкая высота.
		'flex-width'	=> true, // если гибкая ширина.
		'header-text'	=> array('site-title', 'site-description'),
		'unlink-homepage-logo' => true,
	));
	add_theme_support('post-thumbnails');
	add_post_type_support('page', array('excerpt'));

	register_nav_menus(array(
		'primary' 			=> __pl('Primary Menu'),
		'primary_mobile' 	=> __pl('Primary Menu Mobile'),
		'category_menu' 	=> __pl('Category Menu'),
		'footer_menu' 		=> __pl('Footer Menu'),
		'footer_category' 	=> __pl('Footer Category'),
		'cooperation_menu' 	=> __pl('Cooperation Menu'),
	));

	add_theme_support('html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	));

	if (function_exists('register_sidebar')) {
		register_sidebar(['name' => 'Pillars Sidebar', 'id' => 'pillars_sidebar']);
	}

	// файл стилей для редактора блоков
	add_theme_support('editor-styles');						// включает поддержку
	add_editor_style('assets/css/editor-style.min.css');	// добавляет файл стилей editor-style.css
}

/**
 * Наименование размеров в выпадающем списке редакторе контента
 *
 * @param [type] $sizes
 * @return array
 */
function pillars_image_size_names_choose($sizes)
{
	$addsizes = array();
	$newsizes = array_merge($sizes, $addsizes);
	return $newsizes;
}

/**
 * отключаем создание миниатюр файлов для указанных размеров
 *
 * @param [type] $sizes
 * @return array
 */
function pillars_delete_intermediate_image_sizes($sizes)
{
	// размеры которые нужно удалить
	return array_diff($sizes, [
		// '1536x1536',
		'2048x2048',
	]);
}

require_once get_template_directory() . '/includes/pillars.theme.php';		// Styles, scripts & customize
require_once get_template_directory() . '/includes/pillars.functions.php';
require_once get_template_directory() . '/includes/pillars.svg.php';

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
	require_once get_template_directory() . '/includes/woocommerce.php';
}

require_once get_template_directory() . '/includes/pillars.shortcodes.php';
