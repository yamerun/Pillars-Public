<?php

/**
 * Функция получения элементов обратной навигации
 *
 * @source https://misha.agency/wordpress/how-to-create-breadcrumbs.html
 *
 * @return array
 */
function theplugin_get_breadcrumb_items()
{
	global $post;
	$items		= array();

	$nav_main = array(site_url(), 'Главная');

	if (!is_home() && !is_front_page()) {

		$items[] = $nav_main;

		// записи
		if (is_single()) {
			if (get_post_type() != 'post') {
				$_archive 	= get_post_type_object(get_post_type());

				$items[] = array(get_post_type_archive_link(get_post_type()), $_archive->labels->name);

				$taxonomy_post = theplugin_get_taxonomy_list($post->ID);
				if ($taxonomy_post) {
					$items[] = $taxonomy_post;
				}
			} else {
				$items[] = array(get_permalink(get_option('page_for_posts')), get_the_title(get_option('page_for_posts')));
				$items[] 	= thepluign_get_the_category();
			}

			$items[] = array('', get_the_title());

			// страницы
		} elseif (is_page()) {

			if ($post->post_parent) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page =  get_post($parent_id);
					$breadcrumbs[] 	= array(get_permalink($page->ID), get_the_title($page->ID));
					$parent_id  	= $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) $items[] = $crumb;
			}

			$items[] = array('', get_the_title());

			// категории
		} elseif (is_category()) {

			$items[] = array(get_permalink(get_option('page_for_posts')), get_the_title(get_option('page_for_posts')));

			global $wp_query;
			$obj_cat		= $wp_query->get_queried_object();
			$current_cat	= $obj_cat->term_id;
			$current_cat	= get_category($current_cat);
			$parent_cat		= get_category($current_cat->parent);
			if ($current_cat->parent != 0)
				$items[] = array('', get_category_parents($parent_cat, true, ''));

			$items[] = array('', single_cat_title('', false));

			// архивы
		} elseif (is_archive()) {
			$_archive 		= get_post_type_object(get_post_type());
			$_archive_title = strip_tags(get_the_archive_title());
			if ($_archive->labels->name != $_archive_title) {
				$items[] 	= array(get_post_type_archive_link(get_post_type()), $_archive->labels->name);
			}

			if (get_query_var('paged')) {
				$items[] = array(get_post_type_archive_link(get_post_type()), $_archive_title);
			} else {
				$items[] = array('', $_archive_title);
			}

			// страницы поиска
		} elseif (is_search()) {
			$items[] = array('', 'Результат поиска «<strong>' . get_search_query() . '</strong>»');

			// теги (метки)
		} elseif (is_tag()) {
			$items[] = array('', single_tag_title('', false));

			// архивы (по дням)
		} elseif (is_day()) {

			$items[] = array(
				array(get_year_link(get_the_time('Y')), get_the_time('Y')),
				array(get_month_link(get_the_time('Y'), get_the_time('m')), wp_date('F', get_the_time('U'))),
				array('', get_the_time('d'))
			);

			// архивы (по месяцам)
		} elseif (is_month()) {
			$items[] = array(
				array(get_year_link(get_the_time('Y')), get_the_time('Y')),
				array('', wp_date('F', get_the_time('U'))),
			);

			// архивы (по годам)
		} elseif (is_year()) {
			$items[] = array(
				array('', get_the_time('Y'))
			);

			// авторы
		} elseif (is_author()) {
			global $author;
			$userdata 	= get_userdata($author);
			$items[] = array('', 'Автор: ' . $userdata->display_name);

			// если страницы не существует
		} elseif (is_404()) {
			$items[] = array('', 'Ошибка 404');
		}

		// номер текущей страницы
		if (get_query_var('paged'))
			$items[] = array('', get_query_var('paged') . '-я страница');

		// Страница постов
	} elseif (is_home()) {
		$items[] = $nav_main;
		$items[] = array('', get_the_title(get_option('page_for_posts')));

		// Главная
	} elseif (is_front_page()) {
		$page_num = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if ($page_num > 1) {
			$items[] = $nav_main;
			$items[] = array('', $page_num . '-я страница');
		} else {
			$items[] = array('', 'Главная');
		}
	}

	// Если нет элементов, то добавляем один на ссылку Главной
	if (!$items) {
		$items[] = $nav_main;
	}

	return $items;
}

/**
 * Функция формирование html-обёртки обратной навигации
 *
 * @param boolean $echo
 * @param array $args
 * @param string $separator
 * @return string
 */
function theplugin_get_breadcrumb($echo = true, $args = [], $separator = ' / ')
{
	$wrapper 	= [];
	$defaults 	= array(
		'output_before'		=> '<ul class="d-flex f-wrap">',
		'output_after'		=> '</ul>',
		'crumb_before'		=> '<li>',
		'crumb_after'		=> '</li>'
	);

	$args = wp_parse_args($args, $defaults);

	// Получаем элемены обратной навигации
	$nav_items	= theplugin_get_breadcrumb_items();
	$nav_last	= array_pop($nav_items);

	if (count($nav_items) > 0) {
		foreach ($nav_items as $nav_item) {

			// Проверяем является ли элемент навигации подборкой категорий/рубрик
			if (is_array($nav_item[0])) {
				$cat_items = array();
				foreach ($nav_item as $cat_item) {
					// Если есть активная ссылка
					if ($cat_item[0]) {
						$cat_items[] = sprintf(
							'<a href="%s">%s</a>',
							$cat_item[0],
							$cat_item[1]
						);
					} else {
						$cat_items[] = $cat_item[1];
					}
				}

				// Сводим в единый элемент обёртки все категории/рубрики
				$wrapper[] = sprintf(
					'%s%s%s',
					$args['crumb_before'],
					implode($separator, $cat_items),
					$args['crumb_after']
				);
			} else {
				$wrapper[] = sprintf(
					'%s<a href="%s">%s</a>%s',
					$args['crumb_before'],
					$nav_item[0],
					$nav_item[1],
					$args['crumb_after']
				);
			}
		}
	}

	// Последний элемент навигации формируем без ссылки
	$wrapper[] = sprintf(
		'%s%s%s',
		$args['crumb_before'],
		$nav_last[1],
		$args['crumb_after']
	);

	$wrapper = sprintf(
		'%s%s%s',
		$args['output_before'],
		implode("\n\r", $wrapper),
		$args['output_after']
	);

	if ($echo) {
		echo $wrapper;
	} else {
		return $wrapper;
	}
}

