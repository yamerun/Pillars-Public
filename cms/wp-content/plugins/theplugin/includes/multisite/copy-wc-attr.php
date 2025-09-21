<?php

defined('ABSPATH') || exit;

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @param integer $attr_id
 * @return int|bool
 */
function theplugin_multisite_reinstall_wc_create_attribute($blog_id = 0, $attr_id = 0)
{
	$blog_id = absint($blog_id);
	$attr_id = absint($attr_id);
	$current_blog_id = get_current_blog_id();

	$attr = wc_get_attribute($attr_id);
	$attr_id = false;

	if ($blog_id && $attr) {

		$args = theplugin_object_to_array($attr);

		$attr_ids = get_site_meta($current_blog_id, 'wc_attribute_taxonomies', true);

		$args['id'] = 0;
		if (isset($attr_ids[$attr->slug]['blog_' . $blog_id])) {
			$args['id'] = $attr_ids[$attr->slug]['blog_' . $blog_id];
		} else {
			$attr_ids = get_site_meta($blog_id, 'wc_attribute_taxonomies', true);
			if (isset($attr_ids[$attr->slug]['blog_' . $blog_id])) {
				$args['id'] = $attr_ids[$attr->slug]['blog_' . $blog_id];
			}
		}

		// Переключаем ID сайта на переданный блог
		switch_to_blog($blog_id);

		/**
		 *
		 * Create attribute
		 * @source https://wp-kama.ru/plugin/woocommerce/function/wc_create_attribute
		 *
		 */

		$attr_id	= $args['id'];
		$data		= array(
			'attribute_label'	=> $args['name'],
			'attribute_name'	=> preg_replace('/^pa\_/', '', $args['slug']),
			'attribute_type'	=> $args['type'],
			'attribute_orderby'	=> $args['order_by'],
			'attribute_public'	=> $args['has_archives']
		);

		global $wpdb;
		$format = array('%s', '%s', '%s', '%s', '%d');

		if (0 === $attr_id) {
			$results = $wpdb->insert(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				$data,
				$format
			);

			if (is_wp_error($results)) {
				$attr_id = new WP_Error('cannot_create_attribute', $results->get_error_message(), array('status' => 400));
			}

			$attr_id = $wpdb->insert_id;

			/**
			 * Attribute added.
			 *
			 * @param int   $attr_id	Added attribute ID.
			 * @param array $data		Attribute data.
			 */
			do_action('woocommerce_attribute_added', $attr_id, $data);
		} else {
			$results = $wpdb->update(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				$data,
				array('attribute_id' => $attr_id),
				$format,
				array('%d')
			);

			if (false === $results) {
				$attr_id = new WP_Error('cannot_update_attribute', __('Could not update the attribute.', 'woocommerce'), array('status' => 400));
			}

			// Set old slug to check for database changes.
			// TODO дополнить возможность смены по old_slug
			$old_slug = !empty($args['old_slug']) ? wc_sanitize_taxonomy_name($args['old_slug']) : $args['slug'];

			/**
			 * Attribute updated.
			 *
			 * @param int		$attr_id	Added attribute ID.
			 * @param array		$data		Attribute data.
			 * @param string	$old_slug	Attribute old name.
			 */
			do_action('woocommerce_attribute_updated', $attr_id, $data, $old_slug);

			if ($old_slug !== $args['slug']) {
				// Update taxonomies in the wp term taxonomy table.
				$wpdb->update(
					$wpdb->term_taxonomy,
					array('taxonomy' => wc_attribute_taxonomy_name($data['attribute_name'])),
					array('taxonomy' => 'pa_' . $old_slug)
				);
			}

			// Update taxonomy ordering term meta.
			$wpdb->update(
				$wpdb->termmeta,
				array('meta_key' => 'order'), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				array('meta_key' => 'order_pa_' . sanitize_title($old_slug)) // WPCS: slow query ok.
			);

			// Update product attributes which use this taxonomy.
			$old_taxonomy_name	= 'pa_' . $old_slug;
			$new_taxonomy_name	= 'pa_' . $data['attribute_name'];
			$old_attribute_key	= sanitize_title($old_taxonomy_name); // @see WC_Product::set_attributes().
			$new_attribute_key	= sanitize_title($new_taxonomy_name); // @see WC_Product::set_attributes().
			$metadatas			= $wpdb->get_results(
				$wpdb->prepare(
					"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_product_attributes' AND meta_value LIKE %s",
					'%' . $wpdb->esc_like($old_taxonomy_name) . '%'
				),
				ARRAY_A
			);

			foreach ($metadatas as $metadata) {
				$product_id			= $metadata['post_id'];
				$unserialized_data	= maybe_unserialize($metadata['meta_value']);

				if (!$unserialized_data || !is_array($unserialized_data) || !isset($unserialized_data[$old_attribute_key])) {
					continue;
				}

				$unserialized_data[$new_attribute_key] = $unserialized_data[$old_attribute_key];
				unset($unserialized_data[$old_attribute_key]);
				$unserialized_data[$new_attribute_key]['name'] = $new_taxonomy_name;
				update_post_meta($product_id, '_product_attributes', wp_slash($unserialized_data));
			}

			// Update variations which use this taxonomy.
			$wpdb->update(
				$wpdb->postmeta,
				array('meta_key' => 'attribute_pa_' . sanitize_title($data['attribute_name'])), // WPCS: slow query ok.
				array('meta_key' => 'attribute_pa_' . sanitize_title($old_slug)) // WPCS: slow query ok.
			);
		}

		// Clear cache and flush rewrite rules.
		wp_schedule_single_event(time(), 'woocommerce_flush_rewrite_rules');
		delete_transient('wc_attribute_taxonomies');
		WC_Cache_Helper::invalidate_cache_group('woocommerce-attributes');

		restore_current_blog();

		if (!is_wp_error($attr_id)) {
			if (!$attr_ids) {
				$attr_ids = [];
			}
			$attr_ids[$attr->slug]['blog_' . $blog_id] = $attr_id;
			update_site_meta($current_blog_id, 'wc_attribute_taxonomies', $attr_ids);
			update_site_meta($blog_id, 'wc_attribute_taxonomies', $attr_ids);
		}
	}

	return $attr_id;
}
