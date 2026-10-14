<?php

defined('ABSPATH') || exit;

class THEPLUGIN_AJAX_cookie extends THEPLUGIN_AJAX_Handler
{
	function callback()
	{
		/* Check existence post request --> begin */
		if ($_POST) :

			$referer = $_POST['_wp_http_referer'] ?? '';

			$security = $this->security(array(
				'type'				=> 'form-type',
				'verify_key' 		=> 'cookie_verify_key',
				'verify_action' 	=> 'cookie_verify_action',
				'recaptcha' 		=> 'g-recaptcha-response'
			));

			/**
			 * Add security for spam begin
			 */
			if ($security['verify']) :

				$type = $security['type'];

				if ($type) {
					$val  = 'ok for ' . date('Y-m-d', time() + 365 * 86400);
					$time = time() + 365 * 86400;

					$args = [
						'create_time'		=> wp_date('Y-m-d H:i:s'),
						'create_time_gmt'	=> wp_date('Y-m-d H:i:s', null, new DateTimeZone('UTC')),
						'php_session'		=> $_COOKIE['PHPSESSID'] ?? null,
						'agent_ip'			=> $_SERVER['REMOTE_ADDR'] ?? null,
						'agent_hash'		=> md5(serialize(pillars_get_user_server_data())),
						'blog_id'			=> get_current_blog_id(),
						'referer'			=> esc_url($referer)
					];

					global $wpdb;
					$table = theplugin_multisite_get_blog_prefix(1) . 'theplugin_cookie_agree';
					$wpdb->insert($table, $args);

					if ($wpdb->insert_id && !is_wp_error($wpdb->insert_id)) {
						parent::get_message_info(array('message' => $val));
					} else {
						parent::get_message_warning(array('message' => 'Ошибка создания cookie-файла. Пожалуйста, обновите страницу и попробуйте ещё раз отправить сообщение.', 'type' => 'fail'));
					}
				} else {
					/* Ошибки в данных */
					parent::get_message_error('Нет типа данных для создания cookie-файла.');
				}

			/* Send data --> end */

			else : /* Add security for spam else */
				parent::get_message_spam();
			endif; /* Add security for spam end */

		/* Check existence post request --> end */
		endif;

		wp_die();
	}
}

new THEPLUGIN_AJAX_cookie('theplugin_cookie_agree');
