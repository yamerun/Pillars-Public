<?php

defined('ABSPATH') || exit;

// Убедимся что события нет, прежде чем регистрировать новую cron задачу.
if (!wp_next_scheduled('theplugin_update_indexing_wc_product_event')) {
	wp_schedule_event(time(), 'daily', 'theplugin_update_indexing_wc_product_event');
}

// добавляем функцию к указанному хуку
add_action('theplugin_update_indexing_wc_product_event', 'theplugin_update_indexing_wc_product_action');
function theplugin_update_indexing_wc_product_action()
{
	theplugin_wc_product_set_indexing_by_title();
}
