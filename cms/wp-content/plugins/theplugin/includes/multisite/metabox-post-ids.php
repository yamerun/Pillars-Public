<?php

defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'theplugin_multi_ids_meta_box');

foreach (['page', 'post', 'informer', 'portfolio', 'product'] as $posttype) {
	/**
	 * Колоника «Multi IDs»
	 */
	add_filter("manage_{$posttype}_posts_columns", 'theplugin_manage_post_posts_column_multi_ids', 20, 1);
	add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_multi_ids', 20, 2);
}

/**
 * Колонка «Multi IDs» отвечает за вывод IDs по мультисайту
 *
 * @param array $columns
 * @return array
 */
function theplugin_manage_post_posts_column_multi_ids($columns)
{
	$columns['multi_ids'] = '<span><span class="vers networking" title="Multi IDs" aria-hidden="true"></span><span class="screen-reader-text">Multi IDs</span></span>';

	return $columns;
}

/**
 * Undocumented function
 *
 * @param [type] $column_name
 * @return void
 */
function theplugin_manage_post_posts_custom_column_multi_ids($column_name)
{
	if ($column_name === 'thumb') {
		if (get_post_type() == 'product' && get_current_user_id() == 5) {
			$alter_id = get_post_meta(get_the_ID(), '_pillars_product_image_alter_view', true);
			if ($alter_id && $alter_id != get_post_thumbnail_id()) {
				echo '<p></p>' . wp_get_attachment_image($alter_id);
			}
		}
	}

	if ($column_name === 'multi_ids') {
		$multi = get_post_meta(get_the_ID(), '_multisite_post_ids', true);
		if (!$multi) {
			echo '–';
		} else {
			$ids = [];
			foreach ($multi as $blog => $id) {
				$ids[] = $blog . ' – #' . $id;
			}
			echo sprintf(
				'<span title="%s">#</span>',
				esc_attr(join(', ', $ids)),
			);
		}

		$prices = get_post_meta(get_the_ID(), '_multisite_product_prices', true);
		if ($prices && is_array($prices)) {
			$ids = [];
			foreach ($prices as $blog => $flag) {
				if ($flag == 'yes') {
					$ids[] = $blog . ' – ' . $flag;
				}
			}
			if ($ids) {
				echo sprintf(
					'<span title="%s">#</span>',
					esc_attr(join(', ', $ids)),
				);
			}
		}
	}
}

/**
 * Мета-бокс для ввыода данных по поддоменам
 *
 * @return void
 */
function theplugin_multi_ids_meta_box()
{
	add_meta_box('multi_ids_meta', 'Данные по поддоменам', 'theplugin_multi_ids_meta_add', ['page', 'post', 'informer', 'portfolio', 'product'], 'side');
}

/**
 * Undocumented function
 *
 * @param [type] $post
 * @return void
 */
function theplugin_multi_ids_meta_add($post)
{
	$multi = get_post_meta(get_the_ID(), '_multisite_post_ids', true);
	if (!$multi) {
		$values = '–';
	} else {
		$ids = [];
		foreach ($multi as $blog => $id) {
			$ids[] = sprintf(
				'%s – #' . $id,
				theplugin_multisite_get_site_option(absint(str_replace('blog_', '', $blog)), 'blogname')
			);
		}
		$values = join('<br>', $ids);
	}


	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_multisite_post_ids',
		'input_id' 		=> 'theplugin_multisite_post_ids_field',
		'input_type'	=> 'info',
		'input_value'	=> $values,
		'label' 		=> 'IDs:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_multisite_multisite_post_delete',
		'input_id' 		=> 'pillars_multisite_multisite_post_delete_field',
		'input_type'	=> 'checkbox',
		'input_default' => 'yes',
		'input_value'	=> 'no',
		'label' 		=> 'Удалить связи'
	]);
}

/**
 * Обновляем данные last-modified Главной, когда информер обновляется
 */
add_action('save_post', function ($post_id) {

	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	if (!isset($_POST['theplugin_post_meta_noncename']))
		return;
	if (!wp_verify_nonce($_POST['theplugin_post_meta_noncename'], 'theplugin_post_meta_action'))
		return;

	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id))
		return;

	if (isset($_POST['pillars_multisite_multisite_post_delete_field'])) {

		$delete = sanitize_key($_POST['pillars_multisite_multisite_post_delete_field']);
		if ($delete === 'yes') {
			delete_post_meta($post_id, '_multisite_post_ids');

			if (get_post_type($post_id) == 'product') {
				$variations = get_children([
					'post_parent'	=> $post_id,
					'post_type'		=> 'product_variation',
					'numberposts'	=> -1,
					'post_status'	=> 'any'
				], ARRAY_A);

				if ($variations) {
					foreach ($variations as $id => $product) {
						delete_post_meta($id, '_multisite_post_ids');
					}
				}
			}
		}
	}
}, 99);

// Добавляем стили для зарегистрированных колонок
add_action('admin_head', function () {
?>
	<style>
		.fixed .column-tax_id {
			width: 5rem;
		}

		.fixed .column-multi_ids {
			width: 1rem;
		}
	</style>
<?php
});
