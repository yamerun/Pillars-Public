<?php

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'pillars_init_scripts_styles');
add_action('wp_enqueue_scripts', 'pillars_scripts_header');
add_action('wp_enqueue_scripts', 'pillars_styles_header');
add_action('get_footer', 'pillars_scripts_footer');
add_action('get_footer', 'pillars_styles_fonts');
add_action('get_footer', 'pillars_styles_svg');
add_action('get_footer', 'pillars_styles_footer');

add_action('wp_body_open', 'pillars_body_open_action_video');

add_filter('the_content', 'pillars_content_has_scripts_styles', 20);

/**
 * Регистрация скриптов и стилей задействованных в теме
 *
 * @return void
 */
function pillars_init_scripts_styles()
{
	// Модульная сетка
	theplugin_file_get_content_css_by_theme_inline(array(
		'mobile'	=> '/assets/css/grid-mobile.min.css',
		'pc'		=> '/assets/css/grid.min.css'
	), 'pillars-grid', false);

	// Базовый стиль оформления большиства первых экранов сайта
	theplugin_file_get_content_css_by_theme_inline('/assets/css/common.min.css', 'pillars-main');
	// Скрипт для страничного прелоадера
	theplugin_file_get_content_css_by_theme_inline('/assets/css/preloader.min.css', 'preloader');

	// Скрипт подключения к изображениям анимации skeleton при ленивой загруки
	theplugin_file_get_content_js_by_theme_inline('/assets/js/preload-item.min.js', 'skeleton', false);

	// Регистрируем базовый набор скриптов
	wp_register_script('pillars-main', get_template_directory_uri() . '/assets/js/main.min.js', array(), true, true);
	wp_register_script('pillars-main-jquery', get_template_directory_uri() . '/assets/js/main-jquery.min.js', array(), true, true);

	wp_deregister_script('jquery');
	wp_register_script('jquery', PILLARS_URL . '/assets/js/jquery.min.js', array(), '3.5.1', true);

	// pillars_set_asstes_file('main-jquery.min.js', 'js');

	// Регистрируем подгружаемый набор стилей для большего количество страниц
	theplugin_register_css_by_theme(array(
		'mobile'	=> '/assets/css/style.min.css',
		'pc'		=> '/assets/css/style-pc.min.css'
	), 'pillars-style', false);

	// Регистрируем вставляемый стиль модульной сетки Gutenberg
	theplugin_file_get_content_css_by_theme_inline('/assets/css/wp-gutenberg-grid.min.css', 'tp-gutenberg', false);

	// Слайдер Swiper
	wp_register_script('pillars-swiper', get_template_directory_uri() . '/assets/js/swiper.min.js', array('swiper'), false, true);

	if (class_exists('WooCommerce')) {
		if (is_shop()) {
			// Регистрируем подгружаемый набор стилей для страниц Товара
			theplugin_file_get_content_css_by_theme_inline(array(
				'mobile'	=> '/assets/css/pages/wc-catalog.min.css',
				'pc'		=> '/assets/css/pages/wc-catalog.min.css'
			), 'pillars-wc-catalog', false);
		}

		if (is_product()) {
			// Регистрируем подгружаемый набор стилей для страниц Товара
			theplugin_file_get_content_css_by_theme_inline(array(
				'mobile'	=> '/assets/css/pages/wc-product-page.min.css',
				'pc'		=> '/assets/css/pages/wc-product-page.min.css'
			), 'pillars-wc-product', false);
		}

		if (is_cart() || is_checkout()) {
			// Регистрируем подгружаемый набор стилей для страниц Товара
			theplugin_file_get_content_css_by_theme_inline(array(
				'mobile'	=> '/assets/css/pages/wc-cart-page.min.css',
				'pc'		=> '/assets/css/pages/wc-cart-page.min.css'
			), 'pillars-wc-cart', false);
		}
	}
}

/**
 * Вывод скриптов в header темы
 *
 * @return void
 */
function pillars_scripts_header()
{
	// Скрипт для страничного прелоадера
	theplugin_file_get_content_js_by_theme_inline('/assets/js/preloader-page.min.js', 'preloader');

	/**
	 * Базовые глобальные переменные темы
	 */
	$popup_key = 'pillars-popup';
	wp_add_inline_script('preloader', 'window.wp_data = ' . json_encode(array(
		'ajax_url'		=> admin_url('admin-ajax.php'),
		'theme_uri'		=> get_template_directory_uri(),
		'break_sm'		=> 768,
		'popup_key'		=> $popup_key,
		'popup_wrapper'	=> sprintf(
			'<div class="%s__wrapper"><div class="%s__content"><a href="#popup_id" class="%s__close"></a></div></div>',
			$popup_key,
			$popup_key,
			$popup_key
		),
		'currencysymbol'	=> get_woocommerce_currency_symbol()
	)));
}

