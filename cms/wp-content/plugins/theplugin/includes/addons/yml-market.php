<?php

defined('ABSPATH') || exit;

class THEPLUGIN_YML_Feed
{
	/**
	 * Список категорий товаров
	 *
	 * @var array
	 */
	protected $product_cats = [];

	public function get_shop()
	{
		$wrapper = [];
		$wrapper[] = '<?xml version="1.0" encoding="UTF-8"?>';
		$wrapper[] = '<yml_catalog date="' . wp_date('Y-m-d') . 'T' . wp_date('H:i:s+03:00') . '">';
		$wrapper[] = "\t" . '<shop>';

		$tags = array_merge(
			self::get_shop_header(),
			self::get_wc_categories_feed(),
			self::get_offers_feed(),
		);

		foreach ($tags as $tag) {
			$wrapper[] = "\t" . $tag;
		}

		$wrapper[] = "\t" . '</shop>';
		$wrapper[] = '</yml_catalog>';

		return $wrapper;
	}

	/**
	 * Формирование yml-тегов магазина
	 *
	 * @return array
	 */
	private function get_shop_header()
	{
		$wrapper = [
			'<name>Pillars</name>',
			'<company>ООО РК «ПИЛЛАРС плюс»</company>',
			'<url>' . get_bloginfo('url') . '</url>',
			'<platform>WordPress</platform>',
			'<version>' . get_bloginfo('version') . '</version>',
			'<currencies>',
			"\t" . '<currency id="RUR" rate="1"/>',
			'</currencies>'
		];

		return $wrapper;
	}

	/**
	 * Формирование списка yml-тегов категорий товаров
	 *
	 * @return array
	 */
	public function get_wc_categories_feed()
	{
		$data = self::get_wc_categories();
		$wrapper = [];

		if ($data) {
			$wrapper[] = '<categories>';
			foreach ($data as $item) {
				$wrapper[] = "\t" . sprintf(
					'<category id="%d"%s>%s</category>',
					$item['id'],
					($item['parent']) ? ' parentId="' . $item['parent'] . '"' : '',
					$item['name'],
				);
			}
			$wrapper[] = '</categories>';
		}

		return $wrapper;
	}

	/**
	 * Формирование списка yml-тегов товаров
	 *
	 * @return array
	 */
	public function get_offers_feed()
	{
		$offers		= self::get_offers();
		$wrapper	= [];

		if ($offers) {
			$wrapper[] = '<offers>';
			foreach ($offers as $product_id => $offer) {

				// Првоерка существования цен на товар
				$price = self::get_offer_price($offer);

				if ($price) {
					$wrapper[] = "\t" . sprintf('<offer id="%d"%s>', $product_id, ($offer['_stock_status'] == 'instock') ? ' available="true"' : '',);
					$wrapper[] = "\t\t" . sprintf('<name>%s</name>', $offer['name']);
					$wrapper[] = "\t\t" . sprintf('<description><![CDATA[%s]]></description>', $offer['description']);
					foreach (self::get_offer_pictures($offer) as $item) {
						$wrapper[] = "\t\t" . $item;
					}

					foreach ($price as $item) {
						$wrapper[] = "\t\t" . $item;
					}

					$wrapper[] = "\t\t" . sprintf('<url>%s</url>', get_permalink($product_id));
					$wrapper[] = "\t\t" . sprintf('<categoryId>%d</categoryId>', $offer['taxonomy'][0]);
					$wrapper[] = "\t" . '</offer>';
				}
			}
			$wrapper[] = '</offers>';
		}

		return $wrapper;
	}

	/**
	 * Получение списка категорий товаров с глубиной вложения `2`
	 *
	 * @return array
	 */
	private function get_wc_categories($update = false)
	{
		if ($this->product_cats && !$update)
			return $this->product_cats;

		$args = array(
			'taxonomy'		=> 'product_cat',
			'hide_empty'	=> true,
			'parent'		=> 0,
			'exclude'		=> array_merge(get_option('wc_catalog_exclude_category'), get_option('wc_catalog_exclude_category_by_architect')),
		);

		$data = [];
		$categories = get_categories($args);

		if ($categories) {
			foreach ($categories as $term) {
				$data[] = [
					'id'		=> $term->term_id,
					'name'		=> $term->name,
					'parent'	=> $term->parent,
				];

				$children = get_categories(wp_parse_args(['parent' => $term->term_id], $args));
				if ($children) {
					foreach ($children as $_term) {
						$data[] = [
							'id'		=> $_term->term_id,
							'name'		=> $_term->name,
							'parent'	=> $_term->parent,
						];
					}
				}
			}
		}

		$this->product_cats = $data;

		return $data;
	}

	/**
	 * Получение списка меток товаров
	 *
	 * @return array
	 */
	private function get_wc_tags()
	{
		return array(
			273 => 'Цена по запросу',
			379 => 'Скоро в продаже'
		);
	}

