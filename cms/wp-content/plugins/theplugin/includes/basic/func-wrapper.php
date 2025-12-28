<?php

/**
 * Подключение файлов стилей в зависимости от платформы браузера
 *
 * @param string $file
 * @return string
 */
function theplugin_setup_file_by_theme($file = '')
{
	// Если переменная файла задана массива
	if (is_array($file)) {
		// Если это мобильный user-agent и есть стиль для него
		if (theplugin_is_mobile() && isset($file['mobile'])) {
			$file = $file['mobile'];
		} else if ($file['pc']) {
			// Если есть ПК-версия стиля
			$file = $file['pc'];
		} else {
			$file = '';
		}
	}

	return $file;
}

/**
 * Регистрация файла стилей для вставки через `wp_enqueue_style`
 *
 * @param string|array $file если задать массивом, то включится проверка на мобильный user-agent
 * @param string $id
 * @param bool $enqueu флаг подключения скрипта через wp_enqueue_style, по умолчанию да
 * @return void
 */
function theplugin_register_css_by_theme($file = '', $id = '', $enqueue = true)
{
	if (!$file)
		return;

	$file = theplugin_setup_file_by_theme($file);

	wp_register_style($id, get_template_directory_uri() . '/' . ltrim($file, '/'), array(), true);
	if ($enqueue && $file) {
		wp_enqueue_style($id);
	}
}

/**
 * Получение файла стилей для html-inline
 *
 * @param string $file
 * @return string
 */
function theplugin_file_get_content_css_by_theme($file = '')
{
	if (empty($file))
		return '';

	$file		= theplugin_setup_file_by_theme($file);
	$file_path	= get_stylesheet_directory() . '/' . ltrim($file, '/');

	if (file_exists($file_path)) {
		$uri_path = [
			'../fonts' => get_template_directory_uri() . '/assets/fonts',
			'../images' => get_template_directory_uri() . '/assets/images',
			"\n"		=> '',
			"\t"		=> ''
		];
		$file = file_get_contents($file_path);
		return strtr($file, $uri_path);
	}

	return '';
}

/**
 * Вывод файла стилей в html-inline
 *
 * @param string $file
 * @param string $id
 * @return string
 */
function theplugin_file_get_content_css_by_theme_print($file = '', $id = '')
{
	echo sprintf(
		"\t<style %s>%s</style>" . PHP_EOL,
		(!empty($id)) ? 'id="' . $id . '-css"' : '',
		theplugin_file_get_content_css_by_theme($file)
	);
}

/**
 * Встраивание файла стилей в html-inline
 *
 * @param string|array $file если задать массивом, то включится проверка на мобильный user-agent
 * @param string $id
 * @param bool $enqueu флаг подключения скрипта через wp_enqueue_style, по умолчанию да
 * @return void
 */
function theplugin_file_get_content_css_by_theme_inline($file = '', $id = '', $enqueue = true)
{
	if (!$file)
		return;

	// Если переменная файла задана массива
	if (is_array($file)) {
		// Если это мобильный user-agent и есть стиль для него
		if (theplugin_is_mobile() && isset($file['mobile'])) {
			$file = $file['mobile'];
		} else if ($file['pc']) {
			// Если есть ПК-версия стиля
			$file = $file['pc'];
		} else {
			$file = '';
		}
	}

	wp_register_style($id, false, array(), true);
	$content = wp_add_inline_style($id, theplugin_file_get_content_css_by_theme($file));
	if ($enqueue && $content) {
		wp_enqueue_style($id);
	}
}

/**
 * Получение файла скриптов для html-inline
 *
 * @param string $file
 * @return string
 */
function theplugin_file_get_content_js_by_theme($file = '')
{
	if (empty($file))
		return '';

	$file_path = get_stylesheet_directory() . '/' . ltrim($file, '/');

	if (file_exists($file_path)) {
		$file = file_get_contents($file_path);
		return $file;
	}

	return '';
}

