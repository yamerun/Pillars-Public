<?php

/**
 * theplugin_get_dump( $val = '', $pre = true )
 * theplugin_get_dump_return( $val = '', $pre = true )
 * theplugin_get_log( $msg = '', $file = '' )
 */
require THEPLUGIN_DIR . '/includes/basic/func-dev.php';

/**
 * theplugin_send_mail( $mails = '', $subject = '', $msg = '' )
 * theplugin_get_template_part_return( $slug, $name = null, $args = array() )
 * theplugin_get_child_pages_list( $args = [], $type = '', $echo = false )
 */
require THEPLUGIN_DIR . '/includes/basic/func-utility.php';

/**
 * theplugin_get_social_list( $args = [] )
 */
require THEPLUGIN_DIR . '/includes/basic/func-social.php';

/**
 * theplugin_get_json_message( $args = [], $unicode = true )
 * theplugin_get_wrapper_notice( $args = [] )
 */
require THEPLUGIN_DIR . '/includes/basic/func-notice.php';

/**
 * theplugin_get_preg_tag( $tags = array(), $stroke = '', $all = false )
 * theplugin_get_content_by_post_id
 */
require THEPLUGIN_DIR . '/includes/basic/func-content.php';

/**
 */
require THEPLUGIN_DIR . '/includes/basic/func-nav.php';

/**
 * theplugin_get_phone_theme_mod( $phone = '', $wrapper = '' )
 * theplugin_get_phone_pattern( $phone = '', $mask = '(%s) %s %s' )
 * theplugin_get_email_wrapper( $email = '' )
 * theplugin_get_email_theme_mod( $email = '' )
 * theplugin_get_address_theme_mod( $address = 'full', $wrapper = 'text', $class = '' )
 * theplugin_get_address_link_theme_mod( $args = [] )
 * theplugin_get_permalink_contacts()
 * theplugin_get_youtube_embed_link( $url = '' )
 * theplugin_get_form_data_wrapper( $form = [], $post_keys = [] )
 */
require THEPLUGIN_DIR . '/includes/basic/func-wrapper.php';

/**
 * theplugin_set_captcha( $captha_id = '' )
 * theplugin_get_captcha( $token )
 * theplugin_set_form_nonce( $name = '_wpnonce', $referer = true, $echo = true )
 */
require THEPLUGIN_DIR . '/includes/basic/func-security.php';

/**
 * Класс проверки валидности данных
 * class THEPLUGIN_Data_Validation
 *
 * public $data_valid
 * public $data_label
 *
 * function __construct( $value )
 * public function set_valid( $value )
 * public function set_label( $label )
 * public function set_sanitize_data( $tags = [] )
 * public function set_raw_data()
 * public function sanitize_html( $value = null, $tags = [] )
 * public function set_date()
 * public function get_data()
 * public function is_data_required( $required = false )
 * public function get_valid_text()
 * public function get_valid_numeric()
 * public function get_valid_email()
 * public function get_valid_phone()
 * public function get_valid_url()
 * public function get_valid_base64()
 * public function get_valid_confirm()
 * public function get_valid_date()
 * private function get_valid_data( $args = [] )
 * private function get_valid_data_func( $args = [] )
 *
 */
require_once THEPLUGIN_DIR . '/includes/basic/class-data-validation.php';

/**
 * Класс загрузки файлов на сервер средствами WP
 * class THEPLUGIN_Upload_File
 */
require_once THEPLUGIN_DIR . '/includes/basic/class-upload-file.php';

/**
 * Класс обработки запросов через WP AJAX
 * abstract class THEPLUGIN_AJAX_Handler
 */
require_once THEPLUGIN_DIR . '/includes/basic/class-ajax-handler.php';

/**
 * Класс вывода шаблона формы
 */
require_once THEPLUGIN_DIR . '/includes/basic/class-form-input.php';
require_once THEPLUGIN_DIR . '/includes/basic/class-form-wrapper.php';
