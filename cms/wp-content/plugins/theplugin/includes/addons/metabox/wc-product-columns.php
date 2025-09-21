<?php

defined('ABSPATH') || exit;

$posttype	= 'product';

/**
 * Колоника «Изменено»
 */
add_filter("manage_{$posttype}_posts_columns", 'theplugin_manage_post_posts_columns_modified', 20, 1);
add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_modified', 20, 2);
add_filter("manage_edit-{$posttype}_sortable_columns", 'theplugin_sortable_column_modified');

/**
 * Колоника «#» для menu_order
 */
add_filter("manage_{$posttype}_posts_columns", 'theplugin_manage_post_posts_columns_menu_order', 20, 1);
add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_menu_order', 20, 2);
add_filter("manage_edit-{$posttype}_sortable_columns", 'theplugin_sortable_column_menu_order');

/**
 * Колонка `SKU`
 */
add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_sku', 20, 2);

/**
 * Undocumented function
 *
 * @param [type] $column_name
 * @return void
 */
function theplugin_manage_post_posts_custom_column_sku($column_name)
{
	if ($column_name === 'sku') {
		global $product;
		if (!$product->get_sku() && $product->is_type('variable')) {
			$blog_id	= get_current_blog_id();
			$variations	= get_children([
				'post_parent'	=> $product->get_id(),
				'post_type'		=> 'product_variation',
				'numberposts'	=> -1,
				'post_status'	=> 'any'
			], ARRAY_A);

			$data = [];

			foreach ($variations as $id => $_product) {
				$_sku = theplugin_multisite_post_get_meta($blog_id, $id, '_sku');
				if (!is_wp_error($_sku)) {
					$data[$id] = $_sku;
				}
			}

			echo '<br>' . implode(', ', $data);
		}
	}
}