/**
 * Встраивание файла стилей в html-inline
 *
 * @param string $file
 * @param string $id
 * @param bool $enqueu флаг подключения скрипта через wp_enqueue_script, по умолчанию да
 * @return void
 */
function theplugin_file_get_content_js_by_theme_inline($file = '', $id = '', $enqueue = true)
{
	wp_register_script($id, false, array(), null, false);
	wp_add_inline_script($id, theplugin_file_get_content_js_by_theme($file));
	if ($enqueue) {
		wp_enqueue_script($id);
	}
}

function theplugin_get_phone_theme_mod($phone = '', $wrapper = '', $class = '')
{

	if (empty($phone))
		return '';

	switch ($wrapper) {
		case ' ':
			$wrapper = array(' (', ')');
			break;
		case 'br':
			$wrapper = array('<br>(', ')');
			break;
		case 'span':
			$wrapper = array('<span>', '</span>');
			break;
		case 'span-br':
			$wrapper = array('</a><span><br>', '</span>');
			break;
		case 'div':
			$wrapper = array('</a><div>', '</div>');
			break;
		case 'link':
			$wrapper = array('link');
			break;
		case 'text':
			$wrapper = array('text');
			break;
		case 'whatsapp':
			$wrapper = array('whatsapp');
			break;
		case 'whatsapp-br':
			$wrapper = array('whatsapp-br');
			break;
		default:
			$wrapper = array('', '');
			break;
	}

	if (theplugin_get_theme_mod($phone) != '') {

		$phone_num = preg_replace('#[^0-9\+]#', '', theplugin_get_theme_mod($phone));
		if (mb_substr($phone_num, 0, 1) == '8') {
			$phone_num = '+7' . mb_substr($phone_num, 1);
		}

		// Если требуется вернуть только номер телефона
		if ($wrapper[0] == 'link')
			return $phone_num;

		// Если требуется вернуть только номер телефона
		if ($wrapper[0] == 'text')
			return theplugin_get_theme_mod($phone);

		if (mb_strpos($wrapper[0], 'whatsapp') !== false) {
			return sprintf(
				'<a href="https://wa.me/%s" target=_blank>%s</a>%s',
				preg_replace('#[^0-9]#', '', $phone_num),
				theplugin_get_theme_mod($phone),
				(theplugin_get_theme_mod($phone . '_descript') != '' && $wrapper[0] == 'whatsapp-br') ? '<br>' . theplugin_get_theme_mod($phone . '_descript') : ''
			);
		}

		if (theplugin_get_theme_mod($phone . '_descript') != '' && !empty($wrapper[0])) {
			$descript = sprintf('%s%s%s', $wrapper[0], theplugin_get_theme_mod($phone . '_descript'), $wrapper[1]);
		} else {
			$descript = '';
		}

		return sprintf(
			'<a href="tel:%s"%s>%s%s',
			$phone_num,
			(empty($class)) ? '' : ' class="' . $class . '"',
			theplugin_get_theme_mod($phone),
			(mb_strpos($descript, '</a>') === false) ? '</a>' : ''
		);
	}

	return '';
}

function theplugin_get_phone_pattern($phone = '', $mask = '(%s) %s %s')
{
	$phone = preg_replace('#[^0-9\+]#', '', $phone);
	$phone = strtr($phone, array('+8' => '+7'));

	// Если номер начинается на 8, то переводим на +7
	if (mb_substr($phone, 0, 1) == '8') {
		$phone = '+7' . mb_substr($phone, 1);
	}

	// Если номер начинается не на +7, то сохраняем префикс
	if (mb_substr($phone, 0, 2) != '+7' && mb_strpos($phone, '+') === 0) {
		$prefix = mb_substr($phone, 0, 2) . ' ';
		// Проеряем наличие в маске +7, для вставки другого префикса
		if (mb_strpos($mask, '+7') === 0) {
			$mask = trim(mb_substr($mask, 2));
		}
	} else {
		$prefix = '';
	}

	return $prefix . sprintf(
		$mask,
		substr($phone, 2, 3),
		substr($phone, 5, 3),
		substr($phone, 8)
	);
}

