<?php

defined('ABSPATH') || exit;

/**
 * Проверка сущуствования термина таксономии в различных БД мультисайта с переданным ID
 *
 * @param int $term_id
 * @param string $taxonimy
 * @param int $blog_id
 * @param [type] $output
 * @return object|array|null
 */
function theplugin_multisite_term_exists_by_id($term_id, $blog_id, $output = OBJECT)
{
	global $wpdb;

	$blog_id = absint($blog_id);
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);

	$sql = $wpdb->prepare("SELECT * FROM {$prefix}terms WHERE term_id = %d", $term_id);
	$terms = $wpdb->get_row($sql, $output);

	return $terms;
}

/**
 * Проверка существования термина таксономии в различных БД мультисайта по ярлыку
 *
 * @param string $slug
 * @param string $taxonimy
 * @param int $blog_id
 * @param [type] $output
 * @return object|array|null
 */
function theplugin_multisite_term_exists($slug, $taxonimy, $blog_id, $output = OBJECT)
{
	global $wpdb;

	$blog_id = absint($blog_id);
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);

	$taxonomies = array($taxonimy);
	$taxonomies = implode("','", $taxonomies);

	$sql = "SELECT t.* FROM {$prefix}term_taxonomy AS tt
		INNER JOIN {$prefix}terms AS t ON t.term_id = tt.term_id WHERE t.slug IN ( '{$slug}' ) AND tt.taxonomy IN ( '{$taxonomies}' )";

	$terms = $wpdb->get_row($sql, $output);

	return $terms;
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
function theplugin_multisite_update_term_meta($blog_id, $term_id, $key, $value)
{
	global $wpdb;

	$blog_id = absint($blog_id);
	$prefix = theplugin_multisite_get_blog_prefix($blog_id);
	$value = (is_serialized($value)) ? $value : maybe_serialize($value);

	$table = $prefix . 'termmeta';

	$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE term_id = %d AND meta_key = %s", $term_id, $key));
	if ($exists) {
		$result = $wpdb->update(
			$table,
			['meta_value'	=> $value],
			['meta_id'		=> $exists->meta_id]
		);

		if ($result !== false)
			return $term_id;
	} else {
		$result = $wpdb->insert(
			$table,
			[
				'term_id'		=> $term_id,
				'meta_key'		=> $key,
				'meta_value'	=> $value
			],
		);

		if ($result)
			return $wpdb->insert_id;
	}

	return false;
}
