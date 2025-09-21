<?php

defined('ABSPATH') || exit;

/**
 * Вывод html-блока видео с YouTube
 *
 * @return string
 */
add_shortcode('pillars-video-placeholder', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'url'		=> '',
			'caption'	=> '',
			'size'		=> 'sddefault',
			'cover_id'	=> 0
		), $params);

		$url	= theplugin_get_video_embed_link($atts['url']);
		if (!$url)
			return '';

		if (absint($atts['cover_id'])) {
			$covers = wp_get_attachment_image_url($atts['cover_id'], $atts['size']);
		} else {
			$covers	= theplugin_get_youtube_covers($atts['url']);
			$covers = $covers[$atts['size']];
		}

		ob_start(); ?>
		<a class="video-placeholder" target=_blank href="<?= $url ?>">
			<div class="video-placeholder__cover image-radius">
				<div class="media-ratio">
					<img width="640" height="480" src="<?= $covers ?>" />
				</div>
			</div>
			<?php if ($atts['caption']) { ?>
				<div class="video-placeholder__desc">
					<?= wpautop($atts['caption']) ?>
				</div>
			<?php } ?>
			<div class="video-placeholder__btn">
				<svg role="img" fill="none" class="" data-key="video-play" viewBox="0 0 70 70">
					<use href="#video-play"></use>
				</svg>
			</div>
		</a>
<?php return ob_get_clean();
	}
});

add_shortcode('get-popup', function ($params) {
	$atts = shortcode_atts(
		array(
			'id'		=> '',
			'form'		=> 'mail',
			'text'		=> 'Связаться с нами',
			'container'	=> 'button',
			'class'		=> 'btn-2',
			'align'		=> 'none',
			'args'		=> ''
		),
		$params
	);

	$atts['id'] = ($atts['id']) ? $atts['id'] : $atts['form'];

	switch ($atts['container']) {
		case 'a':
			$contain = ['a href="#' . $atts['id'] . '" ', 'a'];
			break;
		case 'div':
			$contain = ['div ', 'div'];
			break;
		default:

			$contain = ['button ', 'button'];
			break;
	}

	switch ($atts['align']) {
		case 'left':
			$align = ['<div class="txt-left">', '</div>'];
			break;
		case 'center':
			$align = ['<div class="txt-center">', '</div>'];
			break;
		case 'right':
			$align = ['<div class="txt-right">', '</div>'];
			break;
		default:
			$align = ['', ''];
			break;
	}

	$form_args = '';
	if ($atts['args']) {
		$form_args = theplugin_maybe_args($atts['args']);
		if (!is_array($form_args)) {
			$form_args = '';
		}
	}

	return sprintf(
		'%s<%s class="%s pillars-popup__btn" %s%s%s><span>%s</span></%s>%s',
		$align[0],
		$contain[0],
		$atts['class'],
		($atts['id']) ? ' data-id="' . $atts['id'] . '"' : '',
		($atts['form']) ? ' data-form="form-' . $atts['form'] . '"' : '',
		($form_args) ? ' data-form_args="' . $atts['args'] . '"' : '',
		$atts['text'],
		$contain[1],
		$align[1]
	);
});

add_shortcode('get-button', function ($params) {
	$atts = shortcode_atts(
		array(
			'id'		=> 'mail',
			'text'		=> 'Связаться с нами',
			'container'	=> 'button',
			'class'		=> 'btn-1',
			'align'		=> 'none',
			'args'		=> ''
		),
		$params
	);

	switch ($atts['container']) {
		case 'a':
			$contain = ['a href="#' . $atts['id'] . '" ', 'a'];
			break;
		case 'div':
			$contain = ['div ', 'div'];
			break;
		default:

			$contain = ['button ', 'button'];
			break;
	}

	switch ($atts['align']) {
		case 'left':
			$align = ['<div class="txt-left">', '</div>'];
			break;
		case 'center':
			$align = ['<div class="txt-center">', '</div>'];
			break;
		case 'right':
			$align = ['<div class="txt-right">', '</div>'];
			break;
		default:
			$align = ['', ''];
			break;
	}

	$form_args = '';
	if ($atts['args']) {
		$form_args = theplugin_maybe_args($atts['args']);
		if (!is_array($form_args)) {
			$form_args = '';
		}
	}

	return sprintf(
		'%s<%s class="%s"%s><span>%s</span></%s>%s',
		$align[0],
		$contain[0],
		$atts['class'],
		($form_args) ? ' data-form_args="' . $atts['args'] . '"' : '',
		$atts['text'],
		$contain[1],
		$align[1]
	);
});

/**
 * Вывод файла темы по заданным параметрам
 *
 * @return string
 */
add_shortcode('get-section', function ($params) {
	$atts = shortcode_atts(array('template' => '', 'parts' => 'section/section', 'path' => '', 'args' => ''), $params);

	// Замена старых ключей от темы `ledmebel`
	$atts['part'] = $atts['template'];
	$atts['type'] = strtr($atts['parts'], [
		'/section' => '',
		'/content' => ''
	]);

	$shortcode = sprintf(
		'[tp-get-part part="%s" type="%s" path="%s" args="%s"]',
		$atts['part'],
		$atts['type'],
		$atts['path'],
		$atts['args'],
	);

	return do_shortcode($shortcode);
});


/**
 * Вывод html-обёртки изображения со скругленными краями
 *
 * @return string
 */
add_shortcode('image-radius', function ($params) {
	$atts = shortcode_atts(array('id' => 0, 'size' => 'medium'), $params);

	if (!absint($atts['id']))
		return '';

	return '<div class="image-radius"><div class="media-ratio">' . wp_get_attachment_image(absint($atts['id']), $atts['size']) . '</div></div>';
});
