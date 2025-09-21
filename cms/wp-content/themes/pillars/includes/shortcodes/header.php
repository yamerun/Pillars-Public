<?php

defined('ABSPATH') || exit;

add_shortcode('pillars_header_tagline', 'pillars_shortcode_header_tagline');
add_shortcode('pillars_header_multisite', 'pillars_shortcode_header_multisite_selector');
add_shortcode('pillars_header_contacts', 'pillars_shortcode_header_contacts');
add_shortcode('pillars_header_account', 'pillars_shortcode_header_account');
add_shortcode('pillars_header_search', 'pillars_shortcode_header_search');

/**
 * Формирование выпадающего списка городов для вывода соответсвтующих ангелов
 *
 * @return string
 */
function pillars_shortcode_header_tagline()
{
	if (!is_admin()) {

		$tag = 'div';
		if (is_front_page()) {
			$tag = 'h1';
		}

		return sprintf(
			'<%s class="header-top__heading">%s</%s>',
			$tag,
			'Производство малых архитектурных форм',
			$tag
		);
	}
}

/**
 * Формирование выпадающего списка городов для вывода соответсвтующих ангелов
 *
 * @return string
 */
function pillars_shortcode_header_multisite_selector()
{
	if (!is_admin()) {

		if (is_multisite()) {

			$options = '';
			$wrapper = '';
			$current = trim(str_replace('Pillars',  '', get_bloginfo('name')));
			$current = ($current) ? $current : 'Екатеринбург';

			$sites	= get_sites(['public' => 1]);
			$data	= [];
			$uri	= (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '/';
			foreach ($sites as $site) {
				$url = theplugin_multisite_get_url_by_domain($site->domain, $uri);
				$name = trim(str_replace('Pillars',  '', get_blog_details($site->blog_id)->blogname));
				$data[$url] = ($name) ? $name : 'Екатеринбург';
				$options .= sprintf(
					'<option value="%s"%s>%s</option>',
					esc_attr($url),
					($site->blog_id == get_current_blog_id()) ? ' selected' : '',
					$data[$url]
				);

				if ($site->blog_id == get_current_blog_id()) {
					$wrapper = sprintf(
						'<div class="multisite-selector__toggle"><div class="multisite-selector__option">%s</div>%s</div>',
						$data[$url],
						pillars_theme_get_svg_symbol('contact-icon-map')
					);
				}
			}

			$wrapper .= '<div class="multisite-selector__dropdown">';
			$wrapper .= '<div class="multisite-selector__options">';
			foreach ($data as $url => $city) {
				$wrapper .= sprintf(
					'<a class="multisite-selector__option" %s href="%s">%s</a>',
					($current == $city) ? '' : 'rel="alternate"',
					esc_url($url),
					$city
				);
			}
			$wrapper .= '</div>';
			$wrapper .= '</div>';

			// <select class="multisite-selector__list">%s</select>	$options

			return sprintf(
				'<div class="multisite-selector">%s</div>',
				$wrapper
			);
		}
	}
}

/**
 * Формирование выпадающего списка городов для вывода соответсвтующих ангелов
 *
 * @return string
 */
function pillars_shortcode_header_contacts($params)
{
	if (!is_admin()) {

		$atts = shortcode_atts(array(
			'type'		=> '',
		), $params);

		switch ($atts['type']) {
			case 'raw-link':
				$keys = array(
					'phone' => 'phone-raw',
					'email' => 'email-raw'
				);
				break;
			default:
				$keys = array(
					'phone' => 'phone',
					'email' => 'email'
				);
				break;
		}

		ob_start();
?>
		<ul class="contacts">
			<li class="phone">
				<?= do_shortcode('[pillars-contact type="' . $keys['phone'] . '"]') ?>
			</li>
			<li class="email">
				<?= do_shortcode('[pillars-contact type="' . $keys['email'] . '" key="contacts_email"]') ?>
			</li>
		</ul>
	<?php
		return ob_get_clean();
	}
}

/**
 * Формирование выпадающего списка городов для вывода соответсвтующих ангелов
 *
 * @return string
 */
function pillars_shortcode_header_account()
{
	if (!is_admin()) {

		ob_start();
	?>
		<ul class="account">
			<?php /*
			<li>
				<a class="my-account" href="#" title="Войти в Мой аккаунт">
					<?= pillars_theme_get_svg_symbol('my-account') ?>
				</a>
			</li> */ ?>
			<li><?php pillars_woocommerce_cart_link(); ?></li>
		</ul>
	<?php
		return ob_get_clean();
	}
}

/**
 * Формирование выпадающего списка городов для вывода соответсвтующих ангелов
 *
 * @return string
 */
function pillars_shortcode_header_search()
{
	if (!is_admin()) {

		ob_start();
	?>
		<form class="form-search-products" id="form-search-products" action="/search" method="post">
			<div class="form-search-products__wrapper">
				<button class="form-search-products__submit"><?= pillars_theme_get_svg_symbol('search-product') ?></button>
				<input name="search" class="form-search-products__input" placeholder="Например: Качели" type="text" autocomplete="off">
				<?php wp_nonce_field('search_verify_action', 'search_verify_key'); ?>
			</div>
		</form>
<?php
		return ob_get_clean();
	}
}