function theplugin_get_phone_text($phone = '', $pre = true)
{
	if (empty($phone))
		return '';

	$phone = preg_replace('#[^0-9\+]#', '', $phone);
	if ((mb_substr($phone, 0, 2) == '+7' && $pre) || mb_substr($phone, 0, 2) == '+8') {
		$phone = '8' . mb_substr($phone, 2);
	}
	return $phone;
}

/**
 * Функция преобразования номмера телевона в вид +7XXXXXXXXXX
 *
 * @param string $phone
 * @return string
 */
function theplugin_set_phone_numeric($phone = '')
{
	$phone = (string) preg_replace('#[^0-9\+]#', '', $phone);

	if (mb_substr($phone, 0, 2) != '+7' && mb_strlen($phone) <= 10) {
		$phone = '+7' . $phone;
	}
	if (mb_substr($phone, 0, 1) == '8' && mb_strlen($phone) > 10) {
		$phone = '+7' . substr($phone, 1);
	}

	return $phone;
}

/**
 * Преобразование переданной строки в формат ФИО-массив
 *
 * @param string $name
 * @return array
 */
function theplugin_set_customer_name($name = '')
{
	// Очищаем имя клиента от двойных пробелов
	$name = preg_replace('/\s+/', ' ', $name);

	if (empty($name))
		return array();

	$args		= array();
	$defaults	= array(
		'first_name'	=> '',
		'last_name'		=> '',
		'middle_name'	=> ''
	);

	// Проверяем наличие пробела для разбивки на ФИО
	if (mb_strpos($name, ' ') !== false) {
		$name 			= explode(' ', $name);
		$args['first_name']	= $name[0];
		unset($name[0]);
		$args['last_name']	= $name[1];
		unset($name[1]);
		if (count($name)) {
			$args['middle_name'] 	= implode(' ', $name);
		}
	} else {
		$args['first_name']	= $name;
	}

	return wp_parse_args($args, $defaults);
}


function theplugin_get_email_wrapper($email = '')
{

	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return sprintf('<a href="mailto:%s">%s</a>', $email, $email);
	} else {
		return '';
	}
}

function theplugin_get_email_theme_mod($email = '')
{

	if (theplugin_get_theme_mod($email) != '') {
		return theplugin_get_email_wrapper(theplugin_get_theme_mod($email));
	}

	return '';
}


function theplugin_get_address_theme_mod($address = 'full', $wrapper = 'text', $class = '')
{

	if (empty(theplugin_get_theme_mod('contacts_address_1')))
		return '';

	switch ($address) {
		case 'full':
			$address = ['stroke_1' => theplugin_get_theme_mod('contacts_address_1'), 'stroke_2' => theplugin_get_theme_mod('contacts_address_2')];
			break;
		case 'stroke_2':
			$address = ['stroke_2' => theplugin_get_theme_mod('contacts_address_2')];
			break;
		case 'stroke_1':
		default:
			$address = ['stroke_1' => theplugin_get_theme_mod('contacts_address_1')];
			break;
	}

	if (count($address) > 1 && !empty($address['stroke_2'])) {
		switch ($wrapper) {
			case ' ':
				$wrapper = array(' (', ')');
				break;
			case 'br':
				$wrapper = array('<br>(', ')');
				break;
			case 'span':
				$wrapper = array('<span>', '</span>');
				break;
			case 'span-br':
				$wrapper = array('<span><br>', '</span>');
				break;
			case 'div':
				$wrapper = array('<div>', '</div>');
				break;
			case 'text':
				$wrapper = array('<br>', '');
				break;
			default:
				$wrapper = array('', '');
				break;
		}

		$get_address = sprintf(
			'%s%s%s%s',
			$address['stroke_1'],
			$wrapper[0],
			$address['stroke_2'],
			$wrapper[1]
		);
	} else {
		$get_address = trim(implode(' ', $address));
	}

	return $get_address;
}

