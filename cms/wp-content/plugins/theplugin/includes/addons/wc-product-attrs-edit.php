<?php

defined('ABSPATH') || exit;

/**
 * Добавление атрибутов к товару и обновление значений терминов в них
 *
 * @param integer $product_id
 * @param array $taxonomies
 * @param boolean $overwrite флаг сохранения только переданных значений атрибутов
 * @return array|bool
 */
function theplugin_wc_product_add_attributes($product_id = 0, $taxonomies = array(), $overwrite = false)
{
	if (!is_numeric($product_id) || !$taxonomies)
		return false;

	// get an instance of the WC_Product Object
	$product	= wc_get_product($product_id);
	$logs		= array();

	if ($product instanceof WC_Product) {
		foreach ($taxonomies as $taxonomy => $terms) {

			$attributes = (array) $product->get_attributes();

			// Если в товаре есть переданный атрибут, то обновляем его значения
			if (array_key_exists($taxonomy, $attributes)) {
				foreach ($attributes as $key => $attribute) {
					// Если переданный ключ таксономии совпадает с ключом атрибута
					if ($key == $taxonomy) {
						$options = (array) $attribute->get_options();

						// Очищаем значение атрибута, если есть флаг перезаписи
						if ($overwrite) {
							if ($terms) {
								$terms_ids = array_keys($terms);
								foreach ($options as $option_id) {
									if (!in_array($option_id, $terms_ids)) {
										$logs['delete'][$taxonomy][$option_id] = wp_remove_object_terms($product_id, $option_id, $taxonomy);
										unset($options[array_search($option_id, $options)]);
									}
								}
							}
						}

						if ($terms) {
							foreach ($terms as $term_id => $term_slug) {
								// Проверяем наличие id ключа в массиве значений атрибуты
								if (!in_array($term_id, $options)) {
									$options[] = $term_id;
									$logs['update'][$taxonomy][$term_id] = $term_slug;
								}
							}
						}
						$attribute->set_options($options);
						$attributes[$key] = $attribute;
						break;
					}
				}
				$product->set_attributes($attributes);
			}
			// Если атрибута нет, то добавляем его в товар
			else {
				$attribute = new WC_Product_Attribute();

				$attribute->set_id(sizeof($attributes) + 1);
				$attribute->set_name($taxonomy);
				$attribute->set_options(array_keys($terms));
				$attribute->set_position(sizeof($attributes) + 1);
				$attribute->set_visible(true);
				$attribute->set_variation(false);
				$attributes[] = $attribute;

				$product->set_attributes($attributes);

				$logs['insert'][$taxonomy] = $terms;
			}

			$product->save();

			// Проверяем связку терминов таксономии с товаром
			foreach ($terms as $term_id => $term_slug) {
				if (!has_term($term_id, $taxonomy, $product_id)) {
					$logs['terms'][$term_slug] = wp_set_object_terms($product_id, $term_slug, $taxonomy, true);
				}
			}
		}
	}

	return $logs;
}

/**
 * Задание порядка атрибутов товара по переданному массиву ключей таксономии
 *
 * @param integer $product_id
 * @param array $order_keys
 * @return bool|void
 */
function theplugin_wc_product_set_order_attributes($product_id = 0, $order_attrs = array())
{
	$order_keys = array_keys($order_attrs);
	$attrs		= get_post_meta($product_id, '_product_attributes', true);
	$attr_keys	= array_keys($attrs);
	$orders		= [];

	if ($attr_keys && $order_keys) {
		// Задаём порядок тех атрибутов, что были переданы
		foreach ($order_keys as $i => $key) {
			if (isset($attrs[$key])) {
				// Задаём новые значения таксономии атрибута
				$orders[$key] = wp_parse_args($order_attrs[$key], $attrs[$key]);
				$orders[$key]['position'] = $i;
				unset($attr_keys[array_search($key, $attr_keys)]);
			}
		}

		// Если остались атрибута товара вне переданного порядка
		if ($attr_keys) {
			foreach ($attr_keys as $key) {
				$i++;
				$orders[$key] = $attrs[$key];
				$orders[$key]['position'] = $i;
				unset($attr_keys[array_search($key, $attr_keys)]);
			}
		}

		// Если обновление порядка было успешно, то обновляем дату редактирования товара
		if (update_post_meta($product_id, '_product_attributes', $orders) !== false) {
			$product	= wc_get_product($product_id);
			$product->save();

			return true;
		} else {
			return false;
		}
	}

	// Если атрибутов нет у товара и/или для задания порядка
	return null;
}

