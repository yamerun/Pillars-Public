<?php

defined('ABSPATH') || exit;

/**
 * Произвольные типы записи informer – Информер на Главной
 */
require THEPLUGIN_DIR . '/includes/addons/custom-post-types/post.php';

/**
 * Произвольные типы записи informer – Информер на Главной
 */
require THEPLUGIN_DIR . '/includes/addons/custom-post-types/informer.php';

/**
 * Произвольные типы записи portfolio – Портфолио
 */
require THEPLUGIN_DIR . '/includes/addons/custom-post-types/portfolio.php';

// * Использовать для обновление правила вывода по url произвольных типов постов
/*
add_action('init', function () {
	flush_rewrite_rules();
});
*/