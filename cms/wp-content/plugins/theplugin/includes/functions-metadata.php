<?php

defined('ABSPATH') || exit;

/**
 *
 */
require THEPLUGIN_DIR . '/includes/addons/metadata/redirects.php';
require THEPLUGIN_DIR . '/includes/addons/metadata/last-modified.php';
require THEPLUGIN_DIR . '/includes/addons/metadata/wp-optimaze.php';
require THEPLUGIN_DIR . '/includes/addons/metadata/image-property.php';
require THEPLUGIN_DIR . '/includes/addons/metadata/wp-mail.php';

/**
 *
 */
require THEPLUGIN_DIR . '/includes/addons/metabox/manage-post-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/manage-metabox.php';

/**
 * Колонки консоли типов записи WP
 */
require THEPLUGIN_DIR . '/includes/addons/metabox/post-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/page-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/informer-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/informer-metabox.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/portfolio-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/wc-product-columns.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/wc-product-metabox.php';
require THEPLUGIN_DIR . '/includes/addons/metabox/wc-order-metabox.php';

// Добавляем стили для зарегистрированных колонок
add_action('admin_print_footer_scripts-edit.php', function () {
?>
	<style>
		.column-id,
		.column-date_edit {
			width: 50px;
		}

		.column-thumb {
			width: 120px;
		}

		.column-thumb img {
			max-width: 100%;
			height: auto;
		}

		.column-menu_order {
			width: 50px !important;
		}
	</style>
<?php
});
