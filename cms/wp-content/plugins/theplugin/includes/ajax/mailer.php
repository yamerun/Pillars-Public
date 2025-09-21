<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class THEPLUGIN_AJAX_mailer extends THEPLUGIN_AJAX_Handler
{
	function callback()
	{
		/* Check existence post request --> begin */
		if ($_POST) :

			$security = $this->security(array(
				'type'				=> 'form-type',
				'verify_key' 		=> '',
				'verify_action' 	=> '',
				'recaptcha' 		=> 'g-recaptcha-response'
			));

			/**
			 * Add security for spam begin
			 */
			if ($security['verify']) :

				$type = $security['type'];
				$informer = '';

				switch ($type) {
					case 'feedback':
						$subject = 'Сообщение с сайта';
						$informer = '<p>Спасибо за сообщение! Наши менеджеры в ближайшее время прочтут ваше сообщение!</p>';
						$post_keys 	= array(
							'feedback-person' 		=> ['Ваше имя', 'name', true],
							'feedback-email' 		=> ['E-mail', 'email', true],
							'feedback-subject'		=> ['Тема', 'text', false],
							'feedback-message' 		=> ['Сообщение', 'text', false],
							'privacy-confirm' 		=> ['Политика конфиденциальности', 'text', true]
						);
						break;
					default:
						$post_keys = array();
						break;
				}

				$form = $_POST;
				unset($form['action']);
				$data = new THEPLUGIN_Data_Validation_Form($post_keys, $form);

				if (!$data->errors()) {

					$form 	= $data->get_valid_data();

					switch ($type) {
						case 'feedback':
							$args_mail['mail_author']	= $form['feedback-person'];
							$args_mail['mail_address']	= $form['feedback-email'];
							break;
						default:
							$output = '';
							break;
					}

					$msg 	= theplugin_get_form_data_wrapper($form, $post_keys);

					$args_mail['mail_message']['wrapper'] = $msg;
					$args_mail['mail_type'] = $type;

					$msg .= '<p>Send: ' . current_time('D, M d, Y H:i') . ' site time</p>';

					$go_to_send = true;

					if ($go_to_send) {
						if (!theplugin_send_mail($this->get_manager_email($type), $subject, $msg)) {
							$args_mail['status'] = 'nosend';
							$go_to_send = false;
						}
					}

					$this->save_to_db($args_mail);

					if ($go_to_send) {
						$this->get_message_info(array('message' => ['title' => 'Сообщение успешно отправлено', 'content' => $informer]));
					} else {
						$this->get_message_warning(array('message' => ['title' => 'Ошибка отправка формы', 'content' => '<p>Пожалуйста, обновите страницу и попробуйте ещё раз отправить сообщение.</p>'], 'type' => 'fail'));
					}
				} else {
					/* Ошибки в данных */
					$this->get_message_error($data->errors());
				}

			/* Send data --> end */

			else : /* Add security for spam else */
				$this->get_message_spam();
			endif; /* Add security for spam end */

		/* Check existence post request --> end */
		endif;

		wp_die();
	}

	/**
	 * Проверяем существования почтовых адресов
	 */
	private function get_manager_email($type = '')
	{

		// Если почтовых адресов нет, то передаем email администратора сайта
		if (empty(theplugin_get_theme_mod('manager_mail_' . $type))) {
			$mails = get_option('admin_email');
		} else {
			$mails = theplugin_get_theme_mod('manager_mail_' . $type);
		}

		return $mails;
	}
}

new THEPLUGIN_AJAX_mailer('theplugin_mailer');
