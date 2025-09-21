<?php

defined('ABSPATH') || exit;

/**
 * Публиковать записи на всех сайтах в мультисайте WordPress
 * @source https://rudrastyh.com/wordpress-multisite/post-to-all-sites.html
 *
 *
 * Копирование или перемещение записей в мультисайте WordPress
 * @source https://rudrastyh.com/wordpress-multisite/copy-pages-between-sites.html
 *
 *
 * Добавление в метабокс функционала прикрепления файлов
 * @source https://sawtech.ru/tehno-blog/rabota-s-mediabibliotekoj-wordpress/
 *
 *
 * Валюта в WooCommerce
 * @source https://misha.agency/woocommerce/valyuta.html
 */

add_filter('upload_dir', 'theplugin_multisite_upload_dir', 999, 1);
function theplugin_multisite_upload_dir($dirs)
{

	$dirs['baseurl'] = network_site_url('/wp-content/uploads');
	$dirs['basedir'] = ABSPATH . 'wp-content/uploads';
	$dirs['path'] = $dirs['basedir'] . $dirs['subdir'];
	$dirs['url'] = $dirs['baseurl'] . $dirs['subdir'];
	return $dirs;
}
