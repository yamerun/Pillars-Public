<?php

defined('ABSPATH') || exit;

if (!function_exists('pillars_theme_wc_get_product_cat_for_menu')) {
	/**
	 * Вывод списка категорий товаров в хедере/сайдбаре Каталога
	 *
	 * @param array $container
	 * @return string
	 */
	function pillars_theme_wc_get_product_cat_for_menu($container = array(), $product_cats = array())
	{

		$defaults = array(
			'before'	=> '<ul>',
			'after'		=> '</ul>'
		);

		$container		= wp_parse_args($container, $defaults);
		$wrapper		= array();
		if (empty($product_cats)) {
			$product_cats	= theplugin_wc_get_product_categories_general(['_pillars_short_title', '_pillars_category_group']);
		}

		if ($product_cats) {
			$wrapper[] = $container['before'];
			$product_subclass = array();

			foreach ($product_cats as $term_id => $term) {
				$subclass = (isset($term['metas']['_pillars_category_group']) && $term['metas']['_pillars_category_group']) ? $term['metas']['_pillars_category_group'] : 'empty';
				$product_subclass[$subclass][$term_id] = array(
					'link' => get_term_link($term['id'], 'product_cat'),
					'name' => (isset($term['metas']['_pillars_short_title']) && $term['metas']['_pillars_short_title']) ? $term['metas']['_pillars_short_title'] : $term['title']
				);
			}

			$category_groups = pillars_get_option('product_category_group');
			unset($category_groups['-1']);

			foreach ($category_groups as $slug => $title) {
				if (isset($product_subclass[$slug])) {
					$wrapper[] = sprintf(
						'<li class="product_group-%s"><span>%s</span>',
						$slug,
						$title
					);

					$wrapper[] = '<ul class="sub-menu">';

					foreach ($product_subclass[$slug] as $term_id => $item) {
						$wrapper[] = sprintf(
							'<li class="product_cat-%s"><a href="%s"><span>%s</span></a></li>',
							$term_id,
							$item['link'],
							$item['name'],
						);
					}

					$wrapper[] = '</ul>';
					$wrapper[] = '</li>';
				}
			}

			/*
		foreach ($product_cats as $term) {

			$wrapper[] = sprintf(
				'<li class="product_cat-%s"><a href="%s"><span>%s</span></a></li>',
				$term['id'],
				get_term_link($term['id'], 'product_cat'),
				(isset($term['metas']['_pillars_short_title']) && $term['metas']['_pillars_short_title']) ? $term['metas']['_pillars_short_title'] : $term['title']
			);
		}
		*/

			$wrapper[] = $container['after'];
		}

		return implode(PHP_EOL, $wrapper);
	}
}

if (!function_exists('__pl')) {
	/**
	 * Переводит строку по текстовому домену `pillars`
	 *
	 * @param string $text
	 * @return string
	 */
	function __pl($text = '')
	{
		return translate($text, 'pillars');
	}
}

/**
 * Формирование ссылки на Панель управления аккаунта у авторизированных пользователей
 *
 * @param array $args
 * @return void|string
 */
function pillars_account_link($title = '')
{
	if (is_user_logged_in() && pillars_current_user_can('manage_architect_project')) {
		return sprintf(
			'<a href="%s">%s</a>',
			get_permalink(wc_get_page_id('myaccount')),
			$title,
		);
	}

	return '';
}

/**
 * Вывод ссылки на Панель управления аккаунта у авторизированных пользователей
 *
 * @param array $args
 * @return void|string
 */
function pillars_account_link_wrapper($args = array())
{
	$defaults = array(
		'before'	=> 'li',
		'after'		=> 'li',
		'class'		=> 'profile',
		'echo'		=> true
	);

	$args		= wp_parse_args($args, $defaults);
	$link		= pillars_account_link(pillars_theme_get_svg_symbol('my-account'));
	$wrapper	= '';

	if ($link) {
		$wrapper = sprintf(
			'<%s class="%s">%s</%s>',
			$args['before'],
			$args['class'],
			$link,
			$args['after'],
		);
	}

	if ($args['echo']) {
		echo $wrapper;
	} else {
		return $wrapper;
	}
}

/**
 * Парсинг контента для вывода галереи Портфолио
 *
 * @param string $content
 * @return array
 */
function pillars_portfolio_get_gallery_ids(&$content)
{
	$gallery	= theplugin_get_preg_tag(['<!-- wp:gallery', '<!-- /wp:gallery -->'], $content);
	if ($gallery) {
		$content = str_replace($gallery[0], '', $content);
		$images = theplugin_get_preg_tag(['<!-- wp:image ', ' -->'], $gallery[1], true);
		$gallery = [];
		if ($images) {
			foreach ($images[1] as $item) {
				$image = theplugin_maybe_array($item);
				$gallery[] = absint($image['id']);
			}
		}
	} else {
		$gallery = [];
	}

	return $gallery;
}

/**
 * Парсинг контента для вывода видео-обзора Портфолио
 *
 * @param string $content
 * @return string
 */
function pillars_portfolio_get_iframe(&$content)
{
	$iframe		= theplugin_get_preg_tag(['<iframe src=', '</iframe>'], $content);
	if ($iframe) {
		$content	= str_replace($iframe[0], '', $content);
		$iframe		= sprintf('<div class="portfolio-video-review">%s</div>', strtr($iframe[0], ['autoplay=1' => 'autoplay=0',]));
	} else {
		$iframe = '';
	}

	return $iframe;
}

/**
 * Парсинг контента для вывода товаров Портфолио
 *
 * @param string $content
 * @return array
 */
function pillars_portfolio_get_product_ids(&$content)
{
	if (get_current_blog_id() !== 1)
		return [];

	$products	= theplugin_get_preg_tag(['<!-- wp:woocommerce/handpicked-products ', ' /-->'], $content);
	if ($products) {
		$content	= str_replace($products[0], '', $content);
		$products	= theplugin_maybe_array($products[1]);
		$products	= $products['products'];
		arsort($products);
	} else {
		$products = [];
	}

	return $products;
}
