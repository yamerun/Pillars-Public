<?php

defined('ABSPATH') || exit;

/**
 * Индексирование наименований товара для поиска
 *
 * @return array
 */
function theplugin_wc_product_set_indexing_by_title()
{
	/**
	 * Преобразование многомерного массива категорий в одномерный
	 *
	 * @param array $cats
	 * @return array
	 */
	function category_ids($cats = [])
	{
		$items = [];
		foreach ($cats as $cat_id => $data) {
			if ($data['childs']) {
				$childs = category_ids($data['childs']);
				foreach ($childs as $_cat_id => $title) {
					$items[$_cat_id] = $title;
				}
			} else {
				$items[$cat_id] = $data['title'];
			}
		}

		return $items;
	}

	$results = [];

	// Получаем список категорий товара с вложенностью дочерний категорий
	$categories = theplugin_get_categories_list(array(
		'taxonomy'	=> 'product_cat',
		'exclude'	=> get_option('wc_catalog_exclude_category'),
	));

	$categories = category_ids($categories);
	$order_ids = [];

	// Получения списка продуктов по каждой категории в единый массив
	foreach ($categories as $cat_id => $title) {
		$products = get_posts([
			'post_type'			=> 'product',
			'post_status'		=> 'publish',
			'tax_query'		=> array(
				'relation'		=> 'AND',
				array(
					'taxonomy'	=> 'product_cat',
					'field'		=> 'id',
					'terms'		=> $cat_id,
					'operator'	=> 'IN',
				),
			),
			'orderby'			=> ['menu_order' => 'ASC'],
			'posts_per_page'	=> -1
		]);

		if ($products) {
			foreach ($products as $product) {
				$product		= wc_get_product($product->ID);
				$order_ids[]	= $product->get_id();

				$results[$product->get_id()] = [
					'id'		=> $product->get_id(),
					'title'		=> $product->get_name(),
					'parent'	=> 0,
				];

				if ($product->is_type('variable')) {
					foreach ($product->get_children() as $id) {
						if (get_post_status($id) == 'publish') {
							$order_ids[]	= $id;
							$results[$id]		= [
								'id'		=> $id,
								'title'		=> get_the_title($id),
								'parent'	=> $product->get_id(),
							];
						}
					}
				}
			}
		}

		wp_reset_query();
	}

	// Сохраняем уникальные ID
	$order_ids = array_unique($order_ids);

	$words = [];
	if ($results) {
		foreach ($results as $product) {
			$strings = explode(' ', $product['title']);
			foreach ($strings as $string) {
				$string = trim(mb_strtolower(html_entity_decode($string)), '".,«»');
				if (mb_strlen($string) > 2) {

					// Фильтр слов нетребующих индексацию
					// TODO перевести всё в фильтр
					if (in_array($string, ['derevo-listvenniczy', 'для', 'тест'])) {
						continue;
					}

					if (!isset($words[$string])) {
						$words[$string] = [
							'base'		=> [],
							'variation'	=> []
						];
					}

					// $words[$string][] = $product['id'];
					if ($product['parent']) {
						$words[$string]['variation'][] = absint($product['id']);
					} else {
						$words[$string]['base'][] = absint($product['id']);
					}
				}
			}
		}
	}

	// Сортировка по алфавиту всех доступных слов
	ksort($words);

	$logs = [];

	if ($words) {
		global $wpdb;
		$table = $wpdb->prefix . 'wc_search_products';

		foreach ($words as $word => $ids) {
			// Формируем порядок приоритетности ID товаров согласно порядку категорий
			foreach ($ids as $type => $items) {
				$ordering = [];
				foreach ($order_ids as $order_id) {
					if (in_array($order_id, $items)) {
						$ordering[] = $order_id;
					}
				}
				$ids[$type] = theplugin_json_encode($ordering);
			}

			$exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE title = %s", $word));
			if ($exists) {
				if ($exists->is_auto && ($exists->base_ids != $ids['base'] || $exists->variation_ids != $ids['variation'])) {
					$logs[$word] = $wpdb->update(
						$table,
						[
							'base_ids'		=> $ids['base'],
							'variation_ids'	=> $ids['variation'],
							'date_update'	=> wp_date('Y-m-d H:i:s')
						],
						[
							'title'			=> $word,
						]
					);
				}
			} else {
				$insert = $wpdb->insert(
					$table,
					[
						'title'			=> $word,
						'base_ids'		=> $ids['base'],
						'variation_ids'	=> $ids['variation'],
						'date_update'	=> wp_date('Y-m-d H:i:s')
					]
				);
				$logs[$word] = ($insert) ? $wpdb->insert_id : false;
			}
		}
	}

	return $logs;
}

