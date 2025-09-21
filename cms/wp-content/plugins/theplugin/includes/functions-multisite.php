<?php

defined('ABSPATH') || exit;

if (is_multisite()) {
	/**
	 *
	 */
	require THEPLUGIN_DIR . '/includes/multisite/media.php';

	/**
	 * Список функций для получения общих данных по мультисайту
	 */
	require THEPLUGIN_DIR . '/includes/multisite/general.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/re-install.php';

	/**
	 * Список функций для обработки мета-данных терминов таксономии
	 */
	require THEPLUGIN_DIR . '/includes/multisite/meta-term.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/copy-post.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/copy-term.php';

	/**
	 * Список функций для дублирование настроек ACF-плагина
	 */
	require THEPLUGIN_DIR . '/includes/multisite/copy-acf.php';

	add_action('plugins_loaded', function () {
		if (class_exists('WooCommerce')) {
			/**
			 * Список функций для дублирование товаров сайта на другой по поддомену
			 */
			require THEPLUGIN_DIR . '/includes/multisite/copy-wc-product.php';

			/**
			 * Список функций для дублирование настроек сайта на другой по поддомену
			 */
			require THEPLUGIN_DIR . '/includes/multisite/copy-wc-attr.php';

			/**
			 * Список функций для дублирование настроек сайта на другой по поддомену
			 */
			require THEPLUGIN_DIR . '/includes/multisite/re-install-wc.php';
		}

		/**
		 * Список функций для дублирование настроек сайта на другой по поддомену
		 */
		require THEPLUGIN_DIR . '/includes/multisite/re-install-seo.php';
	});

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/re-install-nav.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/re-install-other.php';

	/**
	 * Список функций для добавления настроек по склонению городов
	 */
	require THEPLUGIN_DIR . '/includes/multisite/setting-fields.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/seo-filter.php';

	/**
	 * Список функций для дублирование настроек сайта на другой по поддомену
	 */
	require THEPLUGIN_DIR . '/includes/multisite/metabox-post-ids.php';
	require THEPLUGIN_DIR . '/includes/multisite/metabox-tax-ids.php';
	require THEPLUGIN_DIR . '/includes/multisite/metabox-wc-products.php';
}
