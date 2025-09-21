<?php

defined('ABSPATH') || exit;

/**
 * NEW TYPES
 */
add_action('init', 'theplugin_register_post_type_informer'); // Использовать функцию только внутри хука init
add_filter('post_updated_messages', 'theplugin_post_type_informer_messages');
// add_action('after_switch_theme', 'activate_pillars_theme');

/**
 * Регистариция произвольного типа записи 'informer'
 *
 * @return void
 */
function theplugin_register_post_type_informer()
{

	// Services
	$labels = array(
		'name' 					=> 'Информер',
		'singular_name' 		=> 'Информер',
		'add_new' 				=> __('Добавить Информер'),
		'add_new_item' 			=> __('Добавить новый Информер'),
		'edit_item' 			=> __('Редактировать Информер'),
		'new_item' 				=> __('Новая Информер'),
		'all_items' 			=> __('Весь Информер'),
		'view_item' 			=> __('Просмотр всего Информера'),
		'search_items' 			=> __('Поиск Информера'),
		'not_found' 			=> __('Информер не найден.'),
		'not_found_in_trash'	=> __('В корзине нет Информера.'),
		'menu_name' 			=> 'Информер'
	);

	$capability_type = 'informer';
	$args = array(
		'labels' 			=> $labels,
		'public' 			=> true,
		'publicly_queryable' => true,
		'show_ui' 			=> true,
		// 'show_in_rest'		=> true, // Gutenberg
		'has_archive' 		=> true,
		'menu_icon'   		=> 'dashicons-welcome-view-site',
		'menu_position' 	=> 21,
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
 * Добавление списка уведомлений для типа постов 'informer'
 *
 * @param [type] $messages
 * @return array
 */
function theplugin_post_type_informer_messages($messages)
{
	global $post, $post_ID;

	$messages['informer'] = array(
		0 => '',
		1 => sprintf(__('Информер обновлен.') . ' <a href="%s">' . __('Просмотр') . '</a>', esc_url(get_permalink($post_ID))),
		2 => __('Параметр обновлён.'),
		3 => __('Параметр удалён.'),
		4 => __('Информер обновлен'),
		5 => isset($_GET['revision']) ? sprintf(__('Информер восстановлен из редакции:') . ' %s', wp_post_revision_title((int) $_GET['revision'], false)) : false,
		6 => sprintf(__('Информер опубликован на сайте.') . ' <a href="%s">' . __('Просмотр') . '</a>', esc_url(get_permalink($post_ID))),
		7 => __('Информер сохранен.'),
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
 * Добавление особых прав доступа к типу постов 'informer'
 *
 * @return void
 */
function theplugin_post_type_informer_capability()
{
	theplugin_set_capability_by_custom_post_type(['informer'], 'administrator');
	theplugin_set_capability_by_custom_post_type(['informer']);
}

/**
 * Обновляем данные last-modified Главной, когда информер обновляется
 */
add_action('save_post', function ($post_id) {

	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id))
		return;

	if (get_post_type($post_id) == 'informer') {
		theplugin_update_postdate(get_option('page_on_front'));
	}
});
