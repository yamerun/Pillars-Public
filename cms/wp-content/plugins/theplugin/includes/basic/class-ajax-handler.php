<?php

abstract class THEPLUGIN_AJAX_Handler
{

	function __construct($action_name)
	{
		$this->init_hooks($action_name);
	}

	public function init_hooks($action_name)
	{
		add_action('wp_ajax_' . $action_name, 			array($this, 'callback'));
		add_action('wp_ajax_nopriv_' . $action_name, 	array($this, 'callback_nopriv'));
	}

	public function security($args = [])
	{

		if ($_POST) {

			$error 		= '';
			$defaults 	= array(
				'type'				=> '',
				'verify_key' 		=> '',
				'verify_action' 	=> '',
				'recaptcha' 		=> ''
			);

			$args = wp_parse_args($args, $defaults);

			$type = (isset($_POST[$args['type']])) ? sanitize_text_field($_POST[$args['type']]) : '';
			if ($type != '') {
				unset($_POST[$args['type']]);

				$verify_key 	= (empty($args['verify_key'])) 		? $type . '_verify_key' 	: $args['verify_key'];
				$verify_action 	= (empty($args['verify_action']))	? $type . '_verify_action' 	: $args['verify_action'];

				/* Check wp nonce */
				$security = false;
				if (isset($_POST[$verify_key]) && sanitize_text_field($_POST[$verify_key]) != '') {
					if (wp_verify_nonce($_POST[$verify_key], $verify_action) == 1) {
						$security = true;
					} else {
						$error = 'form_verify';
					}
					unset($_POST[$verify_key]);
					unset($_POST['_wp_http_referer']);
				}

				/* Check Google ReCaptcha v3.0 */
				unset($_POST[$args['recaptcha']]);
			} else {
				$error 		= 'No type form';
				$security 	= false;
			}

			return ['verify' => $security, 'error' => $error, 'type' => $type];
		} else {
			return ['verify' => false, 'error' => 'No exist request', 'type' => ''];
		}
	}

	public function get_message_success($args = [])
	{

		$defaults = array(
			'message' 	=> '',
			'type'		=> 'ok',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'success'
		);

		$args = wp_parse_args($args, $defaults);

		$this->get_message_wrapper_json($args);
	}

	public function get_message_info($args = [])
	{

		$defaults = array(
			'message' 	=> '',
			'type'		=> 'ok',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'info'
		);

		$args = wp_parse_args($args, $defaults);

		$this->get_message_wrapper_json($args);
	}

	public function get_message_warning($args = [])
	{

		$defaults = array(
			'message' 	=> '',
			'type'		=> 'ok',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'warning'
		);

		$args = wp_parse_args($args, $defaults);

		$this->get_message_wrapper_json($args);
	}

	public function get_message_error($message = [])
	{

		$args = array(
			'message' 	=> $message,
			'type'		=> 'error',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'error',
			'class'		=> 'content'
		);

		$this->get_message_wrapper_json($args);
	}

	public function get_message_spam($message = [])
	{

		$args = array(
			'message' 	=> [
				'title' 	=> 'Session expired',
				'content' 	=> '<p>Please refresh the page.</p>'
			],
			'type'		=> 'spam',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'warning'
		);

		$args['message'] = wp_parse_args($message, $args['message']);

		$this->get_message_wrapper_json($args);
	}

	private function get_message_wrapper_json($args = [])
	{

		$defaults = array(
			'message' 	=> '',
			'type'		=> 'ok',
			'content'	=> '',
			'console'	=> '',
			'notice'	=> 'info'
		);

		$args = wp_parse_args($args, $defaults);

		if (is_array($args['message'])) {
			$args['message'] = theplugin_get_notice_wrapper(array(
				'type' 		=> $args['notice'],
				'title' 	=> $args['message']['title'],
				'message' 	=> $args['message']['content'],
				'class'		=> (isset($args['message']['class'])) ? $args['message']['class'] : ''
			));
		}
		echo theplugin_get_json_message($args);
		wp_die();
	}

	/**
	 * Сохранение данных сообщения в БД `theplugin_messages`
	 *
	 * @param array $args
	 * @return int|bool
	 */
	public function save_to_db($args = [])
	{
		$defaults = array(
			'send_time'		=> current_time('Y-m-d H:i:s'),
			'mail_type'		=> '',
			'mail_author'	=> '–',
			'mail_address'	=> '–',
			'mail_phone'	=> '–',
			'mail_message'	=> array(),
			'mail_status'	=> 'send'
		);

		$args = wp_parse_args($args, $defaults);

		// Если вдруг нет обратного адреса, то сохраняем данные по браузеру и IP отправителя
		if (empty($args['mail_address'])) {
			$args['mail_message'][] = $_SERVER['REMOTE_ADDR'];
			$args['mail_message'][] = $_SERVER['HTTP_USER_AGENT'];
			$args['mail_address'] = 'empty@whois.com';
		}

		if (empty($args['mail_type'])) {
			$args['mail_type'] = 'empty';
		}

		if (empty($args['mail_phone'])) {
			$args['mail_phone'] = '–';
		}

		$args['mail_message'] = theplugin_json_encode($args['mail_message']);

		global $wpdb;
		$table  = $wpdb->prefix . 'theplugin_messages';

		$wpdb->insert($table, $args);

		if ($wpdb->insert_id) {
			return $wpdb->insert_id;
		}

		// Если при записи в БД произошла ошибка, то сохраняем в лог
		$args['errors'] = $wpdb->insert_id;
		return false;
	}

	public function callback_nopriv()
	{
		$this->callback();
	}

	abstract public function callback();
}
