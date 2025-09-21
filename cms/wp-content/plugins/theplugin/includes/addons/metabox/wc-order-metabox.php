<?php

defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'theplugin_wc_order_meta_box');
function theplugin_wc_order_meta_box()
{
	add_meta_box('wc_order_meta', 'Допол. данные', 'theplugin_wc_order_meta_add', ['shop_order'], 'side');
}

/**
 * Вывод мета-данных WC Order в мета-боксе 'Допол. данные'
 *
 * @param [type] $post
 * @return string
 */
function theplugin_wc_order_meta_add($post)
{
	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_wc_order_company_card',
		'input_id' 		=> 'pillars_wc_order_company_card_field',
		'input_type' 	=> 'link',
		'label' 		=> 'Карточка предприятия'
	]);
}
