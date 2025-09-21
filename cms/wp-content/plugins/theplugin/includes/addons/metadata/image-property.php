<?php

/**
 *  Meta image description
 *
 * Задаём все функции и хуки после зашрузки всех плагинов
 * в частности WooCommerce
 */
add_action('plugins_loaded', function () {

	add_action('wp_head', 'theplugin_woocommerce_meta_desc_image', 99);

	/**
	 * Получение изображения Категории товаров для мета-тегов html-страницы
	 *
	 * @return string
	 */
	function theplugin_woocommerce_meta_desc_image()
	{
		if (is_shop()) {
			$image_id = 1869;
		} else if (is_product_taxonomy()) {
			$term = get_term_by('name', woocommerce_page_title(false), 'product_cat');
			if ($term) {
				$image_id = get_term_meta($term->term_id, 'thumbnail_id', true);
			}
		} else if (has_post_thumbnail(get_the_ID())) {
			$image_id = get_post_thumbnail_id(get_the_ID());
		} else {
			$image_id =  (get_theme_mod('file-background-repost')) ? get_theme_mod('file-background-repost') : '';
		}

		if (!empty($image_id)) {
			$image = wp_get_attachment_image_url($image_id, 'medium');
			foreach (array('image', 'og:image', 'instagram:image', 'twitter:image') as $property) {
				echo "\t<meta property=\"$property\" content=\"$image\" />\n";
			}
		}
	}
});