/**
 * Поиск товаров по индексированным наименованиям
 *
 * @param string $search
 * @return array
 */
function theplugin_wc_product_search_ids_by_indexing($search = '', $tags = ['', '%'])
{
	if (!$search)
		return [];

	global $wpdb;
	$table = $wpdb->prefix . 'wc_search_products';

	$search = explode(' ', $search);
	$data = [];
	foreach ($search as $string) {
		$string = trim(mb_strtolower($string), '".,');
		if (mb_strlen($string) > 2) {

			$data[$string] = [
				'base'		=> [],
				'variation'	=> [],
			];
			$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE title LIKE %s", $tags[0] . $wpdb->esc_like($string) . $tags[1]), ARRAY_A);

			if (!$result) {
				$table = $wpdb->prefix . 'wc_search_product_synonyms';
				$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE synonym LIKE %s", $tags[0] . $wpdb->esc_like($string) . $tags[1]));

				if ($result) {
					$table = $wpdb->prefix . 'wc_search_products';
					$result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $result->title_id), ARRAY_A);
				}
			}

			if ($result) {
				foreach ($result as $item) {
					// Проходим по типам товаров
					foreach ($data[$string] as $key => $items) {
						// Проверяем наличие ID товаров в результате поиска
						$ids = theplugin_maybe_array($item[$key . '_ids']);
						if ($ids) {
							$data[$string][$key] += $ids;
						}
					}
				}
			}

			$data[$string] = [
				'base'		=> array_unique($data[$string]['base']),
				'variation'	=> array_unique($data[$string]['variation']),
			];
		}
	}

	// Формируем первичный массив результатов поиска
	$intersect = array_shift($data);
	// Флаги на использование прошлых результатов поиска
	$is_maybe = [
		'base'		=> false,
		'variation'	=> false,
	];
	// Если ещё данные результатов поиска, то проверяем схождение с первичным массивом
	if ($data) {
		$maybe = [
			'base'		=> [],
			'variation'	=> [],
		];

		foreach ($data as $string => $items) {
			// Проходим по типам товаров
			foreach ($maybe as $key => $values) {
				$maybe[$key] = $intersect[$key];
				$intersect[$key] = array_intersect($intersect[$key], $items[$key]);
				// Если схождений нет, то используем прошлый результат и задаём соответствующий флаг
				if (!$intersect[$key]) {
					$intersect[$key] = array_merge($maybe[$key], $items[$key]);
					$is_maybe[$key] = true;
				}
			}
		}
	}

	// Задаём значение флагов прошлых результатов в итоговый массив
	$intersect['maybe'] = $is_maybe;

	return $intersect;
}

/**
 * Добавление синонимов к индексации наименований товаров
 *
 * @param [type] $title_id
 * @param [type] $synonym
 * @return int|bool
 */
function theplugin_wc_product_set_synonym_by_title($title_id, $synonym)
{
	global $wpdb;
	$table = $wpdb->prefix . 'wc_search_product_synonyms';
	$synonym = trim(mb_strtolower(html_entity_decode($synonym)), '".,«»');

	$wpdb->insert($table, [
		'title_id'	=> absint($title_id),
		'synonym'	=> $synonym
	]);

	return $wpdb->insert_id;
}
