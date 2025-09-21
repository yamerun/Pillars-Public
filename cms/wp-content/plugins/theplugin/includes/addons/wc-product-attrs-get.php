<?php

defined('ABSPATH') || exit;

/**
 * Получение данных из Google-таблицы через csv-формат
 *
 * @param array $args
 * @return void|
 */
function theplugin_google_sheets_get_csv_by_raw($args = array())
{

	$defaults = array(
		'gid'		=> '',
		'sheets'	=> '',
		'range'		=> ''
	);

	$args	= wp_parse_args($args, $defaults);

	if (!$args['gid'] || !$args['sheets'])
		return null;

	$csv = file_get_contents(
		sprintf(
			'https://docs.google.com/spreadsheets/d/%s/export?format=csv&gid=%s%s',
			$args['sheets'],
			$args['gid'],
			($args['range']) ? '&range=' . $args['range'] : ''
		)
	);

	$csv = explode("\r\n", $csv);
	$sheets = array();

	if ($csv) {
		foreach ($csv as $line) {
			if ($line) {
				$sheets[] = str_getcsv($line);
			}
		}
	}

	return $sheets;
}

/**
 * Получение данных продукта по имени, если есть
 *
 * @param string $title
 * @param string $return
 * @return [type]|
 */
function theplugin_get_product_by_title($title = '', $return = 'ID')
{
	$query = new WP_Query(
		[
			'post_type'					=> 'product',
			'title'						=> $title,
			'post_status'				=> 'all',
			'posts_per_page'			=> 1,
			'update_post_term_cache'	=> false,
			'update_post_meta_cache'	=> false,
			'orderby'					=> 'post_date ID',
			'order'						=> 'ASC',
			// get_post like
			'no_found_rows'				=> true,
			'ignore_sticky_posts'		=> true,
		]
	);

	if (!empty($query->post)) {
		$post = $query->post;
		if ($return) {
			return $post->$return;
		} else {
			return $post;
		}
	} else {
		return null;
	}
}

/**
 * Получение и обработка csv-данных для редактирования существующих продуктов по атрибутам
 *
 * @param array $args
 * @param array $attrs массив таксономий для редактирования
 * @return void|array
 */