/**
 * Вывод стилей в header темы
 *
 * @return void
 */
function pillars_styles_header()
{
	// Базовый стиль оформления большиства первых экранов сайта


	if (is_front_page()) {
		theplugin_file_get_content_css_by_theme_inline('/assets/css/pages/frontpage.min.css', 'pillars-frontpage');
	}

	if (theplugin_is_template('template-contact')) {
		theplugin_file_get_content_css_by_theme_inline('/assets/css/pages/contact.min.css', 'pillars-contact');
	}

	if (class_exists('WooCommerce')) {
		if (is_cart() || is_checkout()) {
			theplugin_file_get_content_css_by_theme_inline('/assets/css/common-wc.min.css', 'pillars-wc-main');
		}
	}

	/**
	 * Добавочные классы для body
	 */
	add_filter('body_class', function ($classes) {

		// $classes[] = 'preload';

		foreach ($classes as $i => $item) {
			foreach (array('post-type-', 'term-', 'page-template-') as $class) {
				if (strpos($item, $class) === 0) {
					unset($classes[$i]);
					break;
				}
			}

			if (isset($classes[$i])) {
				foreach (array('page', 'wp-custom-logo') as $class) {
					if ($item == $class) {
						unset($classes[$i]);
						break;
					}
				}
			}
		}

		return array_values($classes);
	}, 10, 1);
}

/**
 * Вывод inline-стиля подгрузки шрифтов темы
 *
 * @return void
 */
function pillars_styles_fonts()
{
	theplugin_file_get_content_css_by_theme_inline('/assets/css/fonts.min.css', 'pillars-fonts');
}

/**
 * Добавляем svg-код иконок
 */
function pillars_styles_svg()
{
	$svgs = array(
		'logo',
		'header-catalog', 'search-product', 'my-account', 'shopping-cart', 'video-play',
		'contact-icon-phone', 'contact-icon-mail', 'contact-icon-map',
		'social-icon-vk', 'social-icon-vkvideo', 'social-icon-youtube', 'social-icon-telegram', 'social-icon-dzen', 'social-icon-yandex',
		'arrow-right'
	);

	$svgs = apply_filters('theplugin_get_svg_symbol_filter', $svgs);

	echo '<svg style="display:none;">' . PHP_EOL;
	foreach ($svgs as $svg_key) {
		echo "\t" . pillars_theme_get_svg($svg_key, '', true);
	}
	echo '</svg>' . PHP_EOL;
}

/**
 * Вывод скриптов в футере темы
 *
 * @return void
 */
function pillars_scripts_footer()
{
	wp_enqueue_script('skeleton');
	wp_enqueue_script('fancybox');
	wp_enqueue_script('pillars-main');
	wp_enqueue_script('jquery');
	wp_enqueue_script('pillars-main-jquery');
	// wp_enqueue_script('plotly');
}

function pillars_styles_footer()
{
	wp_enqueue_style('fancybox');
	wp_enqueue_style('pillars-grid');
	wp_enqueue_style('pillars-style');

	if (class_exists('WooCommerce')) {
		if (is_shop())
			wp_enqueue_style('pillars-wc-catalog');

		if (is_product())
			wp_enqueue_style('pillars-wc-product');

		if (is_cart() || is_checkout())
			wp_enqueue_style('pillars-wc-cart');
	}

	wp_enqueue_script('pillars-swiper');
}


/**
 * Проверка контента поста на содержание слайдера swiper
 *
 * @param [type] $content
 * @return string
 */
function pillars_content_has_scripts_styles($content)
{
	// Если есть css-класс `wp-block-columns`, то вставлем стиль сетки Gutenberg
	if (mb_strpos($content, 'wp-block-columns') !== false) {
		wp_enqueue_style('tp-gutenberg');
	}

	if (mb_strpos($content, 'swiper-container') !== false || true) {
		wp_enqueue_script('swiper');
		wp_enqueue_style('swiper');
		// wp_enqueue_script('pillars-swiper');
	}
	// Возвращаем контент.
	return $content;
}



/**
 * Function for `wp_body_open` action-hook.
 *
 * @return void
 */
