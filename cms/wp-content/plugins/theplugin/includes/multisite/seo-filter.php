<?php

defined('ABSPATH') || exit;

add_filter('wpseo_title', 'theplugin_multisite_wpseo_title_filter', 10, 2);
add_filter('wpseo_opengraph_title', 'theplugin_multisite_wpseo_title_filter', 10, 2);
add_filter('wpseo_metadesc', 'theplugin_multisite_wpseo_title_filter', 10, 2);
add_filter('wpseo_opengraph_desc', 'theplugin_multisite_wpseo_title_filter', 10, 2);
add_filter('wpseo_schema_graph', 'theplugin_multisite_wpseo_schema_graph_filter', 10, 2);

/**
 * Фильтр для преобразования шаблонов `{{city}}` в город
 *
 * @source https://mojwp.ru/kak-vklyuchit-proizvolnyy-shortkod-v-open-graph-razmetke-yoast-seo.html
 *
 * @param string                 $title        The title.
 * @param Indexable_Presentation $presentation The presentation of an indexable.
 *
 * @return string
 */
function theplugin_multisite_wpseo_title_filter($title, $presentation)
{
	if (strpos($title, '{{city') !== false) {
		$replace = theplugin_multisite_wpseo_get_cities();
		$title = strtr($title, $replace);
	}

	return $title;
}

/**
 * Фильтр для вывода `yoast-schema-graph`
 *
 * @param array             $graph   The graph to filter.
 * @param Meta_Tags_Context $context A value object with context variables.
 *
 * @return array
 */
function theplugin_multisite_wpseo_schema_graph_filter($graph, $context)
{

	$replace = theplugin_multisite_wpseo_get_cities();

	$graph = theplugin_multisite_wpseo_replace_values($graph, $replace);

	return $graph;
}

/**
 * Преобразование шаблонов `{{city}}` в город по переданным параметрам
 *
 * @param string $value
 * @param integer $blog_id
 * @return string
 */
function theplugin_multisite_wpseo_replace_values($values, $replace = [])
{
	if (is_array($values)) {
		foreach ($values as $key => $val) {
			$values[$key] = theplugin_multisite_wpseo_replace_values($val, $replace);
		}
	} else {
		$values = strtr($values, $replace);
	}

	return $values;
}


function theplugin_multisite_wpseo_get_cities()
{
	$cities = [];
	for ($i = 1; $i < 7; $i++) {
		$cities['{{city' . $i . '}}'] = theplugin_get_theme_mod('city' . $i);
	}

	return $cities;
}
