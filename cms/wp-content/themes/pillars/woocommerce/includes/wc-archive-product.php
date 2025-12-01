<?php

defined('ABSPATH') || exit;

add_action('woocommerce_archive_description', 'pillars_wc_archive_title_header', 5);
add_action('woocommerce_archive_description', 'pillars_wc_archive_shop_tabs_header', 5);
// add_action('woocommerce_archive_description', 'pillars_wc_archive_description_header', 10);

add_action('woocommerce_after_main_content', 'pillars_wc_archive_videoreviews', 10);
add_action('woocommerce_after_main_content', 'pillars_wc_archive_description', 10);
add_action('woocommerce_after_main_content', 'pillars_wc_archive_after_main_content_advantages', 15);
add_action('woocommerce_after_main_content', 'pillars_wc_archive_after_main_content_news', 20);

add_filter('woocommerce_product_loop_start', 'pillars_wc_product_loop_start_filter');

/**
 * Шаблон вывода заголовка в шапке Категории магазина
 *
 * @return void
 */
function pillars_wc_archive_title_header()
{
	if (is_shop()) {
		if (apply_filters('woocommerce_show_page_title', true)) { ?>
			<h1><?php woocommerce_page_title(); ?></h1>
		<?php
		}
	}

	if (is_product_taxonomy()) {
		$term = get_term_by('name', woocommerce_page_title(false), 'product_cat');
		if ($term) {
			$image_id		= get_term_meta($term->term_id, '_pillars_cat_title_image_id', true);
			$image_position	= get_term_meta($term->term_id, '_pillars_cat_title_image_position', true);

			ob_start();
			pillars_wc_archive_description_header();
			$description = ob_get_clean();

			if ($image_id) {
				echo sprintf(
					'<div class="pillars-wc-term__title-image%s"><div class="media-ratio">%s</div><h1>%s</h1>%s</div>',
					($image_position && $image_position != '-1') ? ' --' . $image_position : '',
					wp_get_attachment_image($image_id, 'full'),
					woocommerce_page_title(false),
					$description
				);
			} else {
				echo '<h1>' . woocommerce_page_title(false) . '</h1>';
				echo $description;
			}
		}
	}

	if (is_tax('pa_kollektsiya')) {
		if (apply_filters('woocommerce_show_page_title', true)) { ?>
			<h1>Коллекция «<?php woocommerce_page_title(); ?>»</h1>
		<?php
		}
	}
}

/**
 * Шаблон вывода табов в шапке Каталога магазина
 *
 * @return void
 */
function pillars_wc_archive_shop_tabs_header()
{
	if (is_shop()) {
		$category_groups = pillars_get_option('product_category_group');
		unset($category_groups['-1']);
		unset($category_groups['playgrounds']);

		$args = array(
			'label'		=> 'Навигация по подкатегориям Каталога',
			'class'		=> [
				'container'	=> 'pillars-tabs',
				'wrapper'	=> 'pillars-tabs__wrapper',
			],
			'params'	=> [
				'data-tab_group' => 'product-groups'
			],
			'items'		=> array()
		);

		foreach ($category_groups as $id => $title) {
			$args['items'][] = array(
				'link'	=> true,
				'href'	=> '#product-group-' . $id,
				'label'	=> $title,
				'class'	=> 'pillars-tabs__item'
			);
		}

		$params = array(
			'aria-label="' . esc_attr($args['label'])  . '"'
		);
		if ($args['params']) {
			foreach ($args['params'] as $attr => $value) {
				$params[] = $attr . '="' . esc_attr($value) . '"';
			}
		}

		$params = join(' ', $params);

		?>
		<nav class="pillars-tabs" <?= $params ?>>
			<ul class="pillars-tabs__wrapper">
				<?php foreach ($args['items'] as $item) {
					echo sprintf(
						'
						<li class="%s"><a href="%s">%s</a></li>',
						$item['class'],
						$item['href'],
						$item['label']
					);
				} ?>
			</ul>
		</nav>
<?php
	}
}

/**
 * Шаблон вывода описания в шапке Категории магазина
 *
 * @return void
 */
