<?php

defined('ABSPATH') || exit;

add_filter('register_post_type_args', 'pillars_filter_register_post_type_args', 10, 2);

function pillars_filter_register_post_type_args($args, $post_type)
{
	if ('post' == $post_type) {
		$args['show_in_rest'] = false;
	}

	return $args;
}
