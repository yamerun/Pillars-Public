<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class THEPLUGIN_AJAX_getform extends THEPLUGIN_AJAX_Handler
{
	function callback()
	{

		/* Check existence post request --> begin */
		if ($_POST) :
			if (isset($_POST['get-form'])) {
				$template 	= sanitize_text_field($_POST['get-form']);
				unset($_POST['get-form']);
				if (count($_POST)) {
					foreach ($_POST as $key => $value) {
						$args[$key] = sanitize_text_field($value);
					}
				} else {
					$args = array();
				}
				$wrapper 	= theplugin_get_template_part_return('template-parts/form/form', $template, $args);
				if (!empty($wrapper)) {
					$this->get_message_info(array('message' => $wrapper));
				} else {
					$this->get_message_warning(array('message' => ['title' => 'Ошибка', 'content' => '<p>Не удалось загрузить форму.</p>'], 'type' => 'fail'));
				}
			} else {
				$this->get_message_warning(array('message' => ['title' => 'Ошибка', 'content' => '<p>Нет запроса на загрузку формы.</p>'], 'type' => 'fail'));
			}

		/* Check existence post request --> end */
		endif;

		wp_die();
	}
}

new THEPLUGIN_AJAX_getform('theplugin_getform');
