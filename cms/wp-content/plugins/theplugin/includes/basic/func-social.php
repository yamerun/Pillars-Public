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
function theplugin_yandex_reviews_widget($company, $is_mini = false)
{
	$args		= get_option('maps_reviews_widget');
	$wrappers	= '';

	$update = true;
	if ($args) {
		if ((wp_date('U') - $args['update']) < 86400) {
			$update = false;
		}
	}

	$commets = get_option('maps_reviews_widget_comments');
	if (!$commets && !$update) {
		$update = true;
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

		$dom	= new DomDocument();
		$dom->loadHTML($data);
		$xpath	= new DOMXpath($dom);
		$link	= $xpath->query("//a[contains(@class,'badge__link-to-map')]");
		if ($link)
			$args['link'] = $link[0]->getAttribute('href');

		if ($elements == count($tags)) {
			$args['update'] = wp_date('U');
			update_option('maps_reviews_widget', $args);
		} else {
			$args = [
				'count'		=> '',
				'stars'		=> '',
				'rating'	=> '',
				'link'		=> ''
			];
		}

		$commets	= [];
		$tags		= [
			'photo'	=> "//img[contains(@class,'comment__photo')]",
			'name'	=> "//p[contains(@class,'comment__name')]",
			'date'	=> "//p[contains(@class,'comment__date')]",
			'stars'	=> "//ul[contains(@class,'stars')]",
			'text'	=> "//p[contains(@class,'comment__text')]",
		];

		foreach ($tags as $key => $query) {
			$tables = $xpath->query($query);
			if ($tables->length) {
				foreach ($tables as $i => $item) {
					switch ($key) {
						case 'photo':
							$commets[$i][$key] = $item->getAttribute('src');
							break;
						case 'stars':
							$commets[$i][$key] = $item->childNodes->length;
							break;
						default:
							$commets[$i][$key] = $item->textContent;
							break;
					}
				}
			}
		}

		if ($commets) {
			update_option('maps_reviews_widget_comments', $commets);
		}
	}

	if ($is_mini) {
		return sprintf(
			'<div class="mini-badge__rating-info">%s<div>%s%s</div></div>',
			$args['count'],
			$args['stars'],
			$args['rating'],
		);
	}

	$wrappers = '';
	if ($commets) {
		ob_start(); ?>
		<div class="y-reviews">
			<div class="y-reviews-badge">
				<img loading="lazy" src="<?= get_template_directory_uri() ?>/assets/images/yandex-map.svg" width="55" height="28">
				<div class="mini-badge__rating-info">
					<?= $args['count'] ?>
					<div>
						<?= $args['stars'] ?>
						<?= $args['rating'] ?>
					</div>
				</div>
				<a href="<?= $args['link'] ?>" class="mini-badge__link-to-map" target="_blank">Оставить отзыв</a>
			</div>
			<div class="y-reviews-slider">
				<div class="y-review__container swiper-container">
					<div class="y-review__wrapper swiper-wrapper">
						<?php foreach ($commets as $comment) {
							if (isset($comment['text']) && $comment['text']) { ?>
								<div class="y-review__slide swiper-slide">
									<div class="y-review">
										<div class="y-review__header">
											<img src="<?= $comment['photo'] ?>" alt="" class="y-review__photo">
											<div class="y-review__profile">
												<p class="y-review__name"><?= $comment['name'] ?></p>
												<p class="y-review__date"><?= $comment['date'] ?></p>
											</div>
										</div>
										<div class="y-review__stars">
											<ul class="stars-list">
												<?php for ($i = 0; $i < $comment['stars']; $i++) {
													echo '<li class="stars-list__star"></li>';
												} ?>
											</ul>
										</div>
										<p class="y-review__text"><?= $comment['text'] ?></p>
									</div>
								</div>
						<?php }
						} ?>
					</div>
					<div class="pillars-slider__navigations">
						<div class="pillars-slider__pagination"></div>
						<div class="pillars-slider__buttons">
							<div class="pillars-slider__button-prev"></div>
							<div class="pillars-slider__button-next"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php $wrappers = ob_get_clean();
	}

	return $wrappers;
}
