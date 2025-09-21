<?php

defined('ABSPATH') || exit;

/**
 * Copy Pages Between Sites in Multisite Network
 * @source https://rudrastyh.com/wordpress-multisite/copy-pages-between-sites.html
 *
 */
add_action('init', function () {
	$post_types = get_post_types();

	foreach ($post_types as $post) {
		if (!in_array($post, ['product'])) {
			// Добавление кастомных массовых действий по мультисайту для таксономии/терминов
			add_filter("bulk_actions-edit-{$post}", 'theplugin_custom_bulk_multisite_post_actions');

			// Добавление обработчика события кастомных массовых действий по мультисайту
			add_filter("handle_bulk_actions-edit-{$post}", 'theplugin_custom_bulk_action_multisite_post_handler', 10, 3);
		}
	}
});

// show an appropriate notice
add_action('admin_notices', 'theplugin_custom_bulk_multisite_post_notices');

/**
 * Добавление в список массовых действий для копирования постов по мультисайту
 *
 * @param [type] $bulk_array
 * @return array
 */
function theplugin_custom_bulk_multisite_post_actions($bulk_array)
{

	$sites = get_sites(
		array(
			// 'site__in' => array( 1,2,3 )
			'site__not_in'	=> get_current_blog_id(), // exclude the current blog
			'number'		=> 50,
		)
	);

	if ($sites) {
		foreach ($sites as $site) {
			$bulk_array["copy_post_to_{$site->blog_id}"] = "Скопировать в {$site->blogname}";
		}
	}

	return $bulk_array;
}

/**
 * Undocumented function
 *
 * @param [type] $post_id
 * @param [type] $blog_id
 * @return void
 */