function pillars_wc_archive_description_header()
{
	if (is_product_taxonomy() && 0 === absint(get_query_var('paged'))) {
		$term = get_queried_object();

		if ($term) {
			$args['description'] = get_term_meta($term->term_id, '_pillars_cat_description_top', true);

			if ($args['description']) {
				wc_get_template('archive-product/term-description-header.php', $args);
			}
		}
	}
}

/**
 * Шаблон вывода описания в шапке Категории магазина
 *
 * @return void
 */
function pillars_wc_archive_videoreviews()
{
	if (is_product_taxonomy() && 0 === absint(get_query_var('paged'))) {
		$term = get_queried_object();

		if ($term) {
			$args['reviews'] = get_term_meta($term->term_id, '_product_category_video_review', true);

			if ($args['reviews']) {
				wc_get_template('archive-product/videoreviews.php', $args);
			}
		}
	}
}

/**
 * Undocumented function
 *
 * @return void
 */
function pillars_wc_archive_description()
{
	if (is_product_taxonomy() && 0 === absint(get_query_var('paged'))) {
		$term = get_queried_object();

		if ($term && !empty($term->description)) {
			$args['description']	= wc_format_content(wp_kses_post($term->description)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$args['term_id']		= $term->term_id;
			wc_get_template('archive-product/term-description.php', $args);
		}
	}
}

/**
 * Вывод новостного слайдера внизу страницы Категории
 *
 * @return void
 */
function pillars_wc_archive_after_main_content_advantages()
{
	if (is_product_taxonomy()) {
		echo do_shortcode('[tp-get-part part="advantage" args="section:true,"]');
	}
}

/**
 * Вывод новостного слайдера внизу страницы Категории
 *
 * @return void
 */
function pillars_wc_archive_after_main_content_news()
{
	if (is_product_taxonomy()) {
		echo do_shortcode('[tp-get-part part="news-slider"]');
	}
}

/**
 * Форматирование css-класса wc-columns для вывода товара по модульной сетки темы
 *
 * @param  $ob_get_clean
 *
 * @return string
 */
function pillars_wc_product_loop_start_filter($ob_get_clean)
{
	if (is_product_taxonomy()) {
		$ob_get_clean = str_replace('products-columns-' . esc_attr(wc_get_loop_prop('columns')), 'products-columns-row row-sm', $ob_get_clean);
	}

	return $ob_get_clean;
}

/**
 * Получение параметров для выводов табов в категориях товара
 *
 * @param array $args
 * @return array
 */
function pillars_wc_set_categories_tab_items($args = array())
{
	$params = array(
		'label'		=> 'Навигация по подкатегориям',
		'class'		=> [
			'container'	=> 'pillars-tabs',
			'wrapper'	=> 'pillars-tabs__wrapper',
		],
		'params'	=> [
			'data-tab_group' => 'product-groups'
		],
		'items'		=> array(),
		'attrs'		=> ''
	);

	if (!$args)
		return $params;

	foreach ($args as $cat_id => $value) {
		if (isset($value['title'])) {
			$params['items'][] = array(
				'data-id'	=> ($value['slug']) ? $value['slug'] : '#',
				'href'		=> (isset($value['link']) && $value['link']) ? $value['link'] : '#',
				'label'		=> sprintf('<span class="hide-sm">%s</span><span class="show-sm">%s</span>', $value['title'], $value['short']),
				'class'		=> 'pillars-tabs__item' . ((isset($value['redirect']) && $value['redirect']) ? ' --no-tab' : '')
			);
		}
	}

	$params['attrs'] = array(
		'aria-label="' . esc_attr($params['label'])  . '"'
	);
	if ($params['params']) {
		foreach ($params['params'] as $attr => $value) {
			$params['attrs'][] = $attr . '="' . esc_attr($value) . '"';
		}
	}
	$params['attrs'] = join(' ', $params['attrs']);

	return $params;
}

/**
 * Вывод сгруппированных товаров по заданным условиям
 *
 * @param array $pa_args
 * @param string|array $tax
 * @param boolean $filter
 * @param string $tax_order
 * @param array $product_order
 * @return void
 */
function pillars_wc_get_categories_list_filter($pa_args = [], $tax = '', $filter = true, $tax_order = null, $product_order = ['menu_order' => 'ASC'])
{
	if (empty($pa_args)) {
		return;
	}

	if (isset($pa_args['link'])) {
		$link = $pa_args['link'];
		unset($pa_args['link']);
	} else {
		$link = false;
	}

	if (isset($pa_args['single']) && $pa_args['single'] == true) {
		$single = true;
		unset($pa_args['single']);
	} else {
		$single = false;
	}

	foreach ($pa_args as $cat_id => $args) {
		$pa_args[$cat_id]['slug'] = '';
		$pa_args[$cat_id]['name'] = '';

		if (absint($cat_id)) {
			$term = get_term($cat_id, 'product_cat');
			if ($term instanceof WP_Term) {
				$pa_args[$cat_id]['slug'] = $term->slug;
				$pa_args[$cat_id]['name'] = $term->name;
				$pa_args[$cat_id]['link'] = get_term_link($term, 'product_cat');
			}
		}
	}

	if ($filter) {
		wc_get_template('loop/loop-filter-ajax.php', array_merge($pa_args, ['link' => $link]));
	}

	if ($single) {
		foreach ($pa_args as $cat_id => $args) {
			if ($cat_id != '0') {
				unset($pa_args[$cat_id]);
			}
		}
		$term = get_term($link, 'product_cat');
		if ($term instanceof WP_Term) {
			$pa_args[$link]['slug'] = $term->slug;
			$pa_args[$link]['name'] = $term->name;
		}
	}
	unset($pa_args['0']);

	$pa_terms = [false];
	if (!empty($tax)) {
		$tax_args = array(
			'hide_empty' => !current_user_can('manage_woocommerce'),
		);
		if ($tax_order) {
			$tax_args['orderby'] = $tax_order;
		}
		$pa_terms = get_terms(
			$tax,
			$tax_args
		);

		if (!$pa_terms) {
			$pa_terms = [false];
		}
	}

	foreach ($pa_args as $cat_id => $args) {

		wc_get_template('loop/loop-filter-start.php', $args);

		foreach ($pa_terms as $pa_term) {

			$query_args = array(
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'tax_query'			=> array(
					array(
						'taxonomy'		=> 'product_cat',
						'field'			=> 'ID',
						'terms'			=> array((int) $cat_id),
						'operator'		=> 'IN',
					)
				),
				'orderby'		=> $product_order
			);

			if (current_user_can('manage_woocommerce')) {
				$query_args['post_status'] = array('publish', 'private');
			}

			if (!empty($pa_term)) {
				$query_args['tax_query'][] = array(
					'taxonomy'			=> $tax,
					'field'				=> 'slug',
					'terms'				=> array($pa_term->slug),
					'operator'			=> 'IN',
				);
			}

			$query = new WP_Query($query_args);

			if ($query->have_posts()) :

				if ($pa_term) {
					$head_tag = (isset($args['hide_title'])) ? 'h2' : 'h3';
					echo sprintf(
						'<div class="block wp-block un-top"><%s class="color-2" data-value="%s">%s</%s></div>' . "\n",
						$head_tag,
						$pa_term->slug,
						$pa_term->name,
						$head_tag
					);
				}
				wc_get_template('loop/loop-start.php');
				while ($query->have_posts()) : $query->the_post();
					wc_get_template('content-product.php');
				endwhile;
				wc_get_template('loop/loop-end.php');
			endif;
			wp_reset_query();
		}

		wc_get_template('loop/loop-filter-end.php');
	}
}

/**
 * Вывод сгруппированных товаров по заданным условиям в единую плитку
 *
 * @param array $pa_args
 * @param string|array $tax
 * @param boolean $filter
 * @param string $tax_order
 * @param array $product_order
 * @return void
 */
function pillars_wc_get_categories_list_filter_by_grid($pa_args = [], $tax = '', $filter = true, $tax_order = null, $product_order = ['menu_order' => 'ASC'])
{
	if (empty($pa_args)) {
		return;
	}

	if (isset($pa_args['link'])) {
		$link = $pa_args['link'];
		unset($pa_args['link']);
	} else {
		$link = false;
	}

	if (isset($pa_args['single']) && $pa_args['single'] == true) {
		$single = true;
		unset($pa_args['single']);
	} else {
		$single = false;
	}

	foreach ($pa_args as $cat_id => $args) {
		$pa_args[$cat_id]['slug'] = '';
		$pa_args[$cat_id]['name'] = '';

		if (absint($cat_id)) {
			$term = get_term($cat_id, 'product_cat');
			if ($term instanceof WP_Term) {
				$pa_args[$cat_id]['slug'] = $term->slug;
				$pa_args[$cat_id]['name'] = $term->name;
				$pa_args[$cat_id]['link'] = get_term_link($term, 'product_cat');
			}
		}
	}

	if ($filter) {
		wc_get_template('loop/loop-filter-ajax.php', array_merge($pa_args, ['link' => $link]));
	}

	if ($single) {
		foreach ($pa_args as $cat_id => $args) {
			if ($cat_id != '0') {
				unset($pa_args[$cat_id]);
			}
		}
		$term = get_term($link, 'product_cat');
		if ($term instanceof WP_Term) {
			$pa_args[$link]['slug'] = $term->slug;
			$pa_args[$link]['name'] = $term->name;
		}
	}
	unset($pa_args['0']);

	$pa_terms = [false];
	if (!empty($tax)) {
		$tax_args = array(
			'hide_empty' => !current_user_can('manage_woocommerce'),
		);
		if ($tax_order) {
			$tax_args['orderby'] = $tax_order;
		}
		$pa_terms = get_terms(
			$tax,
			$tax_args
		);

		if (!$pa_terms) {
			$pa_terms = [false];
		}
	}

	foreach ($pa_args as $cat_id => $args) {

		wc_get_template('loop/loop-filter-start.php', $args);
		wc_get_template('loop/loop-start.php');

		foreach ($pa_terms as $pa_term) {

			$query_args = array(
				'post_type'			=> 'product',
				'post_status'		=> 'publish',
				'posts_per_page'	=> -1,
				'tax_query'			=> array(
					array(
						'taxonomy'		=> 'product_cat',
						'field'			=> 'ID',
						'terms'			=> array((int) $cat_id),
						'operator'		=> 'IN',
					)
				),
				'orderby'		=> $product_order
			);

			if (current_user_can('manage_woocommerce')) {
				$query_args['post_status'] = array('publish', 'private');
			}

			if (!empty($pa_term)) {
				$query_args['tax_query'][] = array(
					'taxonomy'			=> $tax,
					'field'				=> 'slug',
					'terms'				=> array($pa_term->slug),
					'operator'			=> 'IN',
				);
			}

			if ($filter) {
				switch ($cat_id) {
					case 344:
						$exclude = array(839);
						break;
					case 839:
						$exclude = array(342, 463);
						break;
					case 463:
						$exclude = array(342, 344, 839);
						break;
					default:
						$exclude = array();
						break;
				}

				if ($exclude) {
					$query_args['tax_query'][] = array(
						'taxonomy'			=> 'product_cat',
						'field'				=> 'ID',
						'terms'				=> $exclude,
						'operator'			=> 'NOT IN',
					);
				}
			}

			$query = new WP_Query($query_args);

			if ($query->have_posts()) :
				while ($query->have_posts()) : $query->the_post();
					wc_get_template('content-product.php');
				endwhile;
			endif;

			wp_reset_query();
		}

		wc_get_template('loop/loop-end.php');
		wc_get_template('loop/loop-filter-end.php');
	}
}


/**
 * Получение данных для табов переданной категории товаров
 *
 * @param integer $term_id
 * @return array
 */
function pillars_wc_get_categories_tabs($term_id = 0)
{
	if (!$term_id)
		return array();

	$term_childs = get_term_children($term_id, 'product_cat');
	$args = array(
		'0' => [
			'title'	=> get_term_meta($term_id, '_pillars_tab_title_general', true),
			'short'	=> 'Все'
		],
	);
	foreach ($term_childs as $id) {
		if (get_term_meta($id, '_pillars_tab_title', true) == 'yes') {
			$args[$id] = array(
				'title'		=> get_term_meta($id, '_pillars_tab_title_long', true),
				'short'		=> get_term_meta($id, '_pillars_tab_title_short', true),
				'redirect'	=> get_term_meta($id, '_pillars_tab_title_redirect', true),
			);
		}
	}

	return $args;
}
