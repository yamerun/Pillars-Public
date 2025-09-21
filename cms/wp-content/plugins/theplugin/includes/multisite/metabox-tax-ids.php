<?php

defined('ABSPATH') || exit;

add_filter('manage_edit-product_cat_columns',	'theplugin_manage_edit_tax_multi_ids_columns');
add_filter('manage_product_cat_custom_column',	'theplugin_manage_tax_multi_ids_custom_column', 25, 3);

$attr_taxs = theplugin_multisite_get_wc_attribute_taxonomies(['attribute_name']);
foreach ($attr_taxs as $attr) {
	$taxname = 'pa_' . $attr['attribute_name'];
	// Tax ID
	add_filter("manage_edit-{$taxname}_columns",	'theplugin_manage_edit_tax_id_columns');
	add_filter("manage_{$taxname}_custom_column",	'theplugin_manage_tax_id_custom_column', 25, 3);
	add_filter("manage_edit-{$taxname}_sortable_columns", 'theplugin_manage_tax_id_custom_sortable');

	// Multi IDs
	add_filter("manage_edit-{$taxname}_columns",	'theplugin_manage_edit_tax_multi_ids_columns');
	add_filter("manage_{$taxname}_custom_column",	'theplugin_manage_tax_multi_ids_custom_column', 25, 3);
}

/**
 * Колонка «Multi IDs» отвечает за вывод IDs по мультисайту
 *
 * @param [type] $columns
 * @return array
 */
function theplugin_manage_edit_tax_id_columns($columns)
{
	$columns['tax_id'] = '<span title="ID">ID</span>';

	return $columns;
}

/**
 * Вывод значений кастомных полей категорий товаров в таблице
 *
 * @param [type] $out
 * @param [type] $column_name
 * @param [type] $term_id
 * @return string
 */
function theplugin_manage_tax_id_custom_column($out, $column_name, $term_id)
{
	if ($column_name === 'tax_id') {
		$out .= '#' . $term_id;
	}

	return $out;
}

function theplugin_manage_tax_id_custom_sortable($columns)
{
	$columns['tax_id'] = ['term_id', false];
	return $columns;
}

/**
 * Колонка «Multi IDs» отвечает за вывод IDs по мультисайту
 *
 * @param [type] $columns
 * @return array
 */
function theplugin_manage_edit_tax_multi_ids_columns($columns)
{
	$columns['multi_ids'] = '<span title="Multi IDs">I</span>';

	return $columns;
}

/**
 * Вывод значений кастомных полей категорий товаров в таблице
 *
 * @param [type] $out
 * @param [type] $column_name
 * @param [type] $term_id
 * @return string
 */
function theplugin_manage_tax_multi_ids_custom_column($out, $column_name, $term_id)
{
	if ($column_name === 'multi_ids') {
		$multi = get_term_meta($term_id, '_multisite_term_ids', true);
		if (!$multi) {
			$out .= '–';
		} else {
			$ids = [];
			foreach ($multi as $blog => $id) {
				$ids[] = $blog . ' – #' . $id;
			}
			$out .= sprintf(
				'<span title="%s">#</span>',
				esc_attr(join(', ', $ids)),
			);
		}
	}

	return $out;
}
