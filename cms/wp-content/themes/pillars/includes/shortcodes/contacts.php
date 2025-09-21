<?php

defined('ABSPATH') || exit;

/**
 * Вывод контаков из theplugin_get_theme_mod
 * по умолчанию номер телефона с ключом `contacts_phone_1`
 */
add_shortcode('pillars-contact', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'type'		=> 'phone',
			'key'		=> 'contacts_phone_1'
		), $params);

		if (!$atts['type'] || !$atts['key']) {
			return '';
		}

		// TODO перенести в функцию вывод svg-ссылок

		switch ($atts['type']) {
			case 'phone':
				return sprintf(
					'<a class="pillars-popup__btn" href="tel:%s" data-id="recall" data-form="form-recall" data-form_args="%s"><strong>%s</strong><span>%s</span></a>',
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '" wrapper="link"]'),
					theplugin_array_to_args(['page_id' => get_the_ID()]),
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '" type="raw"]'),
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '_descript" type="raw"]')
				);
				break;
			case 'phone-raw':
				return sprintf(
					'<a href="tel:%s"><strong>%s</strong><span>%s</span></a>',
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '" wrapper="link"]'),
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '" type="raw"]'),
					do_shortcode('[tp-get-contact key="' . $atts['key'] . '_descript" type="raw"]')
				);
				break;
			case 'email':
				$email = filter_var(theplugin_get_theme_mod($atts['key']));
				if ($email) {
					return sprintf(
						'<a  class="pillars-popup__btn" href="mailto:%s" data-id="feedback" data-form="form-feedback" data-form_args="%s"><strong>%s</strong></a>',
						$email,
						theplugin_array_to_args(['page_id' => get_the_ID()]),
						$email
					);
				}
				return '';
				break;
			case 'email-raw':
				$email = filter_var(theplugin_get_theme_mod($atts['key']));
				if ($email) {
					return sprintf(
						'<a href="mailto:%s"><strong>%s</strong></a>',
						$email,
						$email
					);
				}
				return '';
				break;
			case 'raw':
				return theplugin_get_theme_mod($atts['key'], '');
				break;
			default:
				return '';
				break;
		}
	}
});
