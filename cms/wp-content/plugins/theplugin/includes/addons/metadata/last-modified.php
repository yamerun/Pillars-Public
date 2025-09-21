<?php

defined('ABSPATH') || exit;

/**
 * LAST MODIFIED
 *
 * Задаём все функции и хуки после зашрузки всех плагинов
 * в частности WooCommerce
 */
add_action('plugins_loaded', function () {

	add_action('wp_head', 'theplugin_get_last_modified_meta', 1);
	add_action('template_redirect', 'theplugin_get_last_modified_header');

	/**
	 * Получение последнего изменения
	 *
	 * @param boolean $unix
	 * @return int|string
	 */
	function theplugin_get_last_modified($unix = false)
	{
		global $post;

		$time 		= 0;
		$template 	= '';

		// TODO в будущем перевести в фильтр add_filter

		if (is_product_taxonomy()) {
			// Если Категория товаров
			$request 	= explode('/', $_SERVER['REQUEST_URI']);
			$term 		= false;
			foreach ($request as $term_slug) {
				if (!empty($term_slug)) {
					if (get_term_by('slug', $term_slug, 'product_cat')) {
						$term 		= get_term_by('slug', $term_slug, 'product_cat');
						$template 	= 'woocommerce/filter/category-' . $term->term_id . '.php';
						$time 		= theplugin_get_last_modified_product($term->term_id);
						break;
					}
				}
			}
		} else if (is_checkout() || is_cart()) {
			// Если Корзина, то обнуляем время последнего изменения
			$time = 0;
		} else if (is_shop()) {
			// Если Каталог, то задаём основной шаблон магазина
			$time 		= theplugin_get_last_modified_product();
			$template 	= 'woocommerce/archive-product.php';
		} else if ($post) {
			$time 		= get_the_modified_date('U');
			$template 	= get_post_meta($post->ID, '_wp_page_template', true);

			switch ($template) {
				case 'templates/template-new-products.php':
					$new_product_id = pillars_wc_has_new_products();
					if ($new_product_id) {
						$template	= '';
						$time 		= get_the_modified_date('U', $new_product_id);
					}
					break;
				case 'default':
					if (is_front_page()) {
						$template = 'templates/main-page.php';
					}
					if (is_home()) {
						$template = 'templates/post-page.php';
					}
					if (is_product()) {
						$template = '';
					}
					break;
				case '':
					break;
			}
		} else {
		}

		// Проверяем существование шаблонов темы
		if ($template && file_exists(get_stylesheet_directory() . '/' . $template)) {
			// Получаем дату изменения шаблона темы и сравниваем с последним изменением
			$temp_time = filemtime(get_stylesheet_directory() . '/' . $template);
			$time = ($temp_time > $time) ? $temp_time : $time;
		}

		$timezone 	= wp_timezone();
		if ($time) {
			$time = $time - get_option('gmt_offset') * 3600;
			// Если пользователь авторизирован, то добавляем 1 час, чтобы вступили в силу изменения интерфейса
			if (is_user_logged_in()) {
				$time += 3600;
			}
		} else {
			// Задаём последнее изменение по текущему времени сайта
			$time = current_time('U');
		}

		if ($unix) {
			return $time;
		} else if ($time) {
			$time = str_replace('+0000', 'GMT', gmdate('r', $time));
		}

		return $time;
	}

	/**
	 * Проверка возможности получения последнего изменения
	 *
	 * @return bool
	 */
	function theplugin_has_last_modified()
	{
		$post 	= get_post();
		$check 	= false;
		if ($post) {
			if (is_page_template()) {
				$template = realpath(get_page_template());
				if (strpos($template, 'template-admin') !== false) {
					$check = true;
				}
			}
			if ($post->post_status != 'publish' && !$check) {
				$check = true;
			}
		}
		return $check;
	}

	/**
	 * Вывод в мета-теги html последнего изменения
	 *
	 * @return string
	 */
	function theplugin_get_last_modified_meta()
	{
		if (!theplugin_has_last_modified()) {
			$last_modified = theplugin_get_last_modified();
			if ($last_modified) {
				echo "\t" . '<meta http-equiv="Last-Modified" content="' . $last_modified . '">' . "\n";
			}
		}
	}

	/**
	 * Проверка наличие последнего изменения в заголовке HTTP-запроса
	 *
	 * @return void
	 */
	function theplugin_get_last_modified_header()
	{

		if ((defined('DOING_AJAX') && DOING_AJAX) || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) || (defined('REST_REQUEST') && REST_REQUEST) || (is_admin()) || (is_cart()) || (is_checkout())) {
			return;
		}

		if (theplugin_has_last_modified()) {
			return;
		}

		$last_modified = theplugin_get_last_modified();
		$if_modified_since = false;
		if (!empty($last_modified)) {
			if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) 	$if_modified_since = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) 	$if_modified_since = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
			if ($if_modified_since && $if_modified_since >= theplugin_get_last_modified(true)) {
				$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');
				header($protocol . ' 304 Not Modified');
				exit;
			}
			header('Last-Modified: ' . $last_modified);
		} else {
			return;
		}
	}

	/**
	 * Получение последнего изменения категории товаров по самому позднему товару
	 *
	 * @param integer $product_cat
	 * @return int
	 */
	function theplugin_get_last_modified_product($product_cat = 0)
	{
		$time = 0;

		$query_args = array(
			'post_type'			=> array('product', 'product_variation'),
			'post_status'		=> 'publish',
			'orderby'			=> 'date',
			'order'				=> 'DESC',
			'posts_per_page'	=> -1,
		);

		if ($product_cat != 0) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy'	=> 'product_cat',
					'field'		=> 'id',
					'terms'		=> $product_cat
				),
			);
		}

		$prod_time = [];

		$query = new WP_Query($query_args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$prod_time[] = get_the_modified_date('U', get_the_ID());
			}
		}
		wp_reset_postdata();

		if (!empty($prod_time)) {
			$time = max($prod_time);
		}

		return $time;
	}
});
