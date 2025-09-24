<?php

/**
 *
 * Plugin Name: The Plugin
 * Description: Функциональное расширение для текущей темы
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: theplugin
 */

defined('ABSPATH') || exit;

if (!defined('THEPLUGIN_DIR')) {
	define('THEPLUGIN_DIR', rtrim(plugin_dir_path(__FILE__), ' /'));
}

if (!defined('THEPLUGIN_URL')) {
	define('THEPLUGIN_URL', rtrim(plugin_dir_url(__FILE__), ' /'));
}

register_activation_hook(__FILE__, 'theplugin_activate');

/**
 * Функция, срабатывающая один раз при активации плагина
 *
 * @return void
 */
function theplugin_activate()
{
	global $wpdb;

	$table = $wpdb->prefix . 'theplugin_options';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table (
		option_id       bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		option_name     varchar(191),
		option_value    longtext,
		PRIMARY KEY (option_id, option_name)
	);");

	$table = $wpdb->prefix . 'theplugin_messages';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table (
		id bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		send_time		datetime DEFAULT '0000-00-00 00:00:00',
		mail_type		varchar(24) NOT NULL,
		mail_author		varchar(128) DEFAULT '–',
		mail_address	varchar(128) NOT NULL,
		mail_phone		varchar(20) DEFAULT '–',
		mail_message	text,
		mail_status		varchar(8) DEFAULT 'send',
		PRIMARY KEY (id)
	);");

	$table = $wpdb->prefix . 'theplugin_redirects';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table (
		redirect_id		bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		create_time		datetime DEFAULT '0000-00-00 00:00:00',
		request_uri_old	text,
		request_uri_new	text,
		request_code	varchar(3) DEFAULT '301',
		PRIMARY KEY (redirect_id)
	);");


	/**
	 * Индексация слов из наименований товаров для поиска
	 */
	$table = $wpdb->prefix . 'wc_search_products';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table (
		id				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		title			varchar(100),
		base_ids		text,
		variation_ids	text,
		date_update		datetime DEFAULT '1970-01-01 00:00:00',
		priority		int(3) UNSIGNED DEFAULT 10,
		is_auto			BOOLEAN DEFAULT 1,
		PRIMARY KEY (id)
	);");

	$table = $wpdb->prefix . 'wc_search_product_synonyms';
	$wpdb->query("CREATE TABLE IF NOT EXISTS $table (
		synonym_id		bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		title_id		bigint(20) UNSIGNED NOT NULL,
		synonym			varchar(100),
		PRIMARY KEY (synonym_id),
		FOREIGN KEY (title_id) REFERENCES wp_wc_search_products (id)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	);");
}

/**
 * Default theme functions
 */
require THEPLUGIN_DIR . '/includes/functions-basic.php';

/**
 * Произвольные типы постов
 */
require THEPLUGIN_DIR . '/includes/functions-custom-post-types.php';

/**
 * Список функций и фильтров для вывода столбцов в списках постов
 * и сохранения произвольнных данных различных видов постов
 */
require THEPLUGIN_DIR . '/includes/functions-metadata.php';


/* STYLES, SCRIPTS & CUSTOMIZE */
require_once THEPLUGIN_DIR . '/includes/theme/scripts-styles.php';

/**
 * Helpers function
 */

require THEPLUGIN_DIR . '/includes/shortcodes.php';
require THEPLUGIN_DIR . '/includes/svg.php'; 			// SVG-code images

/**
 * Список функций и фильтров для вывода настроек плагина в консоли WP
 */
require THEPLUGIN_DIR . '/includes/functions-settings.php';

/**
 * AJAX
 */
require THEPLUGIN_DIR . '/includes/ajax.php';

/**
 * WooCommerce
 */
add_action('plugins_loaded', function () {
	if (class_exists('WooCommerce')) {
		require THEPLUGIN_DIR . '/includes/addons/wc-cart.php';
		require THEPLUGIN_DIR . '/includes/addons/wc-order.php';
		require THEPLUGIN_DIR . '/includes/addons/wc-categories.php';
		require THEPLUGIN_DIR . '/includes/addons/wc-product-attrs-edit.php';
		require THEPLUGIN_DIR . '/includes/addons/wc-product-attrs-get.php';
		require THEPLUGIN_DIR . '/includes/addons/wc-product-search.php';
	}
});

/**
 * Список функций и фильтров для работы с YML-фидом для Яндекс.Маркет
 */
require THEPLUGIN_DIR . '/includes/addons/yml-market.php';

/**
 * Список функций и фильтров для работы WP в режиме мультисайта
 */
require THEPLUGIN_DIR . '/includes/functions-multisite.php';

/**
 * Список функций и фильтров для работы WP в режиме мультисайта
 */
require_once THEPLUGIN_DIR . '/cron/cron-lists.php';
