<?php

defined('ABSPATH') || exit;

add_filter('theplugin_get_svg_filter', 'pillars_theme_get_svg_list', 10, 2);

/**
 * Undocumented function
 *
 * @param string $key
 * @param string $class
 * @param boolean $symbol
 * @return string
 */
function pillars_theme_get_svg($key = 'empty', $class = '', $symbol = false)
{
	/*
	 * DATA SVG
	 */
	$svg = apply_filters('theplugin_get_svg_filter', array(), $key);

	if (!isset($svg[$key])) {
		$key = 'empty';
		$class = '';
	}

	if (is_null($symbol) && $key == 'empty') {
		return '';
	}

	$svg[$key] = theplugin_replace_tab($svg[$key]);

	if ($symbol) {
		return sprintf('<symbol id="%s" %s</symbol>' . PHP_EOL, $key, $svg[$key]);
	} else {
		return sprintf(
			'<svg role="img" fill="none" class="%s" data-key="%s" %s</svg>',
			$class,
			$key,
			$svg[$key]
		);
	}
}

/**
 * Undocumented function
 *
 * @param string $key
 * @param string $class
 * @return string
 */
function pillars_theme_get_svg_symbol($key = 'empty', $class = '')
{
	$code = pillars_theme_get_svg($key, $class);
	$code = preg_replace(array('/width=\"(\d+)\"/i', '/height=\"(\d+)\"/i'), ['', ''], $code);
	preg_match('/viewbox=\"(.+)\"/isU', $code, $viewbox);
	$viewbox = $viewbox[0];
	$path = mb_stristr($code, 'viewBox');
	$code = str_replace($path, '', $code);

	return sprintf(
		'%s %s><use href="#%s"></use></svg>',
		$code,
		$viewbox,
		$key
	);
}

function pillars_theme_get_svg_list($svg = array(), $key = '')
{
	if (defined('PILLARS_DIR')) {
		switch ($key) {
			case 'header-catalog':
			case 'search-product':
			case 'my-account':
			case 'shopping-cart':
			case 'video-play':
				require get_template_directory() . '/includes/svgdata/header.php';
				break;
			case 'logo':
				require get_template_directory() . '/includes/svgdata/logo.php';
				break;
			default:
				if (strpos($key, 'social-icon-') !== false) {
					require get_template_directory() . '/includes/svgdata/social-icons.php';
				}
				if (strpos($key, 'advantage-') !== false) {
					require get_template_directory() . '/includes/svgdata/content.php';
				}
				break;
		}
	}

	return $svg;
}
