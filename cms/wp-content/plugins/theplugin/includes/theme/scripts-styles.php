<?php

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 	'theplugin_add_header_scripts');
add_action('wp_footer',				'theplugin_add_footer_block');
add_action('get_footer', 			'theplugin_add_footer_scripts', 100);
add_action('get_footer', 			'theplugin_add_footer_styles', 100);

add_action('wp_head', 				'theplugin_add_counters_head');
add_action('wp_footer', 			'theplugin_add_cookie_banner', 100);
add_action('wp_footer', 			'theplugin_add_counters_head_to_footer', 100);
add_action('wp_footer', 			'theplugin_add_counters_footer');

add_action('admin_enqueue_scripts', 'theplugin_admin_include_scripts_styles');

add_filter('theplugin_active_counters', 'theplugin_is_active_counters', 10, 1);
add_filter('theplugin_active_counters', 'theplugin_is_decline_counters', 30, 1);

/**
 * STYLES & SCRIPTS
 */

function theplugin_set_asstes_file($file_name = '', $add = 'css', $ver = true)
{
	$file_id = strtr($file_name, array('.css' => '', '.js' => '', '.' => '-'));
	switch ($add) {
		case 'js':
			$file_name = '/assets/js/' . $file_name;
			break;
		default:
			$file_name = '/assets/css/' . $file_name;
			break;
	}



	if (file_exists(THEPLUGIN_DIR . $file_name)) {
		if ($ver === true) {
			$ver = filemtime(THEPLUGIN_DIR . $file_name);
		}
		switch ($add) {
			case 'js':
				wp_enqueue_script($file_id, THEPLUGIN_URL . $file_name, array(), $ver);
				break;
			default:
				wp_enqueue_style($file_id, THEPLUGIN_URL . $file_name, null, $ver);
				break;
		}
	}
}

function theplugin_add_header_scripts()
{

	unset($css_file);
	unset($script_file);

	// Remove scripts & styles
}


function theplugin_add_footer_block()
{
	$variables = array(
		'ajax_url' 			=> admin_url('admin-ajax.php'),
		'theplugin_uri'		=> THEPLUGIN_URL,
		'notice_error'		=> theplugin_get_notice_wrapper(array('type' => 'error', 'title' => 'Ошибка', 'message' => '<p>Не удалось загрузить форму.</p>')),
		'notice_section'	=> theplugin_get_notice_wrapper(array('type' => 'fail', 'title' => 'Ошибка', 'message' => '<p>Не удалось загрузить секцию.</p>')),
		'break_lg'			=> 1200,
		'break_md'			=> 992,
		'break_sm'			=> 768,
		'break_xs'			=> 560
	);
	wp_register_script('theplugin', false, array(), null, true);
	wp_add_inline_script('theplugin', 'window.wp_theplugin = ' . theplugin_json_encode($variables));
	wp_enqueue_script('theplugin');

	wp_register_script('swiper', THEPLUGIN_URL . '/assets/js/swiper-bundle.min.js', array(), '8.4.2');
	wp_register_style('swiper', THEPLUGIN_URL . '/assets/css/swiper-bundle.min.css', array(), '8.4.2');
}

function theplugin_add_footer_scripts()
{
	// Swiper
	wp_enqueue_script('swiper');
	// theplugin_set_asstes_file('theplugin-script.min.js', 'js');
}

function theplugin_add_footer_styles()
{
	// Swriper
	wp_enqueue_style('swiper');
	theplugin_set_asstes_file('theplugin-style.min.css');
}


/**
 * Коды метрики в шапке сайта `wp_head`
 *
 * @return void
 */
function theplugin_add_counters_head()
{
	$is_active = apply_filters('theplugin_active_counters', true);

	if ($is_active) {
		$counters = get_option('tp_counters_code_head_id');
		if ($counters && isset($_COOKIE['pillars_cookie_agree'])) {
			$counters = wp_specialchars_decode($counters, ENT_QUOTES);
			if (strpos($counters, '<script') === false) {
				$counters = sprintf('<script type="text/javascript">%s</script>', $counters);
			}
			echo $counters;
		}
	}
}

/**
 * Вывод куки-баннера
 *
 * @return void
 */
