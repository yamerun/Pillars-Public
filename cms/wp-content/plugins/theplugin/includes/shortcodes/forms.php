<?php

defined('ABSPATH') || exit;

add_shortcode('tp-get-form', function ($params) {
	if (!is_admin() && isset($_GET['form'])) {
		$atts = shortcode_atts(array('template' => ''), $params);

		return theplugin_get_template_part_return('template-parts/form/form', $atts['template']);
	}
});

add_shortcode('tp-get-popup-form', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array('form_id' => '', 'title' => 'Открыть форму', 'class' => 'btn-1', 'args' => ''), $params);

		if (empty($atts['form_id'])) {
			return '';
		}

		if (!empty($atts['args'])) {
			$data = theplugin_maybe_args($atts['args']);
			if (is_array($data)) {
				$data = ' data-form-args="' . esc_attr(json_encode($data)) . '"';
			}
		} else {
			$data = '';
		}

		return sprintf(
			'<a href="#%s" class="tp-popup-link tp-form-ajax %s"%s>%s</a>',
			$atts['form_id'],
			$atts['class'],
			$data,
			$atts['title']
		);
	}
});