function theplugin_get_address_link_theme_mod($args = [])
{

	$defaults = array(
		'address' 	=> 'full',
		'wrapper'	=> 'text',
		'class'		=> '',
		'link'		=> true
	);

	$args = wp_parse_args($args, $defaults);

	if ($args['link']) {
		return sprintf(
			'<a href="%s">%s</a>',
			theplugin_get_theme_mod('contacts_address_link'),
			theplugin_get_address_theme_mod($args['address'], $args['wrapper'], $args['class'])
		);
	} else {
		return theplugin_get_theme_mod('contacts_address_link');
	}
}

function theplugin_get_permalink_contacts()
{

	$page_id = theplugin_get_theme_mod('contacts_page');
	if (!empty($page_id)) {
		$permalink = get_permalink($page_id);
	}

	if (!empty($permalink)) {
		return $permalink;
	}

	return get_home_url();
}

function theplugin_get_placeholder_cover_mod($post_id = 0, $size = 'medium', $attr = array())
{

	$cover = get_the_post_thumbnail($post_id, $size, $attr);
	if (!empty($cover)) {
		return $cover;
	}

	if (!empty(theplugin_get_theme_mod('cover_placeholder'))) {
		$cover = wp_get_attachment_image(theplugin_get_theme_mod('cover_placeholder'), $size, false, $attr);
		if (!empty($cover)) {
			return $cover;
		}
	}

	return '';
}

/**
 * Удаление из переданной строки знаком табуляции, новой и переноса строки
 *
 * @param string $data
 * @param array $tabs
 * @return string
 */
function theplugin_replace_tab($data = '', $tabs = array("\n", "\r", "\t"))
{
	return str_replace($tabs, '', $data);
}

/**
 * Преобразование массива/объекта в json-строку с установленными флагом
 *
 * @param [type] $data
 * @param [type] $flag
 * @return string
 */
function theplugin_json_encode($data, $flag = JSON_UNESCAPED_UNICODE)
{
	return json_encode($data, $flag);
}

/**
 * Очищает имя или наименование, удаляя небезопасные символы.
 *
 * @param [type] $name
 * @param boolean $strict
 * @return void
 */
function theplugin_sanitize_name($name, $strict = false)
{
	$raw_name	= $name;
	$name		= wp_strip_all_tags($name);
	$name		= remove_accents($name);
	// Remove percent-encoded characters.
	$name		= preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $name);
	// Remove HTML entities.
	$name		= preg_replace('/&.+?;/', '', $name);

	// If strict, reduce to ASCII for max portability.
	if ($strict) {
		$name = preg_replace("#[^a-z0-9а-яё \-']#iu", '', $name);
	}

	$name = trim($name);
	// Consolidate contiguous whitespace.
	$name = preg_replace('|\s+|', ' ', $name);

	return apply_filters('sanitize_name', $name, $raw_name, $strict);
}

/**
 * Получение ID видео с YouTube
 *
 * @param string $url
 * @return string
 */
function theplugin_get_youtube_video_id($url = '')
{
	if (strpos($url, 'youtu.be') !== false || strpos($url, 'youtube.com') !== false) {

		preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $url, $matches);
		return $matches[1];
	}

	return '';
}

/**
 * Преобразование url с YouTube в ссылку для вставки видео через фрейм
 *
 * @param string $url
 * @return string
 */
function theplugin_get_youtube_embed_link($url = '')
{
	$video_id = theplugin_get_youtube_video_id($url);
	if ($video_id) {
		return 'https://www.youtube.com/embed/' . $video_id . '?feature=oembed';
	}

	return '';
}

/**
 * Получение списка ссылок на обложки видео с YouTube
 *
 * @param string $url
 * @return array
 */
function theplugin_get_youtube_covers($url = '')
{
	$covers = array(
		'default'		=> '',
		'hqdefault'		=> '',
		'mqdefault'		=> '',
		'sddefault'		=> '',
		'maxresdefault'	=> '',
	);

	$video_id = theplugin_get_youtube_video_id($url);
	if ($video_id) {
		foreach ($covers as $size => $cover) {
			$url = sprintf('https://img.youtube.com/vi/%s/%s.jpg', $video_id, $size);
			$covers[$size] = $url;
		}
	}

	return $covers;
}

/**
 * Преобразование url с видео-сервисам в ссылку для вставки видео через фрейм
 *
 * @param string $url
 * @param boolean $raw
 * @return string
 */
function theplugin_get_video_embed_link($url = '', $raw = false)
{

	$type = '';
	if (strpos($url, 'vk.com') !== false || strpos($url, 'vkvideo.ru') !== false) {
		$type = 'vk';
	}

	if (strpos($url, 'youtu.be') !== false || strpos($url, 'youtube.com') !== false) {
		$type = 'youtube';
	}

	switch ($type) {
		case 'vk':
			if (preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:(vk\.com|vkvideo\.ru)\/(?:(?:(video-|clip-))))([0-9_]+)/", $url, $matches)) {
				$ids = explode('_', $matches[3]);
				return sprintf('https://vkvideo.ru/video_ext.php?oid=-%s&id=%s&hd=2&autoplay=1&frame=%s', $ids[0], $ids[1], trim($matches[2], ' -'));
			}

			if (strpos($url, 'vk.com/video_ext.php') !== false || strpos($url, 'vkvideo.ru/video_ext.php') !== false) {
				return trim($url);
			}
			break;
		case 'youtube':
			return theplugin_get_youtube_embed_link($url);
			break;
		default:
			return ($raw) ? $url : '';
			break;
	}
}

function theplugin_get_form_data_wrapper($form = [], $post_keys = [])
{

	$msg = '';
	if (!empty($form) && !empty($post_keys)) {
		$str = 0;
		$clr = 1;
		$msg  = '<table width="100%" cellspacing="2" cellpadding="0" border="0">';
		$msg .= '<tr><td width="50%"></td><td width="50%"></td></tr>';
		foreach ($form as $key => $value) {
			if ($str == 0) {
				if ($clr == 0) {
					$color = " ";
				} else {
					$color = ' bgcolor="#f8f8f8" ';
				}
				$msg .= '<tr>' . "\n";
			}
			$msg .= '<td' . $color . 'style="padding:10px;border:#e9e9e9 1px solid;"><strong>' . $post_keys[$key][0] . ':</strong> <span style="float: right;">';
			if (is_array($value)) {
				$msg .= '<ol>';
				for ($i = 0; $i < count($value); $i++) {
					$msg .= '<li><ul>';
					foreach ($value[$i] as $k => $val) {
						$msg .= '<li>' . $post_keys[$k][0] . ': ' . stripslashes($val) . '</li>';
					}
					$msg .= '</ul></li>';
				}
				$msg .= '</ol>';
			} else {
				$msg .= $value;
			}
			$msg .= '</span></td>' . "\n";
			if ($str == 1) {
				$msg .= '</tr>' . "\n";
				$str = 0;
				$clr = ($clr == 0) ? 1 : 0;
			} else {
				$str++;
			}
		}
		$msg .= '</table>';
		unset($str);
		unset($clr);
	}

	return $msg;
}

/**
 * Преобразование даты-времени в переданный формат
 *
 * @param string $format
 * @param string $date
 * @return string
 */
function theplugin_strtotime_date($date = '', $format = 'Y-m-d')
{
	switch ($format) {
		case 'fd':
			$format = 'Y-m-d H:i:s';
			break;
		default:
			break;
	}
	return date($format, strtotime($date));
}

