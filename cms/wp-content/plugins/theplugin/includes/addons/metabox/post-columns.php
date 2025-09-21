<?php

defined('ABSPATH') || exit;

$posttype	= 'post';

/**
 * Колоника «Миниатюра»
 */
add_filter("manage_{$posttype}_posts_columns", 'theplugin_manage_post_posts_columns_thumb', 20, 1);
add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_thumb', 20, 2);

/**
 * Колоника «Изменено»
 */
add_filter("manage_{$posttype}_posts_columns", 'theplugin_manage_post_posts_columns_modified', 20, 1);
add_action("manage_{$posttype}_posts_custom_column", 'theplugin_manage_post_posts_custom_column_modified', 20, 2);
add_filter("manage_edit-{$posttype}_sortable_columns", 'theplugin_sortable_column_modified');

// Добавляем стили для зарегистрированных колонок
add_action('admin_print_footer_scripts-edit.php', function () {
?>
	<style>
		.fixed .column-author,
		.fixed .column-categories,
		.fixed .column-tags {
			width: 8%;
		}
	</style>
<?php
});
