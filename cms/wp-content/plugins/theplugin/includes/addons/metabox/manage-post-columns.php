<?php

defined('ABSPATH') || exit;

/**
 * Колонка «Изменено» отвечает за вывод даты редактирования и автора изменения
 *
 * @param array $columns
 * @return array
 */
function theplugin_manage_post_posts_columns_modified($columns)
{
	$columns['date_edit'] = '<span title="Изменено">E</span>';

	return $columns;
}

function theplugin_manage_post_posts_custom_column_modified($column_name)
{
	if ($column_name === 'date_edit') {
		echo sprintf(
			'<span title="%s %s">#</span>',
			get_post_modified_time('d.m.Y в H:i'),
			get_the_modified_author()
		);
	}
}

// добавляем возможность сортировать колонку
function theplugin_sortable_column_modified($sortable_columns)
{
	$sortable_columns['date_edit'] = ['modified', true];
	// false = asc (по умолчанию)
	// true  = desc

	return $sortable_columns;
}


/**
 * 
 */
function theplugin_manage_post_posts_columns_menu_order($columns)
{
	$columns['menu_order'] = '#';

	return $columns;
}

function theplugin_manage_post_posts_custom_column_menu_order($column_name)
{
	if ($column_name === 'menu_order') {
		echo sprintf(
			'%s',
			get_post_field('menu_order')
		);
	}
}

// добавляем возможность сортировать колонку
function theplugin_sortable_column_menu_order($sortable_columns)
{
	$sortable_columns['menu_order'] = ['menu_order', false];
	// false = asc (по умолчанию)
	// true  = desc

	return $sortable_columns;
}

/**
 * 
 */
function theplugin_manage_post_posts_columns_thumb($columns)
{
	$my_columns = [
		'thumb' => 'Миниатюра',
	];

	return array_slice($columns, 0, 1) + $my_columns + $columns;
}

function theplugin_manage_post_posts_custom_column_thumb($column_name)
{
	if ($column_name === 'thumb') {
		// Вывод миниатюры
		if (has_post_thumbnail()) { ?>
			<a href="<?= get_edit_post_link(); ?>">
				<?php the_post_thumbnail('thumbnail'); ?>
			</a>
<?php }
	}
}

/**
 * Общая функция для вывода выпадающего списка переданной таксномии и вида постов в админке WP
 * 
 * Based on Display a custom taxonomy dropdown in admin
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 * 
 * @author WP Kama
 * @link https://wp-kama.ru/id_7524/svoi-filtry-v-tablitsah-zapisej-kommenatriev-polzovatelej.html
 *
 * @param string $typenow (global)
 * @param string $post_type
 * @param string $taxonomy
 * @return void
 */
function theplugin_filter_post_type_by_taxonomy($typenow, $post_type, $taxonomy, $orderby = 'name')
{
	if ($typenow == $post_type) {
		$selected		= isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy	= get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all'	=> sprintf('Все %s', $info_taxonomy->label),
			'taxonomy'			=> $taxonomy,
			'name'				=> $taxonomy,
			'orderby'			=> $orderby,
			'selected'			=> $selected,
			'show_count'		=> true,
			'hierarchical'		=> 1
		));
	};
}

/**
 * Обработка запроса для вывода постов по переданному значению проивзольной таксономии в админке WP
 * 
 * Based on Filter posts by taxonomy in admin
 * @author  Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 * 
 * @author WP Kama
 * @link https://wp-kama.ru/id_7524/svoi-filtry-v-tablitsah-zapisej-kommenatriev-polzovatelej.html
 * 
 * @param string $query (global)
 * @param string $post_type
 * @param string $taxonomy
 * @return void
 */
function theplugin_convert_id_to_term_in_query($query, $post_type, $taxonomy)
{
	$cs = function_exists('get_current_screen') ? get_current_screen() : null;

	// убедимся что мы на нужной странице админки
	if (!is_admin() || empty($cs->post_type) || $cs->post_type != $post_type || $cs->id != 'edit-' . $post_type)
		return;

	$q_vars = &$query->query_vars;
	if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}