function theplugin_add_cookie_banner()
{
	if (!isset($_COOKIE['pillars_cookie_agree']) && !isset($_COOKIE['pillars_cookie_decline'])) {
		get_template_part('template-parts/section/cookie');
	}
}

/**
 * Коды метрики для шапки сайта `wp_head` в подвал сайта для потверждения согласия на обработку ПД метрики
 *
 * @return void
 */
function theplugin_add_counters_head_to_footer()
{
	$is_active = apply_filters('theplugin_active_counters', true);
	if ($is_active) {
		$counters = get_option('tp_counters_code_head_id');
		if ($counters && !isset($_COOKIE['pillars_cookie_agree'])) {
			$counters = wp_specialchars_decode($counters, ENT_QUOTES);
			if (strpos($counters, '<script') === false) {
				$counters = sprintf('<script type="text/javascript">%s</script>', $counters);
			}

			$counters = str_replace('<script>', '<script type="text/javascript">', $counters);
			preg_match_all('#<script type="text/javascript">(.+)</script>#isU', $counters, $scripts);

			if ($scripts[1]) {
				foreach ($scripts[1] as $i => $script) {
					ob_start();
?>
					<script type="text/javascript">
						(function() {
							function load_metrika() {
								console.log('load metrika');
								<?= $script ?>
							}

							const cookiesection = document.getElementById('agree-cookie');
							if (cookiesection) {
								cookiesection.querySelector('.accept').addEventListener('click', () => {
									load_metrika();
								});

								document.querySelector('#decline-cookie .decline').addEventListener('click', () => {
									let date = new Date();
									date.setDate(date.getDate() + 1);
									let val = 'decline for ' + date;
									document.cookie = "pillars_cookie_decline=" + val + "; domain=" + document.domain.match(/[^\.]*\.[^.]*$/)[0] + "; expires=" + date.toUTCString() + "; path=/";
									cookiesection.closest('section.cookie').remove();
								});
							}
						})();
					</script>
<?php
					$script		= ob_get_clean();
					$counters	= str_replace($scripts[0][$i], $script, $counters);
				}
			}

			echo $counters;
		}
	}
}

/**
 * Коды метрики в подвале сайта `wp_footer`
 *
 * @return void
 */
function theplugin_add_counters_footer()
{
	$is_active = apply_filters('theplugin_active_counters', true);

	if ($is_active) {
		$counters = get_option('tp_counters_code_footer_id');
		if ($counters && isset($_COOKIE['pillars_cookie_agree'])) {
			$counters = wp_specialchars_decode($counters, ENT_QUOTES);
			if (strpos($counters, '<script') === false) {
				$counters = sprintf('<script type="text/javascript">%s</script>', $counters);
			}
			echo $counters;
		}
	}
}

/**
 * Отключение кодов метрики, если пользователь `editor`/`adminstrator`
 *
 * @param bool $active
 * @return bool
 */
function theplugin_is_active_counters($active)
{
	if (theplugin_is_redactor())
		return false;

	return $active;
}

/**
 * Отключение кодов метрики, если пользователь `editor`/`adminstrator`
 *
 * @param bool $active
 * @return bool
 */
function theplugin_is_decline_counters($active)
{
	if (isset($_COOKIE['pillars_cookie_decline']))
		return false;

	return $active;
}

/**
 * Подключение js-скрипта для вывода окна выбора изображения из Библиотеки
 * @source https://misha.agency/wordpress/uploader-metabox-option-pages.html
 *
 * @param [type] $hook
 * @return void
 */
function theplugin_admin_include_scripts_styles($hook)
{
	// у вас в админке уже должен быть подключен jQuery, если нет - раскомментируйте следующую строку:
	// wp_enqueue_script('jquery');
	// дальше у нас идут скрипты и стили загрузчика изображений WordPress
	if (!did_action('wp_enqueue_media')) {
		wp_enqueue_media();
	}

	wp_enqueue_script('tp-wp-upload-script', THEPLUGIN_URL . '/assets/js/wp-upload-image.js', array('jquery'), null, false);
	wp_enqueue_style('tp-fields-style', THEPLUGIN_URL . '/assets/css/tp-fields.css');
}
