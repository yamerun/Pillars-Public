<?php

defined('ABSPATH') || exit;

add_action('admin_bar_menu', 'theplugin_multisite_add_admin_bar_link', 99);

/**
 * Получение префикса таблицы переданного блога
 *
 * @param integer $blog_id
 * @return string
 */
function theplugin_multisite_get_blog_prefix($blog_id = 0)
{
	$blog_id = absint($blog_id);

	global $wpdb;
	$prefix = 'wp_'; // TODO продумать корректное отображение для $wpdb->prefix
	return ($blog_id !== 1 && $blog_id) ? $prefix . $blog_id . '_' : $prefix;
}

/**
 * Обработка переданного адреса по поддоменам без переключения блога
 *
 * @param [type] $domain
 * @param string $path
 * @param [type] $scheme
 * @return string
 */
function theplugin_multisite_get_url_by_domain($domain = null, $path = '', $scheme = null)
{
	if (empty($domain)) {
		$url = get_option('home');
	} else {
		$url = $domain;
	}

	if (!in_array($scheme, array('http', 'https', 'relative'), true)) {
		if (is_ssl()) {
			$scheme = 'https';
		} else {
			$scheme = parse_url($url, PHP_URL_SCHEME);
		}
	}

	$url = set_url_scheme($url, $scheme);

	if ($path && is_string($path)) {
		$url .= '/' . ltrim($path, '/');
	}

	return $scheme . '://' . $url;
}

/**
 * Формирования смежных ссылок по поддоменам на основе `REQUEST_URI`
 *
 * @param array $args
 * @return array
 */
