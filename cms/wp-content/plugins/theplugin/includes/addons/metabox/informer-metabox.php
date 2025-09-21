<?php

defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'theplugin_informer_meta_box');
function theplugin_informer_meta_box()
{
	add_meta_box('informer_meta', 'Дополнительные данные', 'theplugin_informer_meta_add', ['informer'], 'side');
}

function theplugin_informer_meta_add($post)
{

	// Используем nonce для верификации
	wp_nonce_field('theplugin_post_meta_action', 'theplugin_post_meta_noncename');

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_informer_link',
		'input_id' 		=> 'pillars_informer_link_field',
		'input_type'	=> 'url',
		'label' 		=> 'Ссылка'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_informer_link_text',
		'input_id' 		=> 'pillars_informer_link_text_field',
		'input_type'	=> 'text',
		'label' 		=> 'Ссылка текст'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_informer_content_view',
		'input_id' 		=> 'pillars_informer_content_view_field',
		'input_type' 	=> 'checkbox',
		'input_default' => 'yes',
		'label' 		=> 'Показывать контент'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_informer_content_align',
		'input_id' 		=> 'pillars_informer_content_align_field',
		'input_type' 	=> 'select',
		'input_default' => [
			'flex-start_txt-left' 	=> 'Верх слева', 	'flex-start_txt-center' => 'Верх центр',	'flex-start_txt-right' 	=> 'Верх справа',
			'center_txt-left' 		=> 'Центр слева',	'center_txt-center' 	=> 'Центр центр', 	'center_txt-right' 		=> 'Центр справа',
			'flex-end_txt-left' 	=> 'Низ слева', 	'flex-end_txt-center' 	=> 'Низ центр',	'flex-end_txt-right' 		=> 'Низ справа'
		],
		'label' 		=> 'Выравнивание контента'
	]);
}

## Сохраняем данные, когда пост сохраняется
add_action('save_post', 'theplugin_save_informer_postdata');
function theplugin_save_informer_postdata($post_id)
{
	$data = array(
		'pillars_informer_link_field' 			=> '_informer_link',
		'pillars_informer_link_text_field' 		=> '_informer_link_text',
		'pillars_informer_content_view_field' 	=> '_informer_content_view',
		'pillars_informer_content_align_field' 	=> '_informer_content_align'
	);

	theplugin_save_postdata($post_id, 'informer', $data);
}