<?php

defined('ABSPATH') || exit;

add_filter('wp_mail_from', 'theplugin_change_feedback_email');
add_filter('wp_mail_from_name', 'theplugin_change_feedback_email_name');

add_action('phpmailer_init', 'theplugin_smtp_enable');
add_action('wp_mail_failed', 'theplugin_mail_failed');

/**
 * TODO: предусмотреть возможность редактирования данных через плагин
 */

/**
 * Смена обратного адреса письма при отправки через wp_mail
 *
 * @param [type] $email
 * @return string
 */
function theplugin_change_feedback_email($email)
{
	return 'info@pillars.ru';
}

/**
 * Смена обратного имени отправителя письма при отправки через wp_mail
 *
 * @param [type] $name
 * @return string
 */
function theplugin_change_feedback_email_name($name)
{
	return get_bloginfo('name');
}

/**
 * Конфигурируем подключение SMTP
 *
 * @param [type] $phpmailer
 * @return void
 */
function theplugin_smtp_enable($phpmailer)
{
	$settings = array(
		'host'		=> '',
		'port'		=> '',
		'username'	=> '',
		'password'	=> '',
		'from' 		=> '',
		'fromname'	=> get_bloginfo('name')
	);

	if (theplugin_get_theme_mod('wp_mail_smpt_auth')) {

		$apply = true;

		foreach ($settings as $key => $value) {
			$set = theplugin_get_theme_mod('wp_mail_smpt_' . $key);
			$settings[$key] = ($set) ? $set : $value;
			if (!$settings[$key]) {
				$apply = false;
				break;
			}
		}

		if ($apply) {

			$phpmailer->isSMTP();
			$phpmailer->SMTPAuth	= true;
			$phpmailer->Host		= $settings['host'];
			$phpmailer->Port		= $settings['port'];
			$phpmailer->Username	= $settings['username'];
			$phpmailer->Password	= $settings['password'];
			// $phpmailer->SMTPSecure	= 'tls';
			// $phpmailer->SMTPSecure	= $phpmailer::ENCRYPTION_SMTPS;
			$phpmailer->From		= $settings['from'];
			$phpmailer->FromName	= $settings['fromname'];
			// $phpmailer->Sender		= '';
		}
	}
}

/**
 * Логирование ошибок при отправке через `wp_mail`
 *
 * @param [type] $wp_error
 * @return void
 */
function theplugin_mail_failed($wp_error)
{
}