function theplugin_get_taxonomy($post_id = 0, $args = array())
{
	if (empty($post_id))
		return '';

	$post		= get_post($post_id);
	$taxonomy	= get_object_taxonomies($post);

	if ($taxonomy) {
		$taxonomy	= array_shift($taxonomy);
		$terms		= wp_get_object_terms($post_id, $taxonomy, $args);

		if ($terms) {
			$defaults	= array(
				'orderby'	=> 'term_order',
				'order'		=> 'ASC'
			);
			$args		= wp_parse_args($args, $defaults);

			return apply_filters('get_the_terms', $terms, $post_id, $taxonomy);
		}
	}

	return '';
}

function theplugin_get_taxonomy_list($post_id = 0, $args = array())
{
	$terms = theplugin_get_taxonomy($post_id, $args);
	$items = array();
	if (!empty($terms)) {
		foreach ($terms as $term) {
			$items[] = array(get_term_link($term), $term->name);
		}

		return $items;
	}

	return '';
}


function thepluign_get_the_category($post_id = false, $separator = '', $parents = '')
{
	if (!is_object_in_taxonomy(get_post_type($post_id), 'category')) {
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters('the_category', '', $separator, $parents);
	}

	/**
	 * Filters the categories before building the category list.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Term[] $categories An array of the post's categories.
	 * @param int|bool  $post_id    ID of the post we're retrieving categories for.
	 *                              When `false`, we assume the current post in the loop.
	 */
	$categories = apply_filters('the_category_list', get_the_category($post_id), $post_id);

	if (empty($categories)) {
		/** This filter is documented in wp-includes/category-template.php */
		return apply_filters('the_category', __('Uncategorized'), $separator, $parents);
	}

	$items = array();
	foreach ($categories as $category) {
		$items[] = array(get_category_link($category->term_id), $category->name);
	}

	return $items;
}

/**
 * WordPress Bootstrap Pagination
 */
function theplugin_get_pagination($args = array())
{

	$defaults = array(
		'range'				=> 4,
		'custom_query'		=> FALSE,
		'previous_string'	=> __('Previous', 'text-domain'),
		'next_string'		=> __('Next', 'text-domain'),
		'before_output'		=> '<ul class="pager">',
		'after_output'		=> '</ul>'
	);

	$args = wp_parse_args(
		$args,
		apply_filters('wp_bootstrap_pagination_defaults', $defaults)
	);

	$args['range'] = (int) $args['range'] - 1;
	if (!$args['custom_query'])
		$args['custom_query'] = @$GLOBALS['wp_query'];
	$count = (int) $args['custom_query']->max_num_pages;
	$page  = intval(get_query_var('paged'));
	$ceil  = ceil($args['range'] / 2);

	if ($count <= 1)
		return FALSE;

	if (!$page)
		$page = 1;

	if ($count > $args['range']) {
		if ($page <= $args['range']) {
			$min = 1;
			$max = $args['range'] + 1;
		} elseif ($page >= ($count - $ceil)) {
			$min = $count - $args['range'];
			$max = $count;
		} elseif ($page >= $args['range'] && $page < ($count - $ceil)) {
			$min = $page - $ceil;
			$max = $page + $ceil;
		}
	} else {
		$min = 1;
		$max = $count;
	}

	$echo = '';
	$previous = intval($page) - 1;
	$previous = esc_attr(get_pagenum_link($previous));

	/* $firstpage = esc_attr( get_pagenum_link(1) );
	if ( $firstpage && (1 != $page) )
		$echo .= '<li class="previous"><a href="' . $firstpage . '">' . __( 'First', 'text-domain' ) . '</a></li>'; */

	if ($previous && (1 != $page))
		$echo .= sprintf(
			'<li class="prev"><a href="%s" title="%s">%s</a></li>',
			$previous,
			__('previous', 'text-domain'),
			$args['previous_string']
		);

	if (!empty($min) && !empty($max)) {
		for ($i = $min; $i <= $max; $i++) {
			if ($page == $i) {
				$echo .= '<li class="active"><span class="active">' . str_pad((int)$i, 1, '0', STR_PAD_LEFT) . '</span></li>';
			} else {
				$echo .= sprintf('<li><a href="%s">%2d</a></li>', esc_attr(get_pagenum_link($i)), $i);
			}
		}
	}

	$next = intval($page) + 1;
	$next = esc_attr(get_pagenum_link($next));
	if ($next && ($count != $page))
		$echo .= '<li class="next"><a href="' . $next . '" title="' . __('next', 'text-domain') . '">' . $args['next_string'] . '</a></li>';

	/* $lastpage = esc_attr( get_pagenum_link($count) );
	if ( $lastpage ) {
		$echo .= '<li class="next"><a href="' . $lastpage . '">' . __( 'Last', 'text-domain' ) . '</a></li>';
	}*/
	if (isset($echo)) {
		echo $args['before_output'] . $echo . $args['after_output'];
	} else {
		echo $args['before_output'] . $args['after_output'];
	}
}
