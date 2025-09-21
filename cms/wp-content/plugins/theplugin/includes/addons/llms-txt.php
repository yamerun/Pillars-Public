<?php

defined('ABSPATH') || exit;

/**
 * Формирование списка постов для `llms.txt` в виде `- [Link title](https://link_url): Optional link details`
 *
 * @param string $post_type
 * @param array $exclude_ids
 * @return string
 */
function theplugin_llms_get_post_list($post_type = 'page', $exclude_ids = [])
{
	global $wpdb;

	$lists		= '';
	$table		= $wpdb->prefix . 'posts';
	$results	= $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM $table WHERE post_type = %s AND post_status = %s ORDER BY post_title ASC",
		$post_type,
		'publish'
	));

	$posts = [];

	if ($results) {
		foreach ($results as $item) {
			if (!in_array($item->ID, $exclude_ids)) {
				$posts[$item->ID] = [
					'title'			=> $item->post_title,
					'description'	=> get_the_excerpt($item->ID),
					'link'			=> get_permalink($item->ID),
				];
			}
		}
	}

	if ($posts) {
		$post_ids	= array_keys($posts);
		$table		= $wpdb->prefix . 'postmeta';
		$results	= $wpdb->get_results("SELECT * FROM $table WHERE post_id IN (" . join(', ', $post_ids) . ") AND (meta_key = '_yoast_wpseo_metadesc' OR meta_key = '_yoast_wpseo_title')");

		if ($results) {
			$metas = [];
			foreach ($results as $item) {
				if ($item->meta_value) {
					$metas[$item->post_id][$item->meta_key] = $item->meta_value;
				}
			}

			$keys = [
				'_yoast_wpseo_title'	=> 'title',
				'_yoast_wpseo_metadesc'	=> 'description'
			];
			foreach ($metas as $post_id => $item) {
				$patterns = [
					'%%title%%'		=> $posts[$post_id]['title'],
					'%%page%%'		=> '',
					'%%sep%%'		=> '-',
					'%%sitename%%'	=> get_bloginfo('name'),
				];
				foreach ($item as $key => $value) {
					$posts[$post_id][strtr($key, $keys)] = strtr($value, $patterns);
				}
			}
		}

		foreach ($posts as $item) {
			$lists .= sprintf(
				'- [%s](%s): %s' . PHP_EOL,
				$item['title'],
				$item['link'],
				strtr($item['description'], ['  ' => ' ']),
			);
		}
	}

	return $lists;
}

function theplugin_llms_get_term_list($taxonomy = 'category', $exclude_ids = [])
{
	$lists	= '';
	$tax	= get_terms(array(
		'taxonomy'		=> $taxonomy,
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		'hide_empty'	=> false,
		'exclude'		=> $exclude_ids,
	));

	$terms = [];

	if ($tax) {
		$taxmeta = get_option('wpseo_taxonomy_meta');
		foreach ($tax as $term) {
			$terms[$term->term_id] = [
				'title'			=> $term->name,
				'description'	=> $term->description,
				'link'			=> get_term_link($term->term_id, $taxonomy),
			];

			if (isset($taxmeta[$taxonomy][$term->term_id])) {
				$patterns = [
					'%%term_title%%'	=> $term->name,
					'%%sep%%'		=> '-',
					'%%sitename%%'	=> get_bloginfo('name'),
					'%%page%%'		=> '',
					'Архивы '		=> '',
					'  ' => ' '
				];
				if ($taxmeta[$taxonomy][$term->term_id]['wpseo_title']) {
					$terms[$term->term_id]['title'] = strtr($taxmeta[$taxonomy][$term->term_id]['wpseo_title'], $patterns);
				}
				if ($taxmeta[$taxonomy][$term->term_id]['wpseo_desc']) {
					$terms[$term->term_id]['description'] = strtr($taxmeta[$taxonomy][$term->term_id]['wpseo_desc'], $patterns);
				}
			}
		}

		foreach ($terms as $item) {
			$lists .= sprintf(
				'- [%s](%s): %s' . PHP_EOL,
				$item['title'],
				$item['link'],
				strtr($item['description'], ['  ' => ' ']),
			);
		}
	}

	return $lists;
}
