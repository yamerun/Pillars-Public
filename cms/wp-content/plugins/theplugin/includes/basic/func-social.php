<?php

/**
 * Вывод списка ссылок на соцсети, заданных параметром `$data`
 *
 * @param array $data*
 * @param array $args
 *
 * @return string|array
 */
function theplugin_get_social_list($data = [], $args = [])
{
	if (!$data)
		return '';

	$defaults = [
		'wrapper'	=> true,
		'container' => 'ul',
		'class'		=> 'social-links',
		'before' 	=> '<li>',
		'after' 	=> '</li>',
		'title'		=> false
	];

	$args	= wp_parse_args($args, $defaults);
	$titles	= array(
		'vk'		=> __('ВКонтакте', 'theplugin'),
		'vkvideo'	=> __('VK Видео', 'theplugin'),
		'youtube'	=> __('YouTube', 'theplugin'),
		'telegram'	=> __('Telegram', 'theplugin'),
		'dzen'		=> __('Яндекс Дзен', 'theplugin'),
	);

	if ($args['wrapper']) {
		$wrapper = '';
		foreach ($data as $key => $link) {

			if (absint($link)) {
				$link = get_permalink($link);
			}

			$wrapper .= sprintf(
				"\n\t\t\t" . '%s<a href="%s" target="_blank">%s%s</a>%s',
				$args['before'],
				$link,
				theplugin_get_svg_symbol('social-icon-' . $key, 'social'),
				($args['title']) ? ' ' . strtr($key, $titles) : '',
				$args['after']
			);
		}

		return sprintf(
			'<%s class="%s">%s</%s>',
			$args['container'],
			$args['class'],
			$wrapper,
			$args['container']
		);
	} else {
		return $data;
	}
}

/**
 * Получение массива ссылок на соцсети из настроек плагина
 *
 * @return array
 */
function theplugin_get_social_list_by_theme_mod()
{
	$theme_socials = theplugin_get_theme_mod('social_links');

	if (!$theme_socials)
		return array();

	$theme_socials	= explode("\n", $theme_socials);
	$data			= array();

	if (is_array($theme_socials) && $theme_socials) {
		foreach ($theme_socials as $theme_social) {
			if ($theme_social) {
				$theme_social = explode(':', trim($theme_social));
				$key = trim($theme_social[0]);
				array_shift($theme_social);
				$link = trim(implode(':', $theme_social));
				$data[$key] = $link;
			}
		}
	}

	return $data;
}

/**
 * HTML-обёртка виджета отзывов Яндекс.Карт
 *
 * @param [type] $company
 * @return string
 */
function theplugin_yandex_reviews_widget($company)
{
	$args = get_option('maps_reviews_widget');

	$update = true;
	if ($args) {
		if ((wp_date('U') - $args['update']) < 86400) {
			$update = false;
		}
	}

	if ($update) {

		$queryUrl = 'https://yandex.ru/maps-reviews-widget/' . $company . '?comments';
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $queryUrl);
		if (!$data = curl_exec($ch)) {
			if (!isset($args['count'])) {
				return '';
			}
		}
		curl_close($ch);

		$args		= [];
		$elements	= 0;
		$tags = [
			'count' => array(
				'<p class="mini-badge__stars-count">',
				'</p>',
			),
			'rating' => array(
				'<a class="mini-badge__rating" ',
				'</a>',
			),
			'stars' => array(
				'<div class="mini-badge__stars">',
				'</div>',
			)
		];

		foreach ($tags as $key => $items) {
			$_data = theplugin_get_preg_tag($items, $data);
			if ($_data) {
				$args[$key] = $_data[0];
				$elements++;
			}
		}

		if ($elements == count($tags)) {
			$args['update'] = wp_date('U');
			update_option('maps_reviews_widget', $args);
		} else {
			$args = [
				'count'		=> '',
				'stars'		=> '',
				'rating'	=> '',
			];
		}
	}

	return sprintf('<div class="mini-badge__rating-info">%s<div>%s%s</div></div>', $args['count'], $args['stars'], $args['rating']);
}
