<?php

defined('ABSPATH') || exit;

/**
 * Получения списка основных категорий товаров WC в виде массива ключ-наименование-дочерние
 *
 * @return array
 */
function theplugin_wc_get_product_categories_general($metas = array())
{
	return theplugin_get_categories_list(array(
		'taxonomy'		=> 'product_cat',
		'hide_empty'	=> true,
		'parent'		=> 0,
		'exclude'		=> get_option('wc_catalog_exclude_category'),
		'metas'			=> $metas
	));
}

/**
 * Получения списка категорий товаров WC в виде массива ключ-наименование-дочерние
 *
 * @param boolean $exclude флаг на скрытие непубличных категорий
 * @return array
 */
function theplugin_wc_get_product_categories_list($exclude = true)
{
	return theplugin_get_categories_list(array(
		'taxonomy'		=> 'product_cat',
		'hide_empty'	=> true,
		'parent'		=> '',
		'exclude'		=> ($exclude) ? get_option('wc_catalog_exclude_category') : array(),
	));
}

/**
 * Получение html-обёртки списка категорий товаров в виде выпадающего списка
 *
 * @param array $args
 * @return string
 */
function theplugin_wc_get_product_categories_list_select_wrapper($args = [])
{
	$defaults = array(
		'exclude'	=> true,
		'id'		=> 'product_cat',
		'name'		=> 'product_cat',
		'class'		=> 'product_cat',
		'required'	=> true
	);

	$args		= wp_parse_args($args, $defaults);
	$categories	= theplugin_wc_get_product_categories_list($args['exclude']);

	return sprintf(
		'<select id="%s" name="%s" class="%s"%s><option value="">%s</option>%s</select>',
		$args['id'],
		$args['name'],
		$args['class'],
		($args['required']) ? ' required=""' : '',
		__('Выберете категорию'),
		theplugin_get_categories_list_wrapper_by_options($categories)
	);
}