/**
 * Проверка корректности даты
 *
 * @param string $date
 * @return boolean
 */
function theplugin_checkdate($date = '')
{
	$date = theplugin_strtotime_date($date);

	// Validate the date.
	$month	= (int) substr($date, 5, 2);
	$day	= (int) substr($date, 8, 2);
	$year	= (int) substr($date, 0, 4);

	return checkdate($month, $day, $year);
}

/**
 * Проверка и преобразование строки в массив
 *
 * @param string $value
 * @return array|string
 */
function theplugin_maybe_array($value = '')
{
	if (is_scalar($value)) {
		$is_array = json_decode($value, true);
		if (is_array($is_array)) {
			$value = $is_array;
		} elseif (@unserialize($value) !== false) {
			$value = unserialize($value);
		}
	}

	return $value;
}

/**
 * Вставка элемента в массив после определённого элемента
 *
 * @source https://wp-kama.ru/note/add-array-element-to-position
 *
 * @param array   $array         The original array to modify.
 * @param mixed   $key           The key after which the new array should be inserted.
 * @param array   $insert_array  The array to insert into the original array.
 *
 * @return void
 */
function theplugin_array_insert_after_key(&$array, $key, $insert_array)
{

	$index = array_search($key, array_keys($array));

	// key is not found, add to the end of the array
	if ($index === false) {
		$array = array_merge($array, $insert_array);
	}
	// split the array into two parts and insert a new element between them
	else {
		$array = array_merge(
			array_slice($array, 0, $index + 1, true),
			$insert_array,
			array_slice($array, $index + 1, null, true)
		);
	}
}

/**
 * Функция преобразования объекта в массив
 *
 * @param object $data
 * @return array
 */
function theplugin_object_to_array($data)
{
	if (is_array($data) || is_object($data)) {
		$result = array();
		foreach ($data as $key => $value) {
			$result[$key] = theplugin_object_to_array($value);
		}
		return $result;
	}
	return $data;
}

/**
 * Функция вывода длины или общего количества переданной переменной
 *
 * @param [type] $value
 * @return int
 */
function theplugin_get_length_value($value = null)
{
	if (is_array($value) || is_object($value)) {
		return count($value);
	} elseif (is_bool($value)) {
		return 1;
	} elseif (is_scalar($value) && !is_resource($value)) {
		return mb_strlen($value);
	} else {
		return 0;
	}
}

/**
 * Преобразование массива/объекты в строки в запросах шорткодов
 * вид строки "key1:value1,key2:value2,..keyN:valueN,"
 *
 * @param string $value
 * @return array|bool
 */
function theplugin_array_to_args($array = array())
{
	$data = array();

	if (is_array($array) || is_object($array)) {
		foreach ($array as $key => $item) {
			$data[] = sprintf('%s:%s', $key, $item);
		}
	}

	return implode(',', $data);
}


/**
 * Преобразование строки в массив в запросах шорткодов
 * вид строки "key1:value1,key2:value2,..keyN:valueN,"
 *
 * @param string $value
 * @return array|bool
 */
function theplugin_maybe_args($value = '')
{
	if ($value)
		$value .= ',';

	if (strpos($value, ',') !== false && strpos($value, ':') !== false) {
		$args = explode(',', $value);
		$data = array();
		for ($i = 0; $i < count($args); $i++) {
			if (strpos($args[$i], ':') !== false) {
				$args[$i] = explode(':', $args[$i]);
				$data[$args[$i][0]] = $args[$i][1];
			}
		}

		return $data;
	}

	return false;
}

/**
 *
 */
function theplugin_get_shortcode_align($align = '')
{

	switch ($align) {
		case 'center':
			return 'has-text-align-center';
			break;
		case 'right':
			return 'has-text-align-right';
			break;
		case 'left':
			return 'has-text-align-left';
			break;
		default:
			return '';
			break;
	}
}

