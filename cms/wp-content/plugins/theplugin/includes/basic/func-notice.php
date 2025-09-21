<?php

/**
 * Remove function getJSONmessage
 * Old function from getJSONmessageContent
 */
function theplugin_get_json_message($args = [], $unicode = true)
{

	$defaults = array(
		'type' 		=> 'empty',
		'message'	=> '',
		'content' 	=> '',
		'console'	=> ''
	);

	$args 	= wp_parse_args($args, $defaults);
	$flags 	= ($unicode) ? JSON_UNESCAPED_UNICODE : 0;

	return json_encode($args, $flags);
}


/**
 * Вывод html-обёртки нотисов от плагина
 * Old function from getStyleMessage
 *
 * @param array $args keys: `type`, `title`,`message`, `console`, `class`
 * @return string
 */
function theplugin_get_notice_wrapper($args = [])
{

	$defaults = array(
		'type' 		=> '',
		'title'		=> '',
		'message' 	=> '',
		'console'	=> '',
		'class'		=> '',
		'icon'		=> ''
	);

	$args = wp_parse_args($args, $defaults);

	switch ($args['type']) {
		case 'success':
			$args['icon'] = '<i class="fa fa-check-circle"></i>';
			break;
		case 'warning':
			$args['icon'] = '<i class="fa fa-exclamation-triangle"></i>';
			break;
		case 'error':
			$args['icon'] = '<i class="fa fa-exclamation-circle"></i>';
			break;
		default:
			$args['icon'] = '<i class="fa fa-info"></i>';
			break;
	}

	$notice = theplugin_get_template_part_return('templates/notice', null, $args);

	if ($notice)
		return $notice;

	return sprintf(
		'<div class="tp-get-notice %s %s"><div class="icon">%s</div><div class="title">%s</div>%s</div>%s',
		$args['type'],
		$args['class'],
		$args['icon'],
		$args['title'],
		$args['message'],
		$args['console']
	);
}
