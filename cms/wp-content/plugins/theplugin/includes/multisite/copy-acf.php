<?php

defined('ABSPATH') || exit;

add_action('wp', 'theplugin_multisite_copy_acf_to_site_handler', 99);

/**
 * Undocumented function
 *
 * @return void
 */
function theplugin_multisite_copy_acf_to_site_handler()
{
	if (isset($_GET['copy-acf'])) {

		$blog_id = absint($_GET['copy-acf']);
		$results = [];
		if ($blog_id && $blog_id != get_current_blog_id()) {
			$results = theplugin_multisite_copy_acf_to_site($blog_id);
		}
	}
}

/**
 * Undocumented function
 *
 * @param integer $blog_id
 * @return array
 */
function theplugin_multisite_copy_acf_to_site($blog_id = 0)
{
	if (!$blog_id)
		return null;

	global $wpdb;

	$table = $wpdb->prefix . 'posts';

	$esc_like = '%' . $wpdb->esc_like('acf-') . '%';
	$acfs = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $table WHERE post_type LIKE %s", $esc_like));

	$data = [];

	if ($acfs) {
		foreach ($acfs as $acf) {
			$data[$acf->ID] = theplugin_multisite_copy_post_to_site($acf->ID, $blog_id);
		}
	}

	return $data;
}