function theplugin_multisite_get_request_uri_by_blogs($args = ['public' => 1])
{
	$data	= [];

	if (is_multisite()) {
		$current = trim(str_replace('Pillars',  '', get_bloginfo('name')));
		$current = ($current) ? $current : 'Екатеринбург';

		$sites	= get_sites($args);

		$uri	= (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/';
		foreach ($sites as $site) {
			$url = theplugin_multisite_get_url_by_domain($site->domain, $uri);
			$name = trim(str_replace('Pillars',  '', get_blog_details($site->blog_id)->blogname));
			$data[$url] = ($name) ? $name : 'Екатеринбург';
		}
	}

	return $data;
}

/**
 * Получение ID мульти-поста в блоге `$blog_id` по переданному ID поста-родственника в текущем блоге
 *
 * @param [type] $post_id
 * @param [type] $blog_id
 * @return int
 */
function theplugin_multisite_post_get_sibling_id($post_id, $blog_id, $current_blog_id = 0)
{
	if (!$current_blog_id)
		$current_blog_id = get_current_blog_id();

	$multi = theplugin_multisite_post_get_meta($current_blog_id, $post_id, '_multisite_post_ids');
	if (!is_wp_error($multi)) {
		if (is_array($multi) && $multi) {
			if (isset($multi['blog_' . $blog_id])) {
				return absint($multi['blog_' . $blog_id]);
			}
		}
	}

	return 0;
}

/**
 * Получение ID мульти-термина в блоге `$blog_id` по переданному ID термина-родственника в текущем блоге
 *
 * @param [type] $post_id
 * @param [type] $blog_id
 * @return int
 */
function theplugin_multisite_term_get_sibling_id($term_id, $blog_id, $current_blog_id = 0)
{
	if (!$current_blog_id)
		$current_blog_id = get_current_blog_id();

	$multi = theplugin_multisite_term_get_meta($current_blog_id, $term_id, '_multisite_term_ids');
	if (!is_wp_error($multi)) {
		if (is_array($multi) && $multi) {
			if (isset($multi['blog_' . $blog_id])) {
				return absint($multi['blog_' . $blog_id]);
			}
		}
	}

	return 0;
}

/**
 * Получение мета-данный поста по указанному ID блога и поста
 *
 * @param [type] $blog_id
 * @param [type] $post_id
 * @param string $key
 * @param boolean $single
 * @return array|string|int
 */
function theplugin_multisite_post_get_meta($blog_id, $post_id, $key = '', $single = true)
{
	global $wpdb;
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$table = $prefix . 'postmeta';

	// TODO доработать для потска по пустому `$key`
	$query = $wpdb->prepare("SELECT * FROM $table WHERE post_id = %d AND meta_key = %s ORDER BY meta_id DESC", $post_id, $key);

	if ($single) {
		$metas = $wpdb->get_row($query);
	} else {
		$metas = $wpdb->get_results($query);
	}


	if ($metas) {
		if ($single) {
			return theplugin_maybe_array($metas->meta_value);
		} else {
			$values = [];
			foreach ($metas as $meta) {
				$values[$meta->meta_id] = theplugin_maybe_array($meta->meta_value);
			}

			return $values;
		}
	}

	return new WP_Error(404, 'Not found meta values by post');
}

/**
 * Получение мета-данный термина по указанному ID блога и поста
 *
 * @param [type] $blog_id
 * @param [type] $post_id
 * @param string $key
 * @param boolean $single
 * @return array|string|int
 */
function theplugin_multisite_term_get_meta($blog_id, $post_id, $key = '', $single = true)
{
	global $wpdb;
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$table = $prefix . 'termmeta';

	// TODO доработать для потска по пустому `$key`
	$query = $wpdb->prepare("SELECT * FROM $table WHERE term_id = %d AND meta_key = %s ORDER BY meta_id DESC", $post_id, $key);

	if ($single) {
		$metas = $wpdb->get_row($query);
	} else {
		$metas = $wpdb->get_results($query);
	}


	if ($metas) {
		if ($single) {
			return theplugin_maybe_array($metas->meta_value);
		} else {
			$values = [];
			foreach ($metas as $meta) {
				$values[$meta->meta_id] = theplugin_maybe_array($meta->meta_value);
			}

			return $values;
		}
	}

	return new WP_Error(404, 'Not found meta values by term');
}

/**
 * Добавление/обновления мета-данных терминов в различных БД мультисайта
 *
 * @param int $blog_id
 * @param int $term_id
 * @param string $key
 * @param [type] $value
 * @return int|false
 */
function theplugin_multisite_update_post_meta($blog_id, $post_id, $key, $value)
{
	global $wpdb;

	$blog_id = absint($blog_id);
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$value = (is_serialized($value)) ? $value : maybe_serialize($value);

	$table = $prefix . 'postmeta';

	$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE post_id = %d AND meta_key = %s", $post_id, $key));
	if ($exists) {
		$result = $wpdb->update(
			$table,
			['meta_value'	=> $value],
			['meta_id'		=> $exists->meta_id]
		);

		if ($result !== false)
			return $post_id;
	} else {
		$result = $wpdb->insert(
			$table,
			[
				'post_id'		=> $post_id,
				'meta_key'		=> $key,
				'meta_value'	=> $value
			],
		);

		if ($result)
			return $wpdb->insert_id;
	}

	return false;
}

/**
 * Получение url блога по переданному ID без переключения по `switch_to_blog`
 *
 * @param integer $blog_id
 * @return string|null
 */
function theplugin_multisite_get_site_option($blog_id = 0, $name = 'siteurl')
{
	global $wpdb;
	$table = theplugin_multisite_get_blog_prefix($blog_id) . 'options';

	$option = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE option_name = %s", $name));
	if ($option) {
		return set_url_scheme($option->option_value);
	}

	return null;
}

/**
 * Замена url основго сайта на url блога по переданному ID
 *
 * @param integer $blog_id
 * @param string $url
 * @return void
 */
function theplugin_multisite_replace_site_url($blog_id = 0, $url = '')
{
	$site_url = theplugin_multisite_get_site_option(1, 'siteurl');
	$blog_url = theplugin_multisite_get_site_option($blog_id, 'siteurl');

	return str_replace($site_url, $blog_url, $url);
}

/**
 * Обновлении опции блога без переключения по `switch_to_blog`
 *
 * @param integer $blog_id
 * @return int|false
 */
function theplugin_multisite_update_option($blog_id = 0, $option_name = '', $option_value = '')
{
	global $wpdb;
	$table = theplugin_multisite_get_blog_prefix($blog_id) . 'options';
	$option_value = (is_serialized($option_value)) ? $option_value : maybe_serialize($option_value);
	// TODO возможно придётся сделать проверку на существование опции
	return $wpdb->update($table, ['option_value' => $option_value], ['option_name' => $option_name]);
}

/**
 * Получение списка зарегистррованных таксономий атрибутов Товара
 *
 * @param array $fields
 * @return array
 */
function theplugin_multisite_get_wc_attribute_taxonomies($fields = [])
{
	global $wpdb;
	$table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
	$attrs = $wpdb->get_results("SELECT * FROM $table ORDER BY attribute_id");

	$data = [];

	if ($attrs) {
		foreach ($attrs as $attr) {
			$data[$attr->attribute_id] = [];
			foreach ($attr as $key => $value) {
				if (in_array($key, $fields) || !$fields) {
					$data[$attr->attribute_id][$key] = $value;
				}
			}
		}
	}

	return $data;
}

/**
 * Обновление даты изменения поста по текущему времени
 *
 * @param int $post_id
 * @param int $blog_id
 * @return int
 */
function theplugin_multisite_update_postdate($post_id = 0, $blog_id = 1)
{
	if (!$post_id || !$blog_id)
		return null;

	global $wpdb;
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$tablename	= 'posts';
	$table		= $prefix . $tablename;
	$time		= current_time('Y-m-d H:i:s');

	return $wpdb->update(
		$table,
		[
			'post_modified'		=> $time,
			'post_modified_gmt'	=> get_gmt_from_date($time)
		],
		['ID'	=> $post_id]
	);
}

/**
 * Обновление мета-тегов постов по `_multisite_post_ids`
 *
 * @return void
 */
function theplugin_multisite_update_post_meta_multi_ids()
{
	global $wpdb;
	$current_id = 1;
	$blog_id = 21;
	$_blog_id = 22;
	$prefix = theplugin_multisite_get_blog_prefix($current_id);
	$table = 'postmeta';

	$tablename = $prefix . $table;

	theplugin_get_dump($tablename);

	$metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE meta_key = %s ORDER BY post_id", '_multisite_post_ids'));

	$data = [];

	if ($metas) {
		foreach ($metas as $item) {
			$ids = theplugin_maybe_array($item->meta_value);

			foreach ($ids as $blog => $id) {
				$ids[$blog] = absint($id);
			}

			if (isset($ids['blog_' . $blog_id])) {

				if (!isset($ids['blog_' . $_blog_id])) {
					$ids['blog_' . $_blog_id] = $ids['blog_' . $blog_id];
				}

				foreach ($ids as $blog => $id) {
					$ids[$blog] = absint($id);
					$data[$blog][$id] = maybe_serialize($ids);
				}
			} else {
				theplugin_get_dump(['no-isset-' . $blog_id => $ids]);
			}
		}
	}

	theplugin_get_dump($data);
}

/**
 * Обновление мета-тегов терминов по `_multisite_term_ids`
 *
 * @return void
 */
function theplugin_multisite_update_term_meta_multi_ids()
{
	global $wpdb;
	$current_id = 1;
	$blog_id = 21;
	$_blog_id = 22;
	$prefix = theplugin_multisite_get_blog_prefix($current_id);
	$table = 'termmeta';

	$tablename = $prefix . $table;

	theplugin_get_dump($tablename);

	$metas = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tablename WHERE meta_key = %s ORDER BY term_id", '_multisite_term_ids'));

	$data = [];

	if ($metas) {
		foreach ($metas as $item) {
			$ids = theplugin_maybe_array($item->meta_value);

			foreach ($ids as $blog => $id) {
				$ids[$blog] = absint($id);
			}

			if (isset($ids['blog_' . $blog_id])) {

				if (!isset($ids['blog_' . $_blog_id])) {
					$ids['blog_' . $_blog_id] = absint($ids['blog_' . $blog_id]);
				}

				foreach ($ids as $blog => $id) {
					$id = absint($id);
					$ids[$blog] = $id;

					$data[$blog][$id] = maybe_serialize($ids);
				}
			} else {
				theplugin_get_dump(['no-isset-' . $blog_id => $ids]);
			}
		}
	}

	theplugin_get_dump($data);

	foreach ($data as $blog => $items) {
		$blog_id = absint(preg_replace('#[^0-9]#', '', $blog));
		$prefix = theplugin_multisite_get_blog_prefix($blog_id);
		$tablename = $prefix . $table;
		echo '<hr>';
		theplugin_get_dump($tablename);
		foreach ($items as $term_id => $values) {
			theplugin_get_dump([$term_id => [$blog, $values]]);
		}
	}
}

/**
 * Пакетная обработка обновления мета-данных всех мульти-постов
 *
 * @param [type] $post_id
 * @param [type] $key
 * @param [type] $value
 * @param integer $current_blog_id
 * @return array
 */
function theplugin_multisite_update_post_meta_by_siblings($post_id, $key, $value, $current_blog_id = 0)
{
	$logs = [];
	$sites = get_sites();
	if ($sites && $post_id) {
		foreach ($sites as $site) {
			$sibling_id = theplugin_multisite_post_get_sibling_id($post_id, $site->blog_id, $current_blog_id);
			if ($sibling_id) {
				$logs[$site->blog_id][$sibling_id] = theplugin_multisite_update_post_meta($site->blog_id, $sibling_id, $key, $value);
			}
		}
	}

	return $logs;
}


/**
 * Добавление в админ-панель смежный ссылок на поддомены
 *
 * @param [type] $wp_admin_bar
 * @return void
 */
function theplugin_multisite_add_admin_bar_link($wp_admin_bar)
{
	$sites = theplugin_multisite_get_request_uri_by_blogs(['public' => null]);
	if ($sites) {

		$args = array(
			'id'		=> 'multi-site-links',
			'parent'	=> 'site-name',
			'title'		=> 'Мульти-ссылки',
			'href'		=> '#'
		);
		$wp_admin_bar->add_node($args);

		foreach ($sites as $url => $site) {
			$wp_admin_bar->add_node(array(
				'id'		=> 'multi-site-links-' . sanitize_title($site),
				'parent'	=> 'multi-site-links',
				'title'		=> $site,
				'href'		=> $url,
				'meta'		=> ['target' => '_blank']
			));
		}
	}
}
