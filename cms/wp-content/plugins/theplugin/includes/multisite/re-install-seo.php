<?php

defined('ABSPATH') || exit;

add_action('wp', 'theplugin_multisite_reinstall_seo_options_handler', 99);

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_reinstall_seo_options_handler()
{
	if (isset($_GET['seo-options'])) {

		$blog_id = absint($_GET['seo-options']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			$results = theplugin_multisite_reinstall_seo_options($blog_id);
		}
	}
}

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @return array
 */
function theplugin_multisite_reinstall_seo_options($blog_id = 0)
{
	$data = [];

	global $wpdb;
	$table = $wpdb->prefix . 'options';
	$options = theplugin_multisite_reinstall_wp_seo_options($table);

	if ($options) {
		$args = [
			'base'		=> $table,
			'site'		=> theplugin_multisite_get_blog_prefix($blog_id) . 'options',
			'keys'		=> $options,
			'exclude'	=> []
		];

		foreach ($args['keys'] as $key => $option) {
			$args['keys'][$key]['option_value'] = maybe_serialize(theplugin_multisite_reinstall_wp_seo_option_values($option, $blog_id));
		}

		$data = theplugin_multisite_reinstall_wp_options($args);
	}

	return $data;
}

/**
 * Получение списка настроек WooCommerce в переданной таблицы опций
 *
 * @param string $table
 * @return array
 */
function theplugin_multisite_reinstall_wp_seo_options($table)
{
	global $wpdb;

	$esc_like = '%' . $wpdb->esc_like('wpseo') . '%';
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE option_name LIKE %s ORDER BY option_id ASC", $esc_like));

	$data		= [];
	$exclude	= [];

	if ($results) {
		foreach ($results as $item) {
			if (!in_array($item->option_name, $exclude)) {
				$data[$item->option_name] = [
					'option_value'	=> $item->option_value,
					'autoload'		=> $item->autoload,
				];
			}
		}
	}

	return $data;
}

/**
 * Преобразование адреса родительского сайта на поддомен
 *
 * @param string $value
 * @param integer $blog_id
 * @return string
 */
function theplugin_multisite_reinstall_wp_seo_option_values($value = '', $blog_id = 0)
{
	$value = theplugin_maybe_array($value);
	if (is_array($value)) {
		foreach ($value as $key => $val) {
			$value[$key] = theplugin_multisite_reinstall_wp_seo_option_values($val, $blog_id);
		}
	} else {
		$value = str_replace(get_site_url(), get_site_url($blog_id), $value);
	}

	return $value;
}

/**
 * Переписывания seo-тегов под шаблон городов в постах и терменах по переданному ID блога
 *
 * @param integer $blog_id
 * @return array
 */