function theplugin_get_products_from_google_sheets($args = array(), $attrs = array())
{
	if (!$args)
		return null;

	$sheets = theplugin_google_sheets_get_csv_by_raw($args);

	$titles = array_shift($sheets);

	if ($attrs) {
		for ($i = 0; $i < count($titles); $i++) {
			$titles[$i] = strtr($titles[$i], $attrs);
		}
	}

	$ids = array();
	$not_found = array();
	$logs = array();

	$product_data = array();
	foreach ($sheets as $i => $sheet) {
		$sheet[0] = theplugin_wrapper_convert_double_space($sheet[0]);
		$title = trim(strtr($sheet[0], array(
			'Корпус'		=> '',
			'Белый'			=> '',
			'220 вольт'		=> '',
			'12 вольт'		=> '',
			'RGB'			=> '',
			'Аккумулятор'	=> '',
			'на конструкции' => 'с конструкцией'
		)));
		$title = theplugin_wrapper_convert_double_space($title);

		$_title = '';
		$product_id = null;

		$logs[] = "<---->$i<---->";

		$logs[] = 'sheet: ' . $sheet[0];

		if ((mb_stripos($sheet[0], 'Белый') !== false || mb_stripos($sheet[0], 'RGB') !== false) && mb_stripos($sheet[0], 'Светильник') === false && mb_stripos($sheet[0], 'Качели-балансиры') === false) {

			$_title = 'Светящийся ' . mb_strtolower(mb_substr($title, 0, 1)) . mb_substr($title, 1);
			$logs[] = $_title . '?';
			$product_id = theplugin_get_product_by_title($_title);

			if (!$product_id) {
				$_title = str_replace('Светящийся ', 'Светящиеся ', $_title);
				$logs[] = $_title . '?';
				$product_id = theplugin_get_product_by_title($_title);
			}

			if (!$product_id) {
				if (mb_stripos($sheet[0], 'Кашпо') !== false) {
					$_title = strtr($title, array(
						'Кашпо-полусфера' => 'Кашпо-полусфера с подсветкой',
						'Кашпо-конус' => 'Кашпо-конус с подсветкой',
						'Кашпо ' => 'Кашпо с подсветкой '
					));
				} else {
					$_title = $title . ' с подсветкой';
				}
				$logs[] = $_title . '?';
				$product_id = theplugin_get_product_by_title($_title);
			}
		}

		if (is_null($product_id) && mb_stripos($sheet[0], 'Светильник') !== false) {
			$title = strtr($title, array(
				'Светильник ' => 'Декор ',
			));
			$product_id = theplugin_get_product_by_title($title);
			if (is_null($product_id)) {
				$_title = strtr($title, array(
					'Декор ' => 'Декор настольный ',
				));
				$logs[] = $_title . '?';
				$product_id = theplugin_get_product_by_title($_title);
			}
			if (is_null($product_id)) {
				$title .= ' с подсветкой';
				$logs[] = $title . '?';
				$product_id = theplugin_get_product_by_title($title);
			}
			if (is_null($product_id)) {
				$_title .= ' с подсветкой';
				$logs[] = $_title . '?';
				$product_id = theplugin_get_product_by_title($_title);
			}
		}

		if (is_null($product_id)) {
			$product_id = theplugin_get_product_by_title($title);
		} elseif ($_title) {
			$title = $_title;
		}

		$logs[] = $title;

		if ($product_id) {
			if (!isset($product_data[$product_id])) {
				$sheet[0] = $title;
				$product_data[$product_id] = array_combine($titles, $sheet);
			}

			$ids[] = $product_id;
			$logs[] = 'product_id: ' . $product_id;
		} else {
			$not_found[] = $title;
		}

		$logs[] = '<------------------------>';
	}

	return array(
		'logs'			=> $logs,
		'not_found'		=> $not_found,
		'find_ids'		=> $ids,
		'product_data'	=> $product_data
	);
}

/**
 * Преобразование двойных пробелов в одинарные
 *
 * @param string $string
 * @return string
 */
function theplugin_wrapper_convert_double_space($string = '')
{
	$string = trim($string);

	if ($string) {
		while (mb_strpos($string, '  ') !== false) {
			$string = str_replace('  ', ' ', $string);
		}
	}

	return $string;
}

/**
 * Транслитерация строки на основе `transliterator_transliterate`
 *
 * @param string $string
 * @return string
 */
function theplugin_wrapper_slugify($string = '')
{
	if (function_exists('transliterator_transliterate')) {
		$string = strtr(
			transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string),
			array('ʹ' => '')
		);
		$string = preg_replace('/[-\s]+/', '-', $string);
		return trim($string, '-');
	}

	return $string;
}

/**
 * Переработка аргументов термина по транлитерации слага
 *
 * @param WP_Term $term
 * @return void
 */
function theplugin_sanitize_title_for_term($term)
{
	$args = array(
		'name'			=> '',
		'slug'			=> '',
		'description'	=> '',
		'parent'		=> '',
		'alias_of'		=> ''
	);
	return array(
		'slug'			=> theplugin_wrapper_slugify($term->name),
	);
}

/**
 * Переработка аргументов термина по
 *
 * @param WP_Term $term
 * @return void
 */
function theplugin_sanitize_weight_for_term($term)
{
	$args = array(
		'name'			=> '',
		'slug'			=> '',
		'description'	=> '',
		'parent'		=> '',
		'alias_of'		=> ''
	);
	return array(
		'slug'			=> strtr($term->name, array('.' => '-', ',' => '-')),
	);
}

function theplugin_sanitize_weight($term)
{
	return strtr($term, array('.' => '-', ',' => '-'));
}

/**
 * Проверка строки на формат ДШВ
 *
 * @param string $value
 * @return boolean|string
 */
function is_gabarity($value = '')
{
	$_value = trim(preg_replace('#[0-9хм]#iu', '', $value));

	if (!$_value && mb_strpos($value, '   ') === false) {
		return true;
	}

	return $_value;
}
