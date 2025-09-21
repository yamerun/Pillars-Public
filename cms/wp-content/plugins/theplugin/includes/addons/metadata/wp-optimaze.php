<?php

defined('ABSPATH') || exit;

/**
 * OPTIMAZE
 */
remove_action('wp_head', 'wp_generator');

add_action('init', 'disable_emojis');
add_action('wp_default_scripts', 'theplugin_remove_jquery_migrate');
add_action('wp_enqueue_scripts', 'theplugin_disable_woocommerce_block_styles');
add_action('enqueue_block_editor_assets', 'theplugin_disable_woocommerce_block_editor_styles', 1, 1);
add_action('init', 'theplugin_remove_wc_json_ld_frontend');

add_filter('script_loader_tag', 'thepugin_add_async_attribute', 10, 2);
add_filter('wpseo_schema_graph', 'theplugin_wpseo_schema_graph_filter', 10, 2);

/**
 * Добавление js-атрибута для запуски js-скриптов после загрузки DOM дерева
 *
 * @param [type] $tag
 * @param [type] $handle
 * @return string
 */
function thepugin_add_async_attribute($tag, $handle)
{
	if (!is_admin()) {
		if ('jquery-core' == $handle || 'jquery' == $handle) {
			return $tag;
		}
		return str_replace(' src', ' defer src', $tag);
	} else {
		return $tag;
	}
}

/**
 * Отключим подключение jquery-migrate при подключении jquery
 *
 * основа от wpschool_remove_jquery_migrate
 *
 * @param [type] $scripts
 * @return void
 */
function theplugin_remove_jquery_migrate($scripts)
{
	if (!is_admin() && isset($scripts->registered['jquery'])) {
		$script = $scripts->registered['jquery'];
		if ($script->deps) {
			$script->deps = array_diff($script->deps, array('jquery-migrate'));
		}
	}
}

/**
 * Исключение файла wc-block-style.css из загрузки страницы
 *
 * @return void
 */
function theplugin_disable_woocommerce_block_styles()
{
	wp_dequeue_style('wc-block-style');
}

/**
 * Отключим стили на странице редактирования записи редактора Gutenberg
 *
 * @return void
 */
function theplugin_disable_woocommerce_block_editor_styles()
{
	wp_deregister_style('wc-block-editor');
	wp_deregister_style('wc-block-style');
}

/**
 * Disable the emoji's
 */


/**
 * Функция фильтра, используемая для удаления плагина tinymce emoji.
 *
 * @param    array  $plugins
 * @return   array  Difference betwen the two arrays
 */
function theplugin_disable_emojis_tinymce($plugins)
{
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	}
	return array();
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @source https://wp-kama.ru/question/kak-otplyuchit-emoji-v-wordpress
 *
 * @param  array  $urls          URLs to print for resource hints.
 * @param  string $relation_type The relation type the URLs are printed for.
 * @return array                 Difference betwen the two arrays.
 */
function theplugin_disable_emojis_remove_dns_prefetch($urls, $relation_type)
{

	if ('dns-prefetch' == $relation_type) {

		// Strip out any URLs referencing the WordPress.org emoji location
		$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
		foreach ($urls as $key => $url) {
			if (strpos($url, $emoji_svg_url_bit) !== false) {
				unset($urls[$key]);
			}
		}
	}

	return $urls;
}


/**
 * Отключение всех файлов стилей и скриптов emoji для ускорения загрузки страницы
 *
 * @return void
 */
function disable_emojis()
{
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	// remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	// remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	// remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	// add_filter('tiny_mce_plugins', 'theplugin_emojis_tinymce');
	// add_filter('wp_resource_hints', 'theplugin_disable_emojis_remove_dns_prefetch', 10, 2);
	// TODO: проверить какие вещи можно отключить
}

/**
 * Удаляет "Рубрика: ", "Метка: " и т.д. из заголовка архива
 */
add_filter('get_the_archive_title', function ($title) {
	return preg_replace('~^[^:]+: ~', '', $title);
});



/**
 * Function for `wpseo_schema_graph` filter-hook.
 *
 * @param array             $graph   The graph to filter.
 * @param Meta_Tags_Context $context A value object with context variables.
 *
 * @return array
 */
function theplugin_wpseo_schema_graph_filter($graph, $context)
{
	foreach ($graph as $i => $items) {
		if (isset($items['image']) && isset($items['thumbnailUrl'])) {
			$graph[$i]['image'] = $items['thumbnailUrl'];
		}
	}

	return $graph;
}

/**
 * Скрытие JSON/LD от WooCommerce во фронтенде на всех страницах, кроме продуктов
 *
 * @source https://removewcfeatures.com/remove-woocommerce-json-ld/
 * @return void
 */
function theplugin_remove_wc_json_ld_frontend()
{
	if (function_exists('is_product') && !is_product()) {
		remove_action('wp_footer', array(WC()->structured_data, 'output_structured_data'), 10);
	}
}
