<?php

defined('ABSPATH') || exit;

// Убедимся что события нет, прежде чем регистрировать новую cron задачу.
if (!wp_next_scheduled('theplugin_update_published_date_posts_cron_event')) {
	wp_schedule_event(time(), 'oncequarter', 'theplugin_update_published_date_posts_cron_event');
}

// добавляем функцию к указанному хуку
add_action('theplugin_update_published_date_posts_cron_event', 'theplugin_update_published_date_posts_cron_action');
function theplugin_update_published_date_posts_cron_action()
{
	$log = 'logs/cron/update-published-date-cron.log';

	if (get_current_blog_id() === 1) {
		theplugin_update_published_date_posts();
	}
}

/**
 * Автоматическое обновление `Last-Modified` у опубликованных постов на один час вперёд от прошлой даты
 *
 * @return array
 */
function theplugin_update_published_date_posts($post_types = ['post'])
{
	$logs	= [];
	$today	= wp_date('Y-m-d H:i:s');
	$gmt	= absint(get_option('gmt_offset'));

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
		$sql[] = $wpdb->prepare(
			"SELECT ID, post_date, post_date_gmt, post_modified FROM $table WHERE post_status = %s AND post_date < %s AND ",
			'publish',
			theplugin_strtotime_date($today . ' -1 days', 'Y-m-d 00:00:00')
		);
		$sql[] = '(' . join(' OR ', $post_types) . ')';
		$sql[] = "ORDER BY post_date ASC";
		$sql = join(' ', $sql);
		$result = $wpdb->get_results($sql);

		if ($result) {
			foreach ($result as $item) {
				$published = theplugin_strtotime_date($item->post_date . ' +2 months', 'fd');
				// Если обновлённая дата публикации позже текущего дня
				if ($published > $today) {
					// Задаём сдвиг на 2 дня назад
					$published = theplugin_strtotime_date($today . ' -2 days', 'Y-m-d') . theplugin_strtotime_date($item->post_date, ' H:i:s');
				}

				$item->post_date		= $published;
				$item->post_date_gmt	= theplugin_strtotime_date($item->post_date . ' -' . $gmt . 'hours', 'fd');

				$logs[$prefix][$item->ID] = $wpdb->update(
					$table,
					[
						'post_date'		=> $item->post_date,
						'post_date_gmt'	=> $item->post_date_gmt,
					],
					['ID' => $item->ID]
				);

				// Если дата публикации позже даты редактирования
				if ($published >= $item->post_modified) {
					// Задаём сдвиг на 1 день вперёд от даты публикации
					$item->post_modified = theplugin_strtotime_date($published . ' +1day') . theplugin_strtotime_date($item->post_modified, ' H:i:s');
					$logs['modified'][$prefix][$item->ID] = $wpdb->update(
						$table,
						[
							'post_modified'		=> $item->post_modified,
							'post_modified_gmt'	=> theplugin_strtotime_date($item->post_modified . ' -' . $gmt . 'hours', 'fd'),
						],
						['ID' => $item->ID]
					);
				}
			}
		}
	}

	return $logs;
}
