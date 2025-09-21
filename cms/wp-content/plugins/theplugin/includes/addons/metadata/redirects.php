<?php

defined('ABSPATH') || exit;

add_action('template_redirect', 'theplugin_get_redirect_header', 1);
// add_action('wp_insert_post_data', 'theplugin_has_change_uri_post', 10, 2);
// add_action('edit_post', 'theplugin_has_change_uri_post', 10, 1);
// add_filter('wp_insert_post_data', 'theplugin_has_change_uri_post', 10, 4);
// add_filter('wp_update_term_data', 'theplugin_has_change_uri_taxonomy', 10, 4);

/**
 * Проверка существование редиректа при инициализации шаблона темы template_redirect
 *
 * @return void
 */
function theplugin_get_redirect_header()
{
	if ((defined('DOING_AJAX') && DOING_AJAX) || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) || (defined('REST_REQUEST') && REST_REQUEST) || (is_admin())) {
		return;
	}

	$redirects = [
		'/product-category/maf' 		=> '/catalog/',
		'/product-category/ulichnye-skamejki/parkovye-skamejki' 		=> '/product-category/ulichnye-skamejki/plastikovye-skamejki/',

		'/gallery'				=> '/portfolio',
		'/gallery/kashpo'		=> '/portfolio',
		'/gallery/stoly'		=> '/portfolio',
		'/gallery/kacheli'		=> '/portfolio',
		'/gallery/kuby'			=> '/portfolio',
		'/gallery/shary'		=> '/portfolio',
		'/gallery/skamejki'		=> '/portfolio',
		'/gallery/svetilniki'	=> '/portfolio',

		'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-1' => '/product/kacheli-delta-ring-1',
		'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-2' => '/product/kacheli-delta-ring-2',
		'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-3' => '/product/kacheli-delta-ring-3',
		'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-4' => '/product/kacheli-delta-ring-4',
		'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-5' => '/product/kacheli-delta-ring-5',
		'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-1' => '/product/kacheli-delta-solo-1',
		'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-2' => '/product/kacheli-delta-solo-2',
		'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-3' => '/product/kacheli-delta-solo-3',
		'/product/svetyashhiesya-kacheli-sota-s-konstrukcziej-1' => '/product/kacheli-delta-sota-1',
		'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-2-2' => '/product/kacheli-delta-sota-2',
		'/product/svetyashhiesya-kacheli-sota-s-konstrukcziej-3' => '/product/kacheli-delta-sota-3',

		'/product/skamejka-eco-1'	=> '/product/skamejka-eco-1-s',
		'/product/skamejka-eco-2'	=> '/product/skamejka-eco-1-m',
		'/product/skamejka-eco-3'	=> '/product/skamejka-eco-1-l',
		'/product/skamejka-eco-4'	=> '/product/skamejka-eco-2-s',
		'/product/skamejka-eco-5'	=> '/product/skamejka-eco-2-m',
		'/product/skamejka-eco-6'	=> '/product/skamejka-eco-2-l',
		'/product/skamejka-eco-7'	=> '/product/skamejka-eco-3-s',
		'/product/skamejka-eco-8'	=> '/product/skamejka-eco-3-m',
		// '/product/skamejka-eco-9'	=> '/product/skamejka-eco-3-l',
		'/product/skamejka-eco-14'	=> '/product/skamejka-eco-4-s',
		'/product/skamejka-eco-15'	=> '/product/skamejka-eco-4-m',
		'/product/skamejka-eco-16'	=> '/product/skamejka-eco-4-l',
		'/product/skamejka-eco-17'	=> '/product/skamejka-eco-5-s',
		'/product/skamejka-eco-18'	=> '/product/skamejka-eco-5-m',
		'/product/skamejka-eco-19'	=> '/product/skamejka-eco-5-l',
		'/product/skamejka-eco-20'	=> '/product/skamejka-eco-6-s',
		'/product/skamejka-eco-21'	=> '/product/skamejka-eco-6-m',
		'/product/skamejka-eco-22'	=> '/product/skamejka-eco-6-l',
		'/product/skamejka-eco-23'	=> '/product/skamejka-eco-7-s',
		'/product/skamejka-eco-24'	=> '/product/skamejka-eco-7-m',
		'/product/skamejka-eco-25'	=> '/product/skamejka-eco-7-l',
		'/product/skamejka-eco-12'	=> '/product/skamejka-eco-8-s',
		'/product/skamejka-eco-13'	=> '/product/skamejka-eco-8-m',
		// '/product/skamejka-eco-10'	=> '/product/skamejka-eco-9',
		// '/product/skamejka-eco-11'	=> '/product/skamejka-eco-10',
		'/product/skamejka-boat-1'	=> '/product/skamejka-eco-11',

	];

	$is_redirect = true;
	if (is_multisite()) {
		$is_redirect = (get_current_blog_id() === 1) ? true : false;

		if (get_current_blog_id() == 22) {
			$is_redirect = true;

			$redirects = [
				'/gallery'				=> '/portfolio',
				'/gallery/kashpo'		=> '/portfolio',
				'/gallery/stoly'		=> '/portfolio',
				'/gallery/kacheli'		=> '/portfolio',
				'/gallery/kuby'			=> '/portfolio',
				'/gallery/shary'		=> '/portfolio',
				'/gallery/skamejki'		=> '/portfolio',
				'/gallery/svetilniki'	=> '/portfolio',

				'/registration'				=> '/',
				'/registration/confirm'		=> '/',
				'/blog/2024/05/28/sovremennye-tendenczii-blagoustrojstva-parkov-v-kazahstane'		=> '/',
				'/blog/2024/05/28/trendy-blagoustrojstva-dvorovyh-territorij-v-kazahstane'			=> '/',
				'/blog/2024/05/28/primenenie-maf-v-blagoustrojstve-zhilyh-kvartalov-kazahstana'		=> '/',

				'/product/skamejka-zmejka-snake'				=> '/product-category/ulichnye-skamejki',
				'/product/skamejka-zmejka-snake-s-podsvetkoj'	=> '/product-category/ulichnye-skamejki',

				'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-1' => '/product/kacheli-delta-ring-1',
				'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-2' => '/product/kacheli-delta-ring-2',
				'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-3' => '/product/kacheli-delta-ring-3',
				'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-4' => '/product/kacheli-delta-ring-4',
				'/product/svetyashhiesya-kacheli-ring-s-konstrukcziej-5' => '/product/kacheli-delta-ring-5',
				'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-1' => '/product/kacheli-delta-solo-1',
				'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-2' => '/product/kacheli-delta-solo-2',
				'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-3' => '/product/kacheli-delta-solo-3',
				'/product/svetyashhiesya-kacheli-sota-s-konstrukcziej-1' => '/product/kacheli-delta-sota-1',
				'/product/svetyashhiesya-kacheli-solo-s-konstrukcziej-2-2' => '/product/kacheli-delta-sota-2',
				'/product/svetyashhiesya-kacheli-sota-s-konstrukcziej-3' => '/product/kacheli-delta-sota-3',

				'/product/kashpo-na-podstavke-gardap-s'		=> '/product-category/kashpo-s-podsvetkoj',
				'/product/kashpo-na-podstavke-gardap-m'		=> '/product-category/kashpo-s-podsvetkoj',
				'/product/kashpo-na-podstavke-gardap-l'		=> '/product-category/kashpo-s-podsvetkoj',
				'/product/kashpo-na-podstavke-spherep-s'	=> '/product-category/kashpo-s-podsvetkoj',
				'/product/kashpo-na-podstavke-spherep-m'	=> '/product-category/kashpo-s-podsvetkoj',

				'/product/skamejka-eco-1'	=> '/product/skamejka-eco-1-s/',
				'/product/skamejka-eco-2'	=> '/product/skamejka-eco-1-m/',
				'/product/skamejka-eco-3'	=> '/product/skamejka-eco-1-l/',
				'/product/skamejka-eco-4'	=> '/product/skamejka-eco-2-s/',
				'/product/skamejka-eco-5'	=> '/product/skamejka-eco-2-m/',
				'/product/skamejka-eco-6'	=> '/product/skamejka-eco-2-l/',
				'/product/skamejka-eco-7'	=> '/product/skamejka-eco-3-s/',
				'/product/skamejka-eco-8'	=> '/product/skamejka-eco-3-m/',
				// '/product/skamejka-eco-9'	=> '/product/skamejka-eco-3-l/',
				'/product/skamejka-eco-14'	=> '/product/skamejka-eco-4-s/',
				'/product/skamejka-eco-15'	=> '/product/skamejka-eco-4-m/',
				'/product/skamejka-eco-16'	=> '/product/skamejka-eco-4-l/',
				'/product/skamejka-eco-17'	=> '/product/skamejka-eco-5-s/',
				'/product/skamejka-eco-18'	=> '/product/skamejka-eco-5-m/',
				'/product/skamejka-eco-19'	=> '/product/skamejka-eco-5-l/',
				'/product/skamejka-eco-20'	=> '/product/skamejka-eco-6-s/',
				'/product/skamejka-eco-21'	=> '/product/skamejka-eco-6-m/',
				'/product/skamejka-eco-22'	=> '/product/skamejka-eco-6-l/',
				'/product/skamejka-eco-23'	=> '/product/skamejka-eco-7-s/',
				'/product/skamejka-eco-24'	=> '/product/skamejka-eco-7-m/',
				'/product/skamejka-eco-25'	=> '/product/skamejka-eco-7-l/',
				// '/product/skamejka-eco-10'	=> '/product/skamejka-eco-9',
				// '/product/skamejka-eco-11'	=> '/product/skamejka-eco-10',
				'/product/skamejka-boat-1'	=> '/product/skamejka-eco-11/',
			];
		}
	}


	$request		= rtrim($_SERVER['REQUEST_URI'], ' /');
	$redirect_url	= (isset($redirects[$request])) ? $redirects[$request] : $request;

	if ($redirect_url != $request && $is_redirect) {
		wp_redirect(get_home_url() . str_replace('//', '/', $redirect_url), 301);
		exit();
	} else {
		return;
	}
}

