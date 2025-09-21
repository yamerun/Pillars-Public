<?php

defined('ABSPATH') || exit;

// Убедимся что события нет, прежде чем регистрировать новую cron задачу.
if (!wp_next_scheduled('theplugin_update_modified_date_posts_cron_event')) {
	wp_schedule_event(time(), 'weekly', 'theplugin_update_modified_date_posts_cron_event');
}

// добавляем функцию к указанному хуку
add_action('theplugin_update_modified_date_posts_cron_event', 'theplugin_update_modified_date_posts_cron_action');
function theplugin_update_modified_date_posts_cron_action()
{
	$log = 'logs/cron/update-modified-date-cron.log';

	if (get_current_blog_id() === 1) {
		theplugin_update_modified_date_posts();
	}
}

/**
 * Автоматическое обновление `Last-Modified` у опубликованных постов на один час вперёд от прошлой даты
 *
 * @return array
 */
function theplugin_update_modified_date_posts($post_types = ['post', 'page', 'product', 'portfolio'])
{
	$logs = [];

	global $wpdb;

	$sites = [1 => $wpdb->prefix];
	if (is_multisite()) {
		$public_sites = get_sites(['public' => 1]);
		foreach ($public_sites as $i => $site) {
			$sites[absint($site->blog_id)] = theplugin_multisite_get_blog_prefix(absint($site->blog_id));
		}
	}

	foreach ($post_types as $i => $type) {
		$post_types[$i] = $wpdb->prepare("post_type = %s", $type);
	}

	foreach ($sites as $blog_id => $prefix) {
		$table = $prefix . 'posts';
		$sql = [];
		$sql[] = $wpdb->prepare("SELECT ID, post_modified, post_modified_gmt FROM $table WHERE post_status = %s AND", 'publish');
		$sql[] = '(' . join(' OR ', $post_types) . ')';
		$sql[] = "ORDER BY post_modified ASC";
		$sql = join(' ', $sql);
		$result = $wpdb->get_results($sql);

		if ($result) {
			foreach ($result as $item) {
				$template 	= theplugin_multisite_post_get_meta($blog_id, $item->ID, '_wp_page_template');
				if (!is_wp_error($template) && file_exists(get_stylesheet_directory() . '/' . $template)) {
					// Получаем дату изменения шаблона темы и сравниваем с последним изменением
					$temp_time = filemtime(get_stylesheet_directory() . '/' . $template);
					if (wp_date('Y-m-d H:i:s', $temp_time) > $item->post_modified) {
						$item->post_modified	= wp_date('Y-m-d H:i:s', $temp_time);
						$item->post_modified_gmt	= wp_date('Y-m-d H:i:s', $temp_time, new DateTimeZone('UTC'));
					}
				}

				$logs[$prefix][$item->ID] = $wpdb->update(
					$table,
					[
						'post_modified'		=> theplugin_strtotime_date($item->post_modified . ' +1 hour', 'Y-m-d H:i:s'),
						'post_modified_gmt'	=> theplugin_strtotime_date($item->post_modified_gmt . ' +1 hour', 'Y-m-d H:i:s'),
					],
					['ID' => $item->ID]
				);
			}
		}
	}

	return $logs;
}
