<?php

defined('ABSPATH') || exit;

add_shortcode('pillars_svg', 'pillars_shortcode_svg');

/**
 * Получение svg-кода
 *
 * @return string
 */
function pillars_shortcode_svg($params)
{
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'key'		=> '',
			'type'		=> 'symbol',
			'class'		=> ''
		), $params);

		if ($atts['key']) {
			if ($atts['type'] == 'symbol') {
				return pillars_theme_get_svg_symbol($atts['key'], $atts['class']);
			} else {
				return pillars_theme_get_svg($atts['key'], $atts['class']);
			}
		}

		return '';
	}
}
