<?php

defined('ABSPATH') || exit;

/**
 * Вывод контаков из theplugin_get_theme_mod
 * по умолчанию номер телефона с ключом `contacts_phone_1`
 */
add_shortcode('tp-get-contact', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'type'		=> 'phone',
			'key'		=> 'contacts_phone_1',
			'wrapper'	=> '',
			'class'		=> '',
			'args'		=> ''	// обработку строки взять из кода шорткода `tp-get-popup-form`
		), $params);

		if (!$atts['type'] || !$atts['key']) {
			return '';
		}

		switch ($atts['type']) {
			case 'phone':
				return theplugin_get_phone_theme_mod($atts['key'], $atts['wrapper'], $atts['class'], theplugin_maybe_args($atts['args']));
				break;
			case 'email':
				return theplugin_get_email_theme_mod($atts['key'], $atts['class'], theplugin_maybe_args($atts['args']));
				break;
			case 'p':
				return sprintf('<p>%s</p>', theplugin_get_theme_mod($atts['key'], ''));
				break;
			case 'raw':
				return theplugin_get_theme_mod($atts['key'], '');
				break;
			default:
				return '';
				break;
		}
	}
});

/**
 * Вывод копирайта из theplugin_get_theme_mod
 */
add_shortcode('tp-get-theme-color', function () {
	if (!is_admin()) {

		$color	= theplugin_get_theme_mod('tabs_theme_color', '');

		if ($color) {
			return '
	<!-- Custom Browsers Color Start -->
	<meta name="theme-color" content="' . $color . '">
	<!-- Custom Browsers Color End -->';
		}
	}
});

/**
 * Вывод ссылок на соцсети
 */
add_shortcode('tp-social-links', function ($params) {
	if (!is_admin()) {

		$atts	= shortcode_atts(array('class' => 'icons-list', 'title' => false), $params);
		$data	= theplugin_get_social_list_by_theme_mod();

		return theplugin_get_social_list($data, $atts);
	}
});

/**
 * Вывод ссылок на соцсети
 */
add_shortcode('tp-social-yandex-map', function ($params) {
	if (!is_admin()) {

		$code = theplugin_get_theme_mod('yandex_map_company');

		if ($code) {
			$widget	= theplugin_yandex_reviews_widget($code);
			return sprintf(
				'<ul class="icons-list"><li><div>%s%s</div></li></ul>',
				theplugin_get_svg_symbol('social-icon-yandex', 'social'),
				$widget
			);
		}

		return '';
	}
});

/**
 * Вывод текста под лого в футоре
 */
add_shortcode('tp-logo-footer-desc', function ($params) {
	if (!is_admin()) {

		$atts	= shortcode_atts(array(), $params);
		$desc	= theplugin_get_theme_mod('logo_footer_desc');

		return apply_filters('the_content', $desc);
	}
});

/**
 * Вывод копирайта из theplugin_get_theme_mod
 */
add_shortcode('tp-get-copyright', function () {
	if (!is_admin()) {

		$copyright	= theplugin_get_theme_mod('information_copyright', '');
		$year		= theplugin_get_theme_mod('information_copyright_year', '');

		if ($copyright) {
			$copyright = sprintf(
				'<p class="copyright__years">%s</p>',
				strtr($copyright, array(
					'SSSS' => $year,
					'YYYY' => current_time('Y')
				))
			);
		}

		$reserved = theplugin_get_theme_mod('information_copyright_reserved', '');
		if ($reserved) {
			$reserved = sprintf('<p class="copyright__reserved">%s</p>', $reserved);
		}

		return $copyright . $reserved;
	}
});

/**
 * Вывод обратной навигации
 */
add_shortcode('tp-breadcrumbs', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'separator'		=> ' / ',
			'args'			=> ''
		), $params);

		$breadcrumb = theplugin_get_breadcrumb(false, theplugin_maybe_args($atts['args']), $atts['separator']);

		if (class_exists('WooCommerce')) {
			if (is_woocommerce()) {
				ob_start();
				do_action('theplugin_woocommerce_breadcrumb');
				$wc_breadcrumb = ob_get_clean();

				if ($wc_breadcrumb) {
					$breadcrumb = $wc_breadcrumb;
				}
			}
		}

		return $breadcrumb;
	}
});

/**
 * Вывод пагинации
 */
add_shortcode('tp-pagination', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(), $params);

		ob_start();
		theplugin_get_pagination();
		$pagination = ob_get_clean();

		return sprintf('<nav class="pagination">%s</nav>', $pagination);
	}
});

/**
 * Вывод iframe для VK Видео
 */
add_shortcode('vk-video', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array('url' => ''), $params);

		if (strpos($atts['url'], 'vk.com/video-') !== false) {
			$url = str_replace('&autoplay=1', '', theplugin_get_video_embed_link($atts['url']));

			return sprintf(
				'<iframe src="%s" title="VK video player" width="853" height="480" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen></iframe>',
				$url
			);
		}
	}
});
