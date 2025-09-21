<?php

defined('ABSPATH') || exit;

if (wp_doing_ajax()) {

	if (isset($_POST['action'])) {
		switch (sanitize_text_field($_POST['action'])) {
				/*
			case 'theplugin_cookie_agree':
				require_once THEPLUGIN_DIR . '/includes/ajax/cookie.php';
				break;
			*/
			case 'tp_nonce_update':
				require_once THEPLUGIN_DIR . '/includes/ajax/nonces.php';
				break;
			case 'theplugin_getform':
				require_once THEPLUGIN_DIR . '/includes/ajax/getform.php';
				break;
			case 'theplugin_mailer':
				require_once THEPLUGIN_DIR . '/includes/ajax/mailer.php';
				break;
			default:
				break;
		}
	}
}
