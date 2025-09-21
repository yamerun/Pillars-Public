<?php

defined('ABSPATH') || exit;

/**
 * Test shortcode for display style notification
 */
add_shortcode('tp-notice', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'type'		=> 'info',
			'title'		=> 'Заголовок сообщения',
			'message'	=> '<p>Текст сообщения</p>',
			'class'		=> ''
		), $params);

		return theplugin_get_notice_wrapper($atts);
	}
});