/**
 * Проверка изменения uri поста
 *
 * @param [type] $data
 * @param [type] $postarr
 * @return array
 */
function theplugin_has_change_uri_post($data, $postarr, $unsanitized_postarr, $update)
{

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $data;
	if (!current_user_can('edit_post', $postarr['ID'])) return $data;

	if (!isset($postarr['post_name'])) {
		return $data;
	}

	if (get_page_uri($postarr['ID']) != $postarr['post_name'] && $postarr['post_status'] == 'publish' && !in_array($postarr['post_type'], array('product_variation'))) {

		$base_url = '/';

		if ($postarr['post_type'] == 'product') $base_url .= 'product/';

		$redirect_old = $base_url . get_page_uri($postarr['ID']);
		$redirect_new = $base_url . $postarr['post_name'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'theplugin_redirects';
		$wpdb->insert(
			$table_name,
			array(
				'create_time' 		=> current_time('Y-m-d H:i:s'),
				'request_uri_old' 	=> $redirect_old,
				'request_uri_new' 	=> $redirect_new
			)
		);
	}

	return $data;
}


/**
 * Проверка изменения uri таксономии
 *
 * @param [type] $data
 * @param [type] $term_id
 * @param [type] $taxonomy
 * @param [type] $args
 * @return array
 */
function theplugin_has_change_uri_taxonomy($data, $term_id, $taxonomy, $args)
{
	$save = false;
	switch ($taxonomy) {
		case 'nav_menu':
			return $data;
			break;
		case 'product_cat':
			$base_url = '/product-category/';
			$save = true;
			break;
		default:
			$base_url = '/';

			$msg = sprintf(
				'<h3>$args</h3>%s<br><br><h3>$data</h3>%s<br><br>%s',
				theplugin_get_dump_return($args),
				theplugin_get_dump_return($data),
				get_term_link((int) $term_id, $taxonomy)
			);
			break;
	}

	$redirect_old = rtrim(str_replace(site_url(), '', get_term_link((int) $term_id, $taxonomy)), ' /');
	$redirect_new = $base_url . $data['slug'];

	if ($redirect_old != $redirect_new && $save) {
		global $wpdb;
		$table = $wpdb->prefix . 'theplugin_redirects';
		$wpdb->insert(
			$table,
			array(
				'create_time' 		=> date('Y-m-d H:i:s'),
				'request_uri_old' 	=> $redirect_old,
				'request_uri_new' 	=> $redirect_new
			)
		);
	}

	return $data;
}
