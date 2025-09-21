<?php

defined('ABSPATH') || exit;

add_action('init', function () {
	$post_types = get_post_types();

	foreach ($post_types as $post) {
		$taxonomy_names = get_object_taxonomies($post);

		if ($taxonomy_names) {
			foreach ($taxonomy_names as $taxonomy) {
				// Добавление кастомных массовых действий по мультисайту для таксономии/терминов
				add_filter("bulk_actions-edit-{$taxonomy}", 'theplugin_custom_bulk_multisite_term_actions');

				// Добавление обработчика события кастомных массовых действий по мультисайту
				add_filter("handle_bulk_actions-edit-{$taxonomy}", 'theplugin_custom_bulk_action_multisite_term_handler', 10, 3);
			}
		}
	}
});

// show an appropriate notice
add_action('admin_notices', 'theplugin_custom_bulk_multisite_term_notices');

/**
 * Добавление в список массовых действий для копирования терминов по мультисайту
 *
 * @param [type] $bulk_array
 * @return array
 */
function theplugin_custom_bulk_multisite_term_actions($bulk_array)
{

	$sites = get_sites(
		array(
			'site__not_in'	=> get_current_blog_id(), // exclude the current blog
			'number'		=> 50,
		)
	);

	if ($sites) {
		foreach ($sites as $site) {
			$bulk_array["copy_term_to_{$site->blog_id}"] = "Скопировать в {$site->blogname}";
		}
	}

	return $bulk_array;
}

/**
 * Undocumented function
 *
 * @param [type] $term_id
 * @param [type] $blog_id
 * @return void
 */
function theplugin_multisite_copy_term_to_site($term_id, $blog_id)
{

	$current_blog_id = get_current_blog_id();
	$success = 'success';

	// Получаем данные поста текущего сайта
	$term = get_term($term_id);
	if (!$term) {
		return;
	}

	$catarr = array(
		'cat_ID'				=> 0,					// Очищаем ID поста для вставки данных
		'taxonomy'				=> $term->taxonomy,
		'cat_name'				=> $term->name,
		'category_description'	=> $term->description,
		'category_nicename'		=> $term->slug,
		'category_parent'		=> $term->parent,
		'category_order'		=> $term->term_order,
	);

	// Получаем все мета-данные поста
	$term_meta = get_term_meta($term_id);
	// Задаём пустой массив IDs мульти-постов
	$multisite_term_ids = [];


	if (isset($term_meta['_multisite_term_ids'])) {
		$multisite_term_ids = theplugin_maybe_array($term_meta['_multisite_term_ids'][0]);
		if (isset($multisite_term_ids['blog_' . $blog_id])) {
			$catarr['cat_ID'] = absint($multisite_term_ids['blog_' . $blog_id]);
			if (!theplugin_multisite_term_exists_by_id($catarr['cat_ID'], $blog_id)) {
				$catarr['cat_ID'] = 0;
			}
		}
	}

	// Добавляем ID родителя термина по мультисайту, если есть
	if ($catarr['category_parent']) {
		$termmeta_parent = theplugin_maybe_array(get_term_meta($catarr['category_parent'], '_multisite_term_ids', true));

		if (isset($termmeta_parent['blog_' . $blog_id])) {
			$catarr['category_parent'] = absint($termmeta_parent['blog_' . $blog_id]);
		}
	}

	// Переключаем ID сайта на переданный блог
	switch_to_blog($blog_id);

	// запомним текущее состояние кеша для решения проблемы переполнения памяти
	$was_suspended = wp_suspend_cache_addition();

	// отключаем кэширование
	wp_suspend_cache_addition(true);

	// Добавление/обновление термина таксономии
	$inserted_term_id = wp_insert_category($catarr, true);

	if ($inserted_term_id && !is_wp_error($inserted_term_id)) {

		// Обновление порядка термина по `term_order`
		theplugin_multisite_update_term_order($inserted_term_id, $catarr['category_order']);

		// Обновляем мета-данные
		foreach ($term_meta as $key => $values) {
			// if you do not want weird redirects
			if ('_wp_old_slug' === $key)
				continue;

			foreach ($values as $value) {
				if ($catarr['cat_ID']) {
					$update = update_term_meta($inserted_term_id, $key, $value);
				} else {
					$update = add_term_meta($inserted_term_id, $key, $value);
				}

				// TODO проверка на отсутствие изменений
				if ($update === false) {
					$success = 'meta';
				}
			}
		}

		// Задаём перекрестное значение ID постов-близнецов для обновляемой записи
		$multisite_term_ids['blog_' . $current_blog_id] = absint($term_id);
		update_term_meta($inserted_term_id, '_multisite_term_ids', $multisite_term_ids);
	} else {
		$success = 'fail';
	}

	// отключаем кэширование
	wp_suspend_cache_addition(true);

	// вернем прежнее состояние кэша обратно
	wp_suspend_cache_addition($was_suspended);

	restore_current_blog();

	if ($success == 'success') {
		// Задаём перекрестное значение ID постов-близнецов для старшей записи
		$multisite_term_ids['blog_' . $blog_id] = absint($inserted_term_id);
		update_term_meta($term_id, '_multisite_term_ids', $multisite_term_ids);
	}

	return $success;
}

