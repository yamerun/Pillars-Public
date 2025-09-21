<?php

defined('ABSPATH') || exit;

/**
 * Вывод файла темы по заданным параметрам
 */
add_shortcode('tp-get-part', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'type'		=> 'section',	// папка внутри темы
			'part'		=> '',			// имя файл для вывода
			'path'		=> '',			// по умолчанию файлы из активной темы
			'args'		=> ''			// обработку строки взять из кода шорткода `tp-get-popup-form`
		), $params);

		if (!$atts['type'] || !$atts['part']) {
			return '';
		}

		$path			= $atts['type'] . '/' . $atts['part'];
		$wrapper = '';

		switch ($atts['path']) {
			case 'wc':
				if (function_exists('wc_get_template')) {
					$wrapper	= theplugin_get_template_wc_part_return($path . '.php', theplugin_maybe_args($atts['args']));
				}
				break;
			case 'theplugin':
				$wrapper	= theplugin_get_template_part_return('template-parts/' . $path, null, theplugin_maybe_args($atts['args']));
				break;
			default:
				$wrapper	= theplugin_get_template_theme_part_return('template-parts/' . $path, null, theplugin_maybe_args($atts['args']));
				break;
		}

		return $wrapper;
	}
});