function theplugin_multisite_copy_post_to_site($post_id, $blog_id)
{
	$current_blog_id = get_current_blog_id();
	$success = 'success';

	// Получаем данные поста текущего сайта
	$post = get_post($post_id, ARRAY_A);
	if (!$post) {
		return;
	}

	// Получаем все мета-данные поста
	$post_meta = get_post_custom($post_id);
	// Задаём пустой массив IDs мульти-постов
	$multisite_post_ids = [];
	// Очищаем ID поста для вставки данных
	$post['ID'] = '';

	if (isset($post_meta['_multisite_post_ids'])) {
		$multisite_post_ids = theplugin_maybe_array($post_meta['_multisite_post_ids'][0]);
		if (isset($multisite_post_ids['blog_' . $blog_id])) {
			$post['ID'] = absint($multisite_post_ids['blog_' . $blog_id]);
		}
	}

	// Добавляем ID родителя поста по мультисайту, если есть
	if ($post['post_parent']) {

		$postmeta_parent = theplugin_maybe_array(get_post_meta($post['post_parent'], '_multisite_post_ids', true));

		if (isset($postmeta_parent['blog_' . $blog_id])) {
			$post['post_parent'] = absint($postmeta_parent['blog_' . $blog_id]);
		}
	}

	/**
	 * Обработка мета-данных, если это элемент меню
	 */
	if ($post['post_type'] == 'nav_menu_item') {

		// Обновляем мета-данные
		foreach ($post_meta as $key => $values) {
			if ($key == '_menu_item_object_id') {
				foreach ($values as $i => $id) {
					// Определяем тип элемента, на которрый ссылается меню
					switch ($post_meta['_menu_item_type'][$i]) {
						case 'post_type':
							$nav_meta = get_post_meta($id, '_multisite_post_ids', true);
							break;
						case 'taxonomy':
							$nav_meta = get_term_meta($id, '_multisite_term_ids', true);
							break;
						default:
							// post_type_archive – страница архивов
							// custom – произвольная
							$nav_meta = [];
							break;
					}

					if (isset($nav_meta['blog_' . $blog_id])) {
						$post_meta['_menu_item_object_id'][$i] = absint($nav_meta['blog_' . $blog_id]);
					}

					// Родительский элемент меню по ID если есть
					if ($post_meta['_menu_item_menu_item_parent'][$i]) {
						$nav_post = get_post($post_meta['_menu_item_menu_item_parent'][$i]);
						// Проверяем наличие элемента меню
						if ($nav_post && $nav_post->post_type == 'nav_menu_item') {
							$nav_meta = get_post_meta($nav_post->ID, '_multisite_post_ids', true);
							if (isset($nav_meta['blog_' . $blog_id])) {
								$post_meta['_menu_item_menu_item_parent'][$i] = $nav_meta['blog_' . $blog_id];
							}
						}
					}

					break;
				}
			}
		}
	}

	// Изменение `guid` на url-блога
	$post['guid'] = str_replace(get_site_url(), get_site_url($blog_id), $post['guid']);
	// Проверка на наличие GET-переменной `p` или `page_id`
	preg_match('/((p|page_id)=(\d+))$/', $post['guid'], $guid);
	if ($guid && isset($post['ID'])) {
		$guid = [$guid[2], absint($guid[3])];
		$post['guid'] = str_replace(join('=', $guid), $guid[0] . '=' . $post['ID'], $post['guid']);
	}

	if ($post['post_name'] == $post_id && $post['ID']) {
		$post['post_name'] = $post['ID'];
	}

	// Переключаем ID сайта на переданный блог
	switch_to_blog($blog_id);

	// запомним текущее состояние кеша для решения проблемы переполнения памяти
	$was_suspended = wp_suspend_cache_addition();

	// отключаем кэширование
	wp_suspend_cache_addition(true);

	// insert the page
	// TODO добавить проверку существования поста
	if ($post['ID']) {
		$inserted_post_id = wp_update_post($post); // обновление поста
	} else {
		$inserted_post_id = wp_insert_post($post); // добавление поста
	}

	if ($inserted_post_id && !is_wp_error($inserted_post_id)) {

		$post['ID'] = $inserted_post_id;

		// Получаем все мета-данные мульти-записи блога
		$_post_meta = [];
		if ($post['ID']) {
			$_post_meta = get_post_custom($post['ID']);
		}

		$cities = [];
		if ($current_blog_id === 1) {
			$cities = theplugin_multisite_get_cities($current_blog_id, false);
		}

		// Обновляем мета-данные
		foreach ($post_meta as $key => $values) {
			// if you do not want weird redirects
			if ('_wp_old_slug' === $key)
				continue;

			foreach ($values as $i => $value) {
				if (in_array($key, ['_yoast_wpseo_title', '_yoast_wpseo_metadesc']) && $current_blog_id === 1) {
					$value = strtr($value, $cities);
				}

				// Проверяем наличие прошлого значения записи и его соотвествие, чтобы добавить/обновить мета-значение
				if (!isset($_post_meta[$key][$i]) || (isset($_post_meta[$key][$i]) && $value !== $_post_meta[$key][$i])) {
					if (is_array(theplugin_maybe_array($value))) {
						$value = theplugin_maybe_array($value);
					}
					$update = update_post_meta($inserted_post_id, $key, $value);

					if ($update === false) {
						$success = 'meta';
					}
				}
			}
		}

		// Задаём перекрестное значение ID постов-близнецов для обновляемой записи
		$multisite_post_ids['blog_' . $current_blog_id] = $post_id;
		$multisite_post_ids['blog_' . $blog_id] = $inserted_post_id;
		update_post_meta($inserted_post_id, '_multisite_post_ids', $multisite_post_ids);
	} else {
		$success = 'fail';
	}

	// вернем прежнее состояние кэша обратно
	wp_suspend_cache_addition($was_suspended);

	restore_current_blog();

	if ($success == 'success' || $inserted_post_id) {
		$post['ID'] = $inserted_post_id;
		// Задаём перекрестное значение ID постов-близнецов для старшей записи
		update_post_meta($post_id, '_multisite_post_ids', $multisite_post_ids);

		// Обновление SEO-тега в таблице плагина YoastSEO
		theplugin_multisite_update_wp_seo_by_post($inserted_post_id, $blog_id);
	}

	theplugin_multisite_set_object_terms($post_id, $post, $blog_id, $current_blog_id);

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
function theplugin_custom_bulk_action_multisite_post_handler($redirect, $doaction, $object_ids)
{
	// we need query args to display correct admin notices
	$redirect = remove_query_arg(array('theplugin_posts_moved', 'theplugin_blogid', 'theplugin_posts_results'), $redirect);

	// Проверяем наличие ярлыка на действие копирования записей
	if (strpos($doaction, 'copy_post_to_') === 0) {
		$blog_id = str_replace('copy_post_to_', '', $doaction); // get blog ID from action name
		$results = [
			'success'	=> [],
			'fail'		=> [],
			'meta'		=> []
		];

		foreach ($object_ids as $post_id) {
			// Обработка записи
			$success = theplugin_multisite_copy_post_to_site($post_id, $blog_id);
			$results[$success][] = $post_id;
		}

		$redirect = add_query_arg(array(
			'theplugin_posts_moved'		=> count($object_ids),
			'theplugin_posts_results'	=> $results,
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
function theplugin_custom_bulk_multisite_post_notices()
{
	if (!empty($_REQUEST['theplugin_posts_moved'])) {

		// because I want to add blog names to notices
		$blog = get_blog_details($_REQUEST['theplugin_blogid']);

		$args = wp_parse_args($_REQUEST['theplugin_posts_results'], array(
			'success'	=> [],
			'fail'		=> [],
			'meta'		=> []
		));

		// depending on ho much posts were changed, make the message different
		echo '<div class="updated notice is-dismissible"><p>';
		printf(
			_n(
				'%d запись скопирована/обновлена в "%s".',
				'%d записей скопировано/обновлено в "%s".',
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

/**
 * Undocumented function
 *
 * @param int $post_id
 * @param [type] $post
 * @param int $blog_id
 * @param integer $current_blog_id
 * @return array
 */
function theplugin_multisite_set_object_terms($post_id, $post, $blog_id, $current_blog_id = 1)
{
	$post = theplugin_object_to_array($post);
	$taxonomy_names = get_object_taxonomies($post['post_type']);
	$taxonomies = [];

	if ($taxonomy_names) {
		foreach ($taxonomy_names as $taxonomy) {
			$terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'id=>slug'));
			if ($terms) {
				$taxonomies[$taxonomy] = $terms;
			}
		}
	}

	$_taxonomies = [];
	if ($taxonomies) {
		foreach ($taxonomies as $taxonomy => $terms) {
			foreach ($terms as $term_id => $slug) {

				$exists	= theplugin_multisite_term_exists($slug, $taxonomy, $blog_id);
				$term	= 0;

				if ($exists) {
					$termmeta = get_term_meta($term_id, '_multisite_term_ids', true);
					if (isset($termmeta['blog_' . $current_blog_id]) && isset($termmeta['blog_' . $blog_id])) {
						$termmeta['blog_' . $current_blog_id] = $term_id;
						$termmeta['blog_' . $blog_id] = $exists->term_id;

						update_term_meta($term_id, '_multisite_term_ids', $termmeta);
						theplugin_multisite_update_term_meta($blog_id, $exists->term_id, '_multisite_term_ids', $termmeta);

						$term = $exists->term_id;
					}
				} else {
					delete_term_meta($term_id, '_multisite_term_ids');
				}

				if ($term) {
					$_taxonomies[$taxonomy][$term] = $slug;
				}
			}
		}
	}

	$data = [];
	if ($_taxonomies) {
		switch_to_blog($blog_id);
		// запомним текущее состояние кеша для решения проблемы переполнения памяти
		$was_suspended = wp_suspend_cache_addition();
		// отключаем кэширование
		wp_suspend_cache_addition(true);

		foreach ($_taxonomies as $taxonomy => $terms) {
			// Проверяем связку терминов таксономии с товаром
			foreach ($terms as $term_id => $term_slug) {
				if (!has_term($term_id, $taxonomy, $post['ID'])) {
					$data[$taxonomy][$term_slug] = wp_set_object_terms($post['ID'], $term_slug, $taxonomy, true);
				}
			}
		}

		if (strpos($post['post_type'], 'product') === 0) {
			$pa = [];
			foreach ($_taxonomies as $taxonomy => $terms) {
				if (strpos($taxonomy, 'pa_') === 0) {
					$pa[$taxonomy] = $terms;
				}
			}

			if ($pa) {
				theplugin_wc_product_add_attributes($post['ID'], $pa, true);
			}
		}

		// вернем прежнее состояние кэша обратно
		wp_suspend_cache_addition($was_suspended);
		restore_current_blog();
	}

	return $data;
}
