<?php

defined('ABSPATH') || exit;

/**
 * NEW TYPES
 */
add_action('init', 'pillars_register_post_type_portfolio'); // Использовать функцию только внутри хука init
add_filter('post_updated_messages', 'pillars_post_type_portfolio_messages');

/**
 * Регистариция произвольного типа записи 'portfolio'
 *
 * @return void
 */
function pillars_register_post_type_portfolio()
{

	// Services
	$labels = array(
		'name' 					=> 'Портфолио',
		'singular_name' 		=> 'Портфолио',
		'add_new' 				=> __('Добавить Портфолио'),
		'add_new_item' 			=> __('Добавить новый Портфолио'),
		'edit_item' 			=> __('Редактировать Портфолио'),
		'new_item' 				=> __('Новая Портфолио'),
		'all_items' 			=> __('Весь Портфолио'),
		'view_item' 			=> __('Просмотр всего Портфолио'),
		'search_items' 			=> __('Поиск Портфолиоа'),
		'not_found' 			=> __('Портфолио не найден.'),
		'not_found_in_trash'	=> __('В корзине нет Портфолио.'),
		'menu_name' 			=> 'Портфолио'
	);

	$capability_type = 'portfolio';
	$args = array(
		'labels' 			=> $labels,
		'public' 			=> true,
		'publicly_queryable' => true,
		'show_ui' 			=> true,
		'show_in_rest'		=> true, // Gutenberg
		'has_archive' 		=> true,
		'menu_icon'   		=> 'dashicons-index-card',
		'menu_position' 	=> 22,
		'capability_type'	=> 'post',
		'query_var'			=> true,
		'rewrite' 			=> true,
		'map_meta_cap'		=> true,
		'hierarchical'		=> true,
		'supports' 			=> array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes')
	);
	register_post_type($capability_type, $args);
}

/**
 * Добавление списка уведомлений для типа постов 'portfolio'
 *
 * @param [type] $messages
 * @return array
 */
function pillars_post_type_portfolio_messages($messages)
{
	global $post, $post_ID;

	$messages['portfolio'] = array(
		0 => '',
		1 => sprintf(__('Портфолио обновлен.') . ' <a href="%s">' . __('Просмотр') . '</a>', esc_url(get_permalink($post_ID))),
		2 => __('Параметр обновлён.'),
		3 => __('Параметр удалён.'),
		4 => __('Портфолио обновлен'),
		5 => isset($_GET['revision']) ? sprintf(__('Портфолио восстановлен из редакции:') . ' %s', wp_post_revision_title((int) $_GET['revision'], false)) : false,
		6 => sprintf(__('Портфолио опубликован на сайте.') . ' <a href="%s">' . __('Просмотр') . '</a>', esc_url(get_permalink($post_ID))),
		7 => __('Портфолио сохранен.'),
		8 => sprintf(__('Отправлено на проверку.') . ' <a target="_blank" href="%s">' . __('Просмотр') . '</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		9 => sprintf(
			__('Запланировано на публикацию:') . ' <strong>%1$s</strong>. <a target="_blank" href="%2$s">' . __('Просмотр') . '</a>',
			date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
			esc_url(get_permalink($post_ID))
		),
		10 => sprintf(
			__('Черновик обновлён.') . ' <a target="_blank" href="%s">' . __('Просмотр') . '</a>',
			esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
		),
	);

	return $messages;
}

/**
 * Обновляем данные last-modified Главной, когда Портфолио обновляется
 */
add_action('save_post', function ($post_id) {

	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id))
		return;

	if (get_post_type($post_id) == 'portfolio') {
		theplugin_update_postdate(get_option('page_on_front'));
	}
});