function pillars_body_open_action()
{
?>
	<style>
		.pillars-preloader-page {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: #202020;
			z-index: 9999;
		}

		.pillars-preloader-page-text {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			margin: auto;
			text-align: center;
			width: 100%;
			height: 100px;
			color: #fff;
			font-family: fantasy, sans-serif;
			font-size: 2rem;
			line-height: 100px;
		}

		.pillars-preloader-page-text>span {
			display: inline-block;
			margin: 0 5px;
			transition: all linear 0.3s;
		}

		.pillars-preloader-page-text>span:nth-child(1) {
			opacity: 1;
			filter: blur(0px);
			-webkit-animation: blur-text 1.5s 0s infinite linear alternate;
			animation: blur-text 1.5s 0s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(2) {
			opacity: .85;
			filter: blur(0.5px);
			-webkit-animation: blur-text 1.5s 0.2s infinite linear alternate;
			animation: blur-text 1.5s 0.2s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(3) {
			opacity: .7;
			filter: blur(1px);
			-webkit-animation: blur-text 1.5s 0.4s infinite linear alternate;
			animation: blur-text 1.5s 0.4s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(4) {
			opacity: .6;
			filter: blur(1.6px);
			-webkit-animation: blur-text 1.5s 0.6s infinite linear alternate;
			animation: blur-text 1.5s 0.6s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(5) {
			opacity: .5;
			filter: blur(2px);
			-webkit-animation: blur-text 1.5s 0.8s infinite linear alternate;
			animation: blur-text 1.5s 0.8s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(6) {
			opacity: .4;
			filter: blur(2.6px);
			-webkit-animation: blur-text 1.5s 1s infinite linear alternate;
			animation: blur-text 1.5s 1s infinite linear alternate;
		}

		.pillars-preloader-page-text>span:nth-child(7) {
			opacity: .3;
			filter: blur(3.2px);
			-webkit-animation: blur-text 1.5s 1.2s infinite linear alternate;
			animation: blur-text 1.5s 1.2s infinite linear alternate;
		}

		@-webkit-keyframes blur-text {
			0% {
				filter: blur(0px);
			}

			100% {
				filter: blur(4px);
			}
		}

		@keyframes blur-text {
			0% {
				opacity: 1;
				filter: blur(0px);
			}

			100% {
				opacity: .2;
				filter: blur(4px);
			}
		}
	</style>

	<div class="pillars-preloader-page">
		<div class="pillars-preloader-page-text">
			<span>P</span>
			<span>I</span>
			<span>L</span>
			<span>L</span>
			<span>A</span>
			<span>R</span>
			<span>S</span>
		</div>
	</div>

	<script>
		const preloaderpage = document.querySelector('.pillars-preloader-page');
		setTimeout(function() {
			if (preloaderpage) {
				preloaderpage.remove();
			}
		}, 5000);
		document.addEventListener('DOMContentLoaded', function(e) {
			if (preloaderpage) {
				preloaderpage.remove();
				// preloaderpage.style.display = 'none';
			}
		});
	</script>
<?php
}

/**
 * Function for `wp_body_open` action-hook.
 *
 * @return void
 */
function pillars_body_open_action_video()
{
?>
	<style>
		.pillars-preloader-page {
			display: flex;
			align-items: center;
			justify-content: center;
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: #000;
			z-index: 9999;
		}

		.pillars-preloader-page-image {
			width: 320px;
			height: 90px;
			opacity: 0;
			filter: blur(4px);
			-webkit-animation: blink 4.5s 0s infinite linear alternate;
			animation: blink 4.5s 0s infinite linear alternate;
		}

		.pillars-preloader-page-image img {
			width: 100%;
			height: auto;
		}

		@-webkit-keyframes blink {
			100% {
				opacity: 0.5;
				filter: blur(0px);
			}

			70% {
				opacity: 1;
				filter: blur(0px);
			}

			20% {
				opacity: 1;
				filter: blur(0px);
			}

			0% {
				opacity: 0;
				filter: blur(4px);
			}
		}

		@keyframes blink {
			100% {
				opacity: 0.5;
				filter: blur(0px);
			}

			70% {
				opacity: 1;
				filter: blur(0px);
			}

			20% {
				opacity: 1;
				filter: blur(0px);
			}

			0% {
				opacity: 0;
				filter: blur(4px);
			}
		}
	</style>
	<div class="pillars-preloader-page">
		<div class="pillars-preloader-page-image">
			<img width="320" height="90" alt="Pillars" src="data:image/jpeg;base64,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" />
			<?php /* <img src="<?= get_template_directory_uri() ?>/assets/images/logo-saber-line.jpg" width="320" height="90" alt="Pillars"> */ ?>
		</div>
	</div>

	<script>
		const preloaderpage = document.querySelector('.pillars-preloader-page');
		setTimeout(function() {
			if (preloaderpage) {
				preloaderpage.remove();
			}
		}, 5000);
		document.addEventListener('DOMContentLoaded', function(e) {
			if (preloaderpage) {
				preloaderpage.remove();
			}
		});
	</script>
<?php
}