/**
 * Ссылка на страницу по указанному ID и переданным запросам
 *
 * @param integer $id
 * @param string|array $params
 * @return string|void
 */
function theplugin_get_permalink_by_id($id = 0, $params = '')
{
	if (!empty($params)) {
		if (is_array($params)) {
			$request = [];
			foreach ($params as $key => $value) {
				$request[] = $key . '=' . urlencode($value);
			}
			$params = '?' . implode('&', $request);
		} else {
			$params = '?' . $params;
		}
	}

	$permalink = get_permalink($id);

	if (!empty($permalink)) {
		return $permalink . $params;
	}

	return '';
}

/**
 * Получение html-обёртки переданного списка категорий в виде опций выпадающего списка
 *
 * @param array $list
 * @param string $offset
 * @return string
 */
function theplugin_get_categories_list_wrapper_by_options($list = [], $offset = '')
{
	$wrapper = '';
	foreach ($list as $cat_id => $item) {
		$wrapper .= sprintf(
			'<option value="%d">%s</option>',
			sanitize_key($cat_id),
			esc_attr($offset . $item['title']),
		);
		if (count($item['childs'])) {
			$wrapper .= theplugin_get_categories_list_wrapper_by_options($item['childs'], $offset . '––');
		}
	}

	return $wrapper;
}

/**
 * Округления числа до `significance` разряда в большую сторону
 *
 * @param [type] $number
 * @param integer $significance
 * @return int|bool
 */
function theplugin_ceil($number, $significance = 1)
{
	return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
}

/**
 * Функция-костыль для вывода логотипа для мульти-сайтов
 *
 * @param integer $blog_id
 * @return string
 */
function theplugin_get_custom_logo($blog_id = 0)
{
	if (!is_multisite())
		return get_custom_logo($blog_id);

	$html			= '';
	$switched_blog	= false;

	if (is_multisite() && !empty($blog_id) && get_current_blog_id() !== (int) $blog_id) {
		switch_to_blog($blog_id);
		$switched_blog = true;
	}

	// We have a logo. Logo is go.
	// TODO найти способ, как решить через has_custom_logo()
	if (absint(get_theme_mod('custom_logo'))) {
		$custom_logo_id		= get_theme_mod('custom_logo');
		$custom_logo_attr	= array(
			'class'		=> 'custom-logo',
			'loading'	=> false,
		);

		$unlink_homepage_logo = (bool) get_theme_support('custom-logo', 'unlink-homepage-logo');

		if ($unlink_homepage_logo && is_front_page() && !is_paged()) {
			$custom_logo_attr['alt'] = '';
		} else {
			$image_alt = get_post_meta($custom_logo_id, '_wp_attachment_image_alt', true);
			if (empty($image_alt)) {
				$custom_logo_attr['alt'] = get_bloginfo('name', 'display');
			}
		}

		$custom_logo_attr = apply_filters('get_custom_logo_image_attributes', $custom_logo_attr, $custom_logo_id, $blog_id);
		$image = wp_get_attachment_image($custom_logo_id, 'full', false, $custom_logo_attr);

		// Check that we have a proper HTML img element.
		if ($image) {

			if ($unlink_homepage_logo && is_front_page() && !is_paged()) {
				// If on the home page, don't link the logo to home.
				$html = sprintf(
					'<span class="custom-logo-link">%1$s</span>',
					$image
				);
			} else {
				$aria_current = is_front_page() && !is_paged() ? ' aria-current="page"' : '';

				$html = sprintf(
					'<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>',
					esc_url(home_url('/')),
					$aria_current,
					$image
				);
			}
		}
	} elseif (is_customize_preview()) {
		// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
		$html = sprintf(
			'<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo" alt="" /></a>',
			esc_url(home_url('/'))
		);
	}

	if ($switched_blog) {
		restore_current_blog();
	}

	return apply_filters('get_custom_logo', $html, $blog_id);
}
