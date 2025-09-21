<?php

defined('ABSPATH') || exit;

add_filter('theplugin_get_svg_symbol_filter', 'theplugin_get_svg_symbol_list', 5, 1);
add_filter('theplugin_get_svg_filter', 'theplugin_get_svg_list', 5, 2);

/**
 * Undocumented function
 *
 * @param string $key
 * @param string $class
 * @param boolean $symbol
 * @return string
 */
function theplugin_get_svg($key = 'empty', $class = '', $symbol = false)
{
	/**
	 * DATA SVG
	 */
	$svg = apply_filters('theplugin_get_svg_filter', array(), $key);

	if (!isset($svg[$key])) {
		$key = 'empty';
		$class = '';
	}

	$svg[$key] = theplugin_replace_tab($svg[$key]);

	if ($symbol) {
		return sprintf('<symbol id="%s" %s</symbol>' . PHP_EOL, $key, $svg[$key]);
	} else {
		return sprintf(
			'<svg role="img" fill="none" class="%s" data-key="%s" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0px" y="0px" %s</svg>',
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
function theplugin_get_svg_symbol($key = 'empty', $class = '')
{
	$code = theplugin_get_svg($key, $class);
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

/**
 * Undocumented function
 *
 * @param array $args
 * @return array
 */
function theplugin_get_svg_symbol_list($args = array())
{
	return $args;
}

/**
 * Undocumented function
 *
 * @param array $svg
 * @param string $key
 * @return array
 */
function theplugin_get_svg_list($svg = array(), $key = '')
{
	switch ($key) {
			/*
		case 'contact-manager':
		case 'contact-phone':
		case 'contact-location':
		case 'contact-whatsapp':
			require THEPLUGIN_DIR . '/includes/svgdata/page-contact.php';
			break;
		*/
		default:
			# code...
			break;
	}

	/**
	 * DEFAULT
	 */
	$svg['empty'] = 'width="36" height="36" viewBox="0 0 38.9 38.9" style="enable-background:new 0 0 38.9 38.9;" xml:space="preserve"><path fill="currentColor" d="M19.4,5c8,0,14.4,6.4,14.4,14.4s-6.4,14.4-14.4,14.4S5,27.4,5,19.4S11.5,5,19.4,5L19.4,5z M19.4,9.4 c-5.6,0-10.1,4.5-10.1,10.1s4.5,10.1,10.1,10.1S29.5,25,29.5,19.4S25,9.4,19.4,9.4z"/>';

	return $svg;
}