/**
 * Обработка постов на массовое действие по копированию/обновлению записей в мультисайте
 *
 * @param [type] $redirect
 * @param [type] $doaction
 * @param [type] $object_ids
 * @return array
 */
function theplugin_custom_bulk_action_multisite_term_handler($redirect, $doaction, $object_ids)
{
	// we need query args to display correct admin notices
	$redirect = remove_query_arg(array('theplugin_terms_moved', 'theplugin_blogid', 'theplugin_terms_results'), $redirect);

	// Проверяем наличие ярлыка на действие копирования записей
	if (strpos($doaction, 'copy_term_to_') === 0) {
		$blog_id = str_replace('copy_term_to_', '', $doaction); // get blog ID from action name
		$results = [
			'success'	=> [],
			'fail'		=> [],
			'meta'		=> []
		];

		foreach ($object_ids as $term_id) {
			// Обработка записи
			$success = theplugin_multisite_copy_term_to_site($term_id, $blog_id);
			$results[$success][] = $term_id;
		}

		$redirect = add_query_arg(array(
			'theplugin_terms_moved'		=> count($object_ids),
			'theplugin_terms_results'	=> $results,
			'theplugin_blogid'			=> $blog_id
		), $redirect);
	}

	return $redirect;
}

/**
 * Уведомление об успешном копировании записи
 *
 * @return void
 */
function theplugin_custom_bulk_multisite_term_notices()
{
	if (!empty($_REQUEST['theplugin_terms_moved'])) {

		// because I want to add blog names to notices
		$blog = get_blog_details($_REQUEST['theplugin_blogid']);

		$args = wp_parse_args($_REQUEST['theplugin_terms_results'], array(
			'success'	=> [],
			'fail'		=> [],
			'meta'		=> []
		));

		// depending on ho much terms were changed, make the message different
		echo '<div class="updated notice is-dismissible"><p>';
		printf(
			_n(
				'%d элемент скопирована/обновлена в "%s".',
				'%d элементов скопировано/обновлено в "%s".',
				count($args['success'])
			),
			count($args['success']),
			$blog->blogname
		);
		echo '</p><p>';
		printf(
			'Ошибок копирования: %d',
			count($args['fail']),
			($args['fail']) ? join(', ', $args['fail']) : '',
		);
		echo '<br>';
		printf(
			'Ошибок мета-данных: %d',
			count($args['meta']),
			($args['meta']) ? join(', ', $args['meta']) : '',
		);
		echo '</p></div>';
	}
}


function theplugin_term_exists($term, $taxonomy = '', $parent_term = null)
{
	global $_wp_suspend_cache_invalidation;

	if (null === $term) {
		return null;
	}

	$defaults = array(
		'get'                    => 'all',
		'fields'                 => 'ids',
		'number'                 => 1,
		'update_term_meta_cache' => false,
		'order'                  => 'ASC',
		'orderby'                => 'term_id',
		'suppress_filter'        => true,
	);

	// Ensure that while importing, queries are not cached.
	if (!empty($_wp_suspend_cache_invalidation)) {
		$defaults['cache_results'] = false;
	}

	if (!empty($taxonomy)) {
		$defaults['taxonomy'] = $taxonomy;
		$defaults['fields']   = 'all';
	}

	/**
	 * Filters default query arguments for checking if a term exists.
	 *
	 * @since 6.0.0
	 *
	 * @param array      $defaults    An array of arguments passed to get_terms().
	 * @param int|string $term        The term to check. Accepts term ID, slug, or name.
	 * @param string     $taxonomy    The taxonomy name to use. An empty string indicates
	 *                                the search is against all taxonomies.
	 * @param int|null   $parent_term ID of parent term under which to confine the exists search.
	 *                                Null indicates the search is unconfined.
	 */
	$defaults = apply_filters('term_exists_default_query_args', $defaults, $term, $taxonomy, $parent_term);

	if (is_int($term)) {
		if (0 === $term) {
			return 0;
		}
		$args  = wp_parse_args(array('include' => array($term)), $defaults);
		$terms = get_terms($args);
	} else {
		$term = trim(wp_unslash($term));
		if ('' === $term) {
			return null;
		}

		if (!empty($taxonomy) && is_numeric($parent_term)) {
			$defaults['parent'] = (int) $parent_term;
		}

		$args  = wp_parse_args(array('slug' => sanitize_title($term)), $defaults);
		$terms = get_terms($args);

		if (empty($terms) || is_wp_error($terms)) {
			$args  = wp_parse_args(array('name' => $term), $defaults);
			$terms = get_terms($args);
		}
	}

	if (empty($terms) || is_wp_error($terms)) {
		return null;
	}

	$_term = array_shift($terms);

	if (!empty($taxonomy)) {
		return array(
			'term_id'          => (string) $_term->term_id,
			'term_taxonomy_id' => (string) $_term->term_taxonomy_id,
		);
	}

	return (string) $_term;
}

/**
 * Обновление порадяка термина по переданному ID
 *
 * @param [type] $term_id
 * @param [type] $order
 * @return int|false
 */
function theplugin_multisite_update_term_order($term_id, $order)
{
	global $wpdb;

	if ($term_id && $order) {
		$table = $wpdb->prefix . 'terms';
		return $wpdb->update(
			$table,
			array('term_order' => $order),
			array('term_id' => $term_id)
		);
	}
}