	/**
	 * Получение списка продуктов для офферов
	 *
	 * @return array
	 */
	public function get_offers()
	{
		$params = array(
			'post_type'		=> array('product'),
			'post_status'	=> 'publish',
			'tax_query'		=> array(
				'relation' 		=> 'AND',
				array(
					'taxonomy'	=> 'product_cat',
					'field'		=> 'id',
					'terms'		=> array_merge(get_option('wc_catalog_exclude_category'), get_option('wc_catalog_exclude_category_by_architect')),
					'operator' 	=> 'NOT IN',
				),
			),
			'orderby'			=> 'menu_order',
			'order'				=> 'ASC',
			'posts_per_page'	=> -1
		);

		$product_tags	= self::get_wc_tags(); // Цена по запросу, Скоро в продаже
		$categories		= self::get_wc_categories();
		$cat_ids		= [];

		for ($i = 0; $i < count($categories); $i++) {
			$parent = false;
			for ($j = ($i + 1); $j < count($categories); $j++) {
				if ($categories[$i]['id'] == $categories[$j]['parent']) {
					$parent = true;
				}
			}

			if (!$parent) {
				$cat_ids[] = $categories[$i]['id'];
			}
		}

		$query = new WP_Query($params);
		$products = [];

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$products[get_the_ID()]	= [
					'name'			=> apply_filters('the_title', get_the_title()),
					'description'	=> self::formatting_content(apply_filters('the_content', get_the_content()))
				];
			}
		}
		wp_reset_query();

		global $wpdb;
		$table	= $wpdb->prefix . 'postmeta';
		$tax	=  $wpdb->prefix . 'term_relationships';

		$keys = [
			'_stock_status',
			'_product_attributes',
			'_thumbnail_id',
			'_product_image_gallery',
			'_product_video_review',
			'_product_certificate_id',
			'_product_certificate_text',
			'_sku',
			'_regular_price',
			'alter_cover'
		];

		$meta_key = [];
		foreach ($keys as $key) {
			$meta_key[] = $wpdb->prepare("meta_key = %s", $key);
		}

		$post_ids = array_keys($products);
		$i = 0;
		$ids = array_slice($post_ids, $i, 100);
		while ($ids) {

			$children = $wpdb->get_results($wpdb->prepare("SELECT ID, post_parent, post_type FROM $wpdb->posts WHERE post_parent IN (" . join(', ', $post_ids) . ") AND post_type = %s AND post_status = %s ORDER BY post_parent", 'product_variation', 'publish'));
			if ($children) {
				foreach ($children as $child) {
					if (!isset($products[$child->post_parent]['childs'])) {
						$products[$child->post_parent]['childs'] = [];
					}
					if (!in_array($child->ID, $products[$child->post_parent]['childs'])) {
						$products[$child->post_parent]['childs'][] = absint($child->ID);
					}
				}
			}

			$metas = $wpdb->get_results("SELECT * FROM $table WHERE post_id IN (" . join(', ', $post_ids) . ") AND (" . join(' OR ', $meta_key) . ")");
			foreach ($metas as $meta) {
				$products[$meta->post_id][$meta->meta_key] = $meta->meta_value;
			}

			$terms = $wpdb->get_results("SELECT * FROM $tax WHERE object_id IN (" . join(', ', $post_ids) . ") AND term_taxonomy_id IN (" . join(', ', $cat_ids) . ")");
			foreach ($terms as $term) {
				if (!isset($products[$term->object_id]['taxonomy'])) {
					$products[$term->object_id]['taxonomy'] = [];
				}
				if (!in_array($term->term_taxonomy_id, $products[$term->object_id]['taxonomy'])) {
					$products[$term->object_id]['taxonomy'][] = $term->term_taxonomy_id;
				}
			}

			$tags = $wpdb->get_results("SELECT * FROM $tax WHERE object_id IN (" . join(', ', $post_ids) . ") AND term_taxonomy_id IN (" . join(', ', array_keys($product_tags)) . ")");
			foreach ($tags as $term) {
				if (!isset($products[$term->object_id]['tags'])) {
					$products[$term->object_id]['tags'] = [];
				}
				if (!in_array($term->term_taxonomy_id, $products[$term->object_id]['tags'])) {
					$products[$term->object_id]['tags'][] = $term->term_taxonomy_id;
				}
			}

			$i += 100;
			$ids = array_slice($post_ids, $i, 100);
		}

		return $products;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $content
	 * @return void
	 */
	private function formatting_content($content = '')
	{
		$content = strip_tags($content, '<p><li><br>');
		$content = strtr($content, ['</li>' => '<br>']);
		$content = strip_tags($content, '<p><br>');

		return $content;
	}

	/**
	 * Получение списка `picture` товара
	 *
	 * @param array $data
	 * @return array
	 */
	private function get_offer_pictures($data = [])
	{
		$images = [];
		if (isset($data['alter_cover']) && $data['alter_cover']) {
			$images[] = $data['alter_cover'];
		}
		$images[] = $data['_thumbnail_id'];
		$images = array_merge($images, explode(',', $data['_product_image_gallery']));
		$images = array_unique($images);
		$images = array_slice($images, 0, 3);

		foreach ($images as $i => $image) {
			$images[$i] = sprintf('<picture>%s</picture>', wp_get_attachment_url($image));
		}

		return $images;
	}

	/**
	 * Получение тегов прайса товара
	 *
	 * @param array $data
	 * @return array
	 */
	private function get_offer_price($data = [])
	{
		if (!$data['tags'])
			$data['tags'] = [];

		$tags = array_keys(self::get_wc_tags());
		if (array_intersect($data['tags'], $tags))
			return [];

		$wrapper = ['<currencyId>RUR</currencyId>'];

		if (isset($data['childs'])) {
			global $wpdb;
			$table	= $wpdb->prefix . 'postmeta';
			$price = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE post_id IN (" . join(', ', $data['childs']) . ") AND meta_key = %s ORDER BY meta_value ASC", '_regular_price'));
			if ($price) {
				$wrapper[] = sprintf('<price>%d</price>', $price->meta_value);
			}
		} elseif ($data['_regular_price']) {
			$wrapper[] = sprintf('<price>%d</price>', $data['_regular_price']);
		}

		if (count($wrapper) > 1)
			return $wrapper;

		return [];
	}
}
