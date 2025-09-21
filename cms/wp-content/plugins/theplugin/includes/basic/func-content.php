<?php

defined('ABSPATH') || exit;

/**
 * Функция обработки переданной строки по заданным html-тегам
 *
 * @param array $tags
 * @param string $stroke
 * @param boolean $all
 * @return array
 */
function theplugin_get_preg_tag($tags = array(), $stroke = '', $all = false)
{
	if ($all) {
		preg_match_all('|' . $tags[0] . '(.+)' . $tags[1] . '|isU', $stroke, $data);
	} else {
		preg_match('|' . $tags[0] . '(.+)' . $tags[1] . '|isU', $stroke, $data);
	}
	return $data;
}


/**
 * Вывод содержимого поста по переданному ID
 *
 * @param integer $post_id
 * @param bool $filter использование фильтра the_content, по умолчанию нет
 * @return void
 */
function theplugin_get_content_by_post_id($post_id = 0, $filter = false)
{
	if (empty($post_id))
		return '';

	$_post = get_post($post_id);
	if (!($_post instanceof WP_Post)) {
		return '';
	}

	if ($filter) {
		return apply_filters('the_content', $_post->post_content);
	}

	return $_post->post_content;
}

/**
 * Функция получения постов по переданным аргументам, по умолчанию выводит страницы без родителя
 *
 * @param array $args – аргументы для WP_Query
 * @param array $wrapper – аргументы для обёртки получаемых постов
 * @param string $call_wrapper – функция обертки
 * @return stirng $result
 */
function theplugin_get_posts_by_parent($args = array(), $wrapper = array(), $call_wrapper = 'theplugin_get_posts_wrapper')
{

	$defaults = array(
		'post_parent'	=> 0,
		'post_type'		=> 'page',
		'per'			=> 10,
		'paged'			=> -1,
		'orderby'		=> array('date' => 'asc')
	);

	$args = wp_parse_args($args, $defaults);

	$defaults = array(
		'type'	=> 'list',
		'class'	=> 'posts-list'
	);

	$wrapper = wp_parse_args($wrapper, $defaults);

	$result = '';
	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$item = call_user_func($call_wrapper . '_item', $wrapper);
			if (is_array($item)) {
				if (!is_array($result)) {
					$result = array();
				}
				$result[] = call_user_func($call_wrapper . '_item', $wrapper);
			} else {
				$result .= call_user_func($call_wrapper . '_item', $wrapper);
			}
		}
	}

	wp_reset_query();

	if (empty($result))
		return '';

	// Обработка полученного блока постов
	$result = call_user_func($call_wrapper, $wrapper, $result);

	return $result;
}

/**
 * Функция вывода обёртки единичного поста
 *
 * @param array $wrapper
 * @return void
 */
function theplugin_get_posts_wrapper_item($wrapper = array())
{
	global $post;

	$result = '';
	switch ($wrapper['type']) {
		case 'list':
			$post_id = get_the_ID();
			$result .= sprintf(
				'<li id="post-%d"><a href="%s">%s</a></li>',
				$post_id,
				get_permalink($post_id),
				get_the_title($post_id)
			);
			break;
		case 'data':
			$result = array();
			$post_id = get_the_ID();
			$result = array(
				'post_id'		=> $post_id,
				'post_title'	=> get_permalink($post_id),
				'post_link'		=> get_the_title($post_id)
			);
			break;
		default:
			$result .= theplugin_get_template_part_return('template-parts/content/content', null, array('class' => $wrapper['class']));
			break;
	}

	return $result;
}

/**
 * Функция обработки обёртки всего списка постов
 *
 * @param array $wrapper
 * @param string $result
 * @return void
 */
function theplugin_get_posts_wrapper($wrapper = array(), $result = '')
{
	switch ($wrapper['type']) {
		case 'list':
			$result = sprintf('<ul class="%s">%s</ul>', $wrapper['class'], $result);
			break;
	}

	return $result;
}

/**
 * Функция получения вида постов для страниц архивов
 *
 * @param [type] $post_type
 * @return string
 */
function theplugin_get_post_type($post_type)
{
	if ($post_type == 'post') {
		$post_type = 'single';
	}
	return $post_type;
}

/**
 * Получения списка категорий в виде массива ключ-наименование-дочерние
 *
 * @param array $args
 * @return array
 */
function theplugin_get_categories_list($args = array(), $hierarchical = true)
{
	$defaults	= array(
		'taxonomy'	=> 'category',
		'parent'	=> '',
		'metas'		=> []
	);
	$args		= wp_parse_args($args, $defaults);

	$data 		= [];
	$metas		= $args['metas'];
	unset($args['metas']);
	$categories = get_categories($args);

	if ($categories) {
		foreach ($categories as $cat) {
			$data[$cat->term_id]['id']		= $cat->term_id;
			$data[$cat->term_id]['title']	= $cat->name;
			$data[$cat->term_id]['slug']	= $cat->slug;
			$data[$cat->term_id]['parent']	= $cat->parent;
			$data[$cat->term_id]['order']	= $cat->term_order;
			$data[$cat->term_id]['metas']	= array();
			// Если есть мета-ключи для получения мета-данных термина
			if ($metas) {
				foreach ($metas as $meta_key) {
					$data[$cat->term_id]['metas'][$meta_key] = get_term_meta($cat->term_id, $meta_key, true);
				}
			}
		}

		if ($hierarchical) {
			$data = theplugin_get_categories_list_hierarchical(0, $data);
		}
	}

	return $data;
}

/**
 * Формирование иерархичного списка с вложениями из массива `theplugin_get_categories_list`
 *
 * @param integer $parent_id
 * @param array $categories
 * @return array
 */
function theplugin_get_categories_list_hierarchical($parent_id = 0, $categories = array())
{
	$data = array();

	foreach ($categories as $cat_id => $item) {
		if ($item['parent'] == $parent_id) {
			$data[$cat_id] = $item;
		}
	}

	foreach ($data as $cat_id => $item) {
		$data[$cat_id]['childs'] = theplugin_get_categories_list_hierarchical($cat_id, $categories);
	}

	return $data;
}

/**
 * Кастомизированный порядок вывода произвольного типа записи на странице архива
 *
 * @param [type] $query
 * @return void
 */
function theplugin_custom_type_filter_archive_order_by_menu_asc($query, $post_type = '')
{
	if (!$post_type)
		return null;

	if (!is_admin() && is_archive() && $query->get('post_type') == $post_type) {
		// Сортировка по `menu_order` по возрастанию
		$query->set('orderby', array('menu_order' => 'asc'));
		// Вывести все посты
		$query->set('posts_per_page', -1);
	}

	// TODO прописать универсальную функцию для множества типа записей и порядка вывода в архиве
}
