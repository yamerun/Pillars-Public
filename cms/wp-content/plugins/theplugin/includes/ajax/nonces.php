<?php

defined('ABSPATH') || exit;

class THEPLUGIN_Nonce_Update extends THEPLUGIN_AJAX_Handler
{
	function callback()
	{
		/* Check existence post request --> begin */
		if ($_POST) {
			foreach ($_POST as $key => $value) {
				if (strpos($key, '_verify_key') !== false) {
					$verify_key		= $key;
					$verify_action	= str_replace('_verify_key', '_verify_action', $key);
					break;
				}
			}

			if (isset($verify_key) && isset($verify_action)) {
				if ($_POST[$verify_key] && wp_verify_nonce($_POST[$verify_key], $verify_action) == 1) {
					echo theplugin_json_encode(array('result' => ''));
				} else {
					echo theplugin_json_encode(array('result' => wp_create_nonce($verify_action)));
				}
			}
		}	/* Check existence post request --> end */

		wp_die();
	}
}

new THEPLUGIN_Nonce_Update('tp_nonce_update');