/**
 * Проверка существования атрибуты товара по имени в таксономии, добавление в случаи отсутствия
 *
 * @param [type] $term_name
 * @param [type] $taxonomy
 * @param bool $insert флаг на добавление флага термина в таксономию, по умолчанию `да`
 * @param string $funcrule
 * @return array|WP_Error|WP_Term
 */
function theplugin_wc_product_has_attribute_by_name($term_name, $taxonomy, $insert = true, $funcrule = 'sanitize_title')
{
	$term_exists = term_exists($term_name, $taxonomy);

	if (!$term_exists) {
		// Проверяем возможно на сохранение
		if ($insert) {
			$term_data	= wp_insert_term($term_name, $taxonomy, array(
				'slug'		=> call_user_func($funcrule, $term_name),
			));
			if (!is_wp_error($term_data)) {
				$term	= get_term_by('id', $term_data['term_id'], $taxonomy);
			} else {
				// Сохраняем данные об ошибки
				$term	= $term_data;
			}
		} else {
			$term = new WP_Error('no exist', $term_name . ' не найден в ' . $taxonomy);
		}
	} else {
		// Получаем объект WP_Term по id
		$term	= get_term_by('id', $term_exists['term_id'], $taxonomy);
	}

	return $term;
}

/**
 * Добавления списка атрибутов товаров, если соответствуют условиями `$funcrule`
 *
 * @param array $product_data
 * @param string $attr_key ключ таксономии
 * @param string $funcrule функция условия формата, если её нет, то вернёт значение атрибутов
 * @return array
 */
function theplugin_wc_product_set_attribute_is_no_exists($product_data = array(), $attr_key = '', $funcrule = '')
{
	$logs = array();
	$attrs = array();
	foreach ($product_data as $product_id => $items) {
		if ($items[$attr_key]) {
			$attrs[$product_id] = trim($items[$attr_key]);
		}
	}

	$attrs = array_unique($attrs);

	if (function_exists($funcrule)) {
		foreach ($attrs as $product_id => $value) {
			$is_rule = call_user_func($funcrule, $value);

			if ($is_rule === true && $value) {
				$logs['insert'][$product_id] = theplugin_wc_product_has_attribute_by_name($value, $attr_key);
				// $logs['insert'][$product_id] = $value;
			} else {
				$logs['error'][$product_id] = $value;
			}
		}

		return $logs;
	}

	return $attrs;
}

/**
 * Обновление данных термина таксономии по заданным условиям `$func_update`
 *
 * @param array|string $taxonomy
 * @param string $func_update
 * @return array
 */
function theplugin_update_terms_by_taxonomy($taxonomy, $func_update = '')
{
	$logs	= array();
	$terms	= get_terms(array(
		'taxonomy'		=> $taxonomy, // название таксономии с WP 4.5
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		'hide_empty'	=> false
	));

	foreach ($terms as $term) {
		if (function_exists($func_update)) {
			$logs[$term->term_id]['args'] = call_user_func($func_update, $term);
			$logs[$term->term_id]['update'] = wp_update_term($term->term_id, $taxonomy, $logs[$term->term_id]['args']);
		}
	}

	return $logs;
}


add_filter('sanitize_title', 'wp_kama_sanitize_title_filter', 9, 3);

/**
 * Function for `sanitize_title` filter-hook.
 *
 * @param string $title     Sanitized title.
 * @param string $raw_title The title prior to sanitization.
 * @param string $context   The context for which the title is being sanitized.
 *
 * @return string
 */
function wp_kama_sanitize_title_filter($title, $raw_title, $context)
{
	$is_float = preg_replace('#[0-9,.]#', '', trim($title));
	if ($is_float === '') {
		return strtr($title, array(
			'.' => '-',
			',' => '-',
		));
	}

	return $title;
}
