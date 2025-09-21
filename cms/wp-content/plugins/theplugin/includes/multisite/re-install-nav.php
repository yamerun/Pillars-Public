<?php

defined('ABSPATH') || exit;

add_action('wp', 'theplugin_multisite_reinstall_navs', 99);

function theplugin_multisite_reinstall_navs($blog_id = 0)
{
	if (isset($_GET['re-install-nav'])) {

		$blog_id = absint($_GET['re-install-nav']);
		$blog_id = ($blog_id) ? $blog_id : get_current_blog_id();

		$navs = get_terms([
			'taxonomy'		=> 'nav_menu',
			'hide_empty'	=> false,
		]);

		if ($navs) {

			$data = [];

			require_once ABSPATH . '/wp-admin/includes/taxonomy.php';

			foreach ($navs as $term) {

				$data[$term->term_id] = theplugin_multisite_copy_term_to_site($term->term_id, $blog_id);

				$items = get_posts(
					array(
						'post_type'		=> 'nav_menu_item',
						'post_status'	=> 'publish',
						'posts_per_page' => -1,
						'tax_query'		=> array(
							array(
								'taxonomy'	=> 'nav_menu',
								'field'		=> 'term_taxonomy_id',
								'terms'		=> $term->term_id,
							)
						)
					)
				);


				$menu_items = [];
				if ($items) {
					foreach ($items as $item) {
						$menu_items[$item->ID] = theplugin_multisite_copy_post_to_site($item->ID, $blog_id);
					}
				}
			}
		}

		$mods = get_option('theme_mods_pillars');
		if ($mods) {
			foreach ($mods['nav_menu_locations'] as $key => $term_id) {
				$ids = get_term_meta($term_id, '_multisite_term_ids', true);
				$mods['nav_menu_locations'][$key] = absint($ids['blog_' . $blog_id]);
			}

			switch_to_blog($blog_id);
			update_option('theme_mods_pillars', $mods);
			restore_current_blog();
		}

		switch_to_blog($blog_id);
		$navs = get_terms([
			'taxonomy' => 'nav_menu',
			'hide_empty' => false,
		]);
		if ($navs) {
			foreach ($navs as $term) {
				// wp_delete_term($term->term_id, $term->taxonomy);
			}
		}
		restore_current_blog();
	}
}
