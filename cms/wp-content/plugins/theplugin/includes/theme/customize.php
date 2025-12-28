<?php

defined('ABSPATH') || exit;

add_filter('the_content', 'theplugin_the_content_set_contacts', 99);

/**
 * Проверка контента поста на содержание паттернов контактов
 *
 * @param [type] $content
 * @return string
 */
function theplugin_the_content_set_contacts($content)
{
	if (!is_admin()) {
		$mods		= theplugin_get_theme_mods();
		$contacts	= [];

		foreach (['contacts_phone_1', 'contacts_phone_2'] as $key) {
			if (isset($mods[$key]) && $mods[$key]) {
				$contacts["{{$key}}"] = sprintf(
					'<a href="tel:%s">%s</a>',
					preg_replace('#[^0-9\+]#', '', $mods[$key]),
					$mods[$key]
				);
			}
		}

		if (isset($mods['contacts_phone_w']) && $mods['contacts_phone_w']) {
			$contacts['{{contacts_phone_w}}'] = sprintf(
				'<a href="https://wa.me/%s" target=_blank>%s</a>',
				preg_replace('#[^0-9\+]#', '', $mods['contacts_phone_w']),
				$mods['contacts_phone_w']
			);
		}

		foreach (['contacts_work_1', 'contacts_work_2', 'contacts_address_1', 'contacts_address_2'] as $key) {
			if (isset($mods[$key]) && $mods[$key]) {
				$contacts["{{$key}}"] = $mods[$key];
			}
		}

		if (isset($mods['contacts_email']) && $mods['contacts_email']) {
			$contacts['{{contacts_email}}'] = sprintf(
				'<a href="mailto:%s">%s</a>',
				esc_attr($mods['contacts_email']),
				$mods['contacts_email']
			);
		}

		if (isset($contacts['contacts_address_1']) && isset($contacts['contacts_address_2'])) {
			$contacts['{{contacts_address}}'] = join(', ', [$contacts['contacts_address_1'], $contacts['contacts_address_2']]);
		}

		$content = strtr($content, $contacts);
	}

	return $content;
}
