<?php

add_action('admin_notices', 'true_custom_notice');

function true_custom_notice()
{

	if (
		isset($_GET['page'])
		&& 'true_slider' == $_GET['page']
		&& isset($_GET['settings-updated'])
		&& true == $_GET['settings-updated']
	) {
		echo '<div class="notice notice-success is-dismissible"><p>Слайдер сохранён!</p></div>';
	}
}