function theplugin_multisite_reinstall_wp_seo_indexable($blog_id = 0)
{
	$current_blog_id = 1;
	$cities = theplugin_multisite_get_cities($current_blog_id, false);

	if ($current_blog_id == $blog_id || !$cities)
		return null;

	global $wpdb;
	$table_name = 'yoast_indexable';
	$prefix = theplugin_multisite_get_blog_prefix($current_blog_id);
	$table = $prefix . $table_name;
	$taxmeta = get_option('wpseo_taxonomy_meta');
	$_taxmeta = [];

	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE object_id IS NOT NULL"));
	$data = [];

	if ($results) {
		foreach ($results as $item) {
			switch ($item->object_type) {
				case 'post':
					$multi = theplugin_multisite_post_get_sibling_id($item->object_id, $blog_id, $current_blog_id);
					if ($multi) {
						$metas = [
							'title'		=> theplugin_multisite_post_get_meta($current_blog_id, $item->object_id, '_yoast_wpseo_title'),
							'metadesc'	=> theplugin_multisite_post_get_meta($current_blog_id, $item->object_id, '_yoast_wpseo_metadesc'),
						];
					}
					break;
				case 'term':
					$multi = theplugin_multisite_term_get_sibling_id($item->object_id, $blog_id, $current_blog_id);
					$metas = [];
					if ($multi) {
						if (isset($taxmeta[$item->object_sub_type][$item->object_id])) {
							$_taxmeta[$item->object_sub_type][$multi]['wpseo_title']		= strtr($taxmeta[$item->object_sub_type][$item->object_id]['wpseo_title'], $cities);
							$_taxmeta[$item->object_sub_type][$multi]['wpseo_desc']			= strtr($taxmeta[$item->object_sub_type][$item->object_id]['wpseo_desc'], $cities);
							if (isset($taxmeta[$item->object_sub_type][$item->object_id]['wpseo_canonical'])) {
								$_taxmeta[$item->object_sub_type][$multi]['wpseo_canonical']	= theplugin_multisite_replace_site_url($blog_id, $taxmeta[$item->object_sub_type][$item->object_id]['wpseo_canonical']);
							}

							$_taxmeta[$item->object_sub_type][$multi] = wp_parse_args($_taxmeta[$item->object_sub_type][$multi], $taxmeta[$item->object_sub_type][$item->object_id]);
						}
					}
					break;
				default:
					$multi = 0;
					$metas = [];
					break;
			}

			if ($multi) {
				$data[$item->object_type][$item->object_id] = [
					'object_id'			=> $multi,
					'metas'				=> $metas,
					'object_sub_type'	=> $item->object_sub_type,
					'title'				=> strtr($item->title, $cities),
					'description'		=> strtr($item->description, $cities),
					'primary_focus_keyword'	=> $item->primary_focus_keyword,
				];
			}
		}

		if ($data) {
			$table = theplugin_multisite_get_blog_prefix($blog_id) . $table_name;

			foreach ($data as $type => $items) {
				foreach ($items as $id => $item) {
					$object_id	= $item['object_id'];
					$metas		= $item['metas'];
					unset($item['object_id']);
					unset($item['metas']);

					$data[$type][$id] = [
						'update' => $wpdb->update(
							$table,
							$item,
							['object_id' => $object_id, 'object_type' => $type]
						)
					];

					if ($metas) {
						foreach ($metas as $key => $meta) {
							if (!is_wp_error($meta)) {
								$meta = strtr($meta, $cities);
								switch ($type) {
									case 'post':
										$data[$type][$id]['metas'][$key] = theplugin_multisite_update_post_meta($blog_id, $object_id, '_yoast_wpseo_' . $key, $meta);
										break;
									case 'term':
										break;
								}
							}
						}
					}
				}
			}

			if ($_taxmeta) {
				$_taxmeta = wp_parse_args($_taxmeta, $taxmeta);
				$data['wpseo_taxonomy_meta'] = theplugin_multisite_update_option($blog_id, 'wpseo_taxonomy_meta', $_taxmeta);
			}
		}
	}

	return $data;
}

/**
 * Обновление seo-тегов под шаблон городов в переданном посте и ID блога
 *
 * @param [type] $post_id
 * @param integer $blog_id
 * @return int|bool
 */
function theplugin_multisite_update_wp_seo_by_post($post_id, $blog_id = 0)
{
	$current_blog_id = 1;
	$cities = theplugin_multisite_get_cities($current_blog_id, false);

	if ($current_blog_id == $blog_id || !$blog_id || !$cities)
		return null;

	$object_id	= theplugin_multisite_post_get_sibling_id($post_id, $current_blog_id, $blog_id);
	$table_name	= 'yoast_indexable';
	$prefix		= theplugin_multisite_get_blog_prefix($current_blog_id);
	$table		= $prefix . $table_name;

	global $wpdb;
	$item	= $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE object_id = %d AND object_type = %s", $object_id, 'post'));
	$table	= theplugin_multisite_get_blog_prefix($blog_id) . $table_name;

	if ($item) {
		return $wpdb->update(
			$table,
			[
				'object_sub_type'	=> $item->object_sub_type,
				'title'				=> strtr($item->title, $cities),
				'description'		=> strtr($item->description, $cities),
				'primary_focus_keyword'	=> $item->primary_focus_keyword,
			],
			['object_id' => $post_id, 'object_type' => 'post']
		);
	}

	return null;
}

/**
 * Формирование шаблона замены города родительского сайта на теги автозамены в дочернем блоге
 *
 * @param integer $blog_id
 * @return array
 */
function theplugin_multisite_get_cities($blog_id = 1, $key = true)
{
	global $wpdb;
	$table_name = 'options';
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$table = $prefix . $table_name;
	$cities = [];

	$option = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE option_name = %s", 'tp_theme_mods'));

	if ($option) {
		$option = theplugin_maybe_array($option->option_value);

		if (isset($option['city1'])) {
			for ($i = 1; $i < 7; $i++) {
				if ($key) {
					$cities['{{city' . $i . '}}'] = $option['city' . $i];
				} else {
					if (!isset($cities[$option['city' . $i]])) {
						$cities[$option['city' . $i]] = '{{city' . $i . '}}';
					}
				}
			}
		}
	}

	return $cities;
}
