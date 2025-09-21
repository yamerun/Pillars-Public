<?php

defined('ABSPATH') || exit;

/**
 * В консоли WP добавляем на старницу WC Товара панель "Свойство записи"
 *
 * @param array $args
 * @param string $post_type
 *  @return array
 */
function theplugin_register_post_type_args_product($args, $post_type)
{
	if ('product' == $post_type) {
		array_push($args['supports'], 'page-attributes');
	}
	return $args;
}
add_filter('register_post_type_args', 'theplugin_register_post_type_args_product', 10, 2);


/**
 * Регистрация мета-бокса в WC Product
 *
 * @return void
 */
function theplugin_product_meta_box()
{
	add_meta_box('product_meta', 'Скидка при заказе', 'theplugin_product_meta_add', ['product'], 'side');
}
add_action('add_meta_boxes', 'theplugin_product_meta_box');

/**
 * Вывод мета-данных WC Product в мета-боксе 'Скидка при заказе'
 *
 * @param [type] $post
 * @return string
 */
function theplugin_product_meta_add($post)
{
	// Используем nonce для верификации
	wp_nonce_field('theplugin_post_meta_action', 'theplugin_post_meta_noncename');

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_discounts_count',
		'input_id' 		=> 'pillars_product_discounts_count_field',
		'input_type'	=> 'number',
		'input_default' => 0,
		'label' 		=> 'Количество, от:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_discounts_per',
		'input_id' 		=> 'pillars_product_discounts_per_field',
		'input_type'	=> 'number',
		'input_default' => 0,
		'label' 		=> 'Скидка, %:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_gallery_notice',
		'input_id' 		=> 'pillars_product_gallery_notice_field',
		'label' 		=> 'Инфо для галереи:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_price_notice',
		'input_id' 		=> 'pillars_product_price_notice_field',
		'label' 		=> 'Пояснение стоимости:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_certificate_id',
		'input_id' 		=> 'pillars_product_certificate_id_field',
		'input_type'	=> 'number',
		'input_default' => 0,
		'label' 		=> 'Сертификат ID:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_certificate_text',
		'input_id' 		=> 'pillars_product_certificate_text_field',
		'label' 		=> 'Сертификат описание:'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_in_stock',
		'input_id' 		=> 'pillars_product_in_stock_field',
		'input_type'	=> 'checkbox',
		'input_default' => 'yes',
		'label' 		=> 'В наличии'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_featured',
		'input_id' 		=> 'pillars_product_featured_field',
		'input_type'	=> 'checkbox',
		'input_default' => 'yes',
		'label' 		=> 'Хит продаж'
	]);

	echo theplugin_get_components_panel([
		'post_id' 		=> $post->ID,
		'post_meta' 	=> '_product_module_sys_link',
		'input_id' 		=> 'pillars_product_module_sys_link_field',
		'label' 		=> 'Ссылка на модульную систему:'
	]);

	// TODO добавить поля для `_product_video_review`
}

## Сохраняем данные, когда пост сохраняется
add_action('save_post', 'theplugin_save_product_postdata');
function theplugin_save_product_postdata($post_id)
{
	$data = array(
		'pillars_product_discounts_per_field' 	=> '_product_discounts_per',
		'pillars_product_discounts_count_field'	=> '_product_discounts_count',
		'pillars_product_gallery_notice_field'	=> '_product_gallery_notice',
		'pillars_product_price_notice_field'	=> '_product_price_notice',
		'pillars_product_certificate_id_field'	=> '_product_certificate_id',
		'pillars_product_certificate_text_field' => '_product_certificate_text',
		'pillars_product_in_stock_field'		=> '_product_in_stock',
		'pillars_product_featured_field'		=> '_product_featured',
		'pillars_product_module_sys_link_field'	=> '_product_module_sys_link',
	);

	theplugin_save_postdata($post_id, 'product', $data);
}
