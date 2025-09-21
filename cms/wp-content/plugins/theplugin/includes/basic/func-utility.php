<?php

/**
 * Функция сохранения опции в переданную таблицу
 *
 * @param string $table
 * @param string $option
 * @param array $args
 * @return void
 */
function theplugin_set_option_by($table = '', $option = '', $args = array())
{
	if (empty($table) || empty($option) || empty($args))
		return false;

	global $wpdb;
	$exist = $wpdb->get_row($wpdb->prepare("SELECT option_id FROM $table WHERE option_name = %s", $option));
	// Если опции нет, то сохраняем
	if (empty($exist)) {
		$result = $wpdb->insert(
			$table,
			array(
				'option_name'   => $option,
				'option_value'  => maybe_serialize($args)
			)
		);

		if (!$result) {
			return false;
		}

		return (int) $wpdb->insert_id;
	} else {
		return $exist;
	}
}

/**
 * Функция получения опции из переданной таблицы
 *
 * @param string $table
 * @param [type] $option
 * @param boolean $default
 * @return string $values
 */
function theplugin_get_option_by($table, $option, $default = false)
{
	if (is_scalar($option)) {
		$option = trim($option);
	}

	if (empty($table) || empty($option))
		return false;

	global $wpdb;

	$suppress	= $wpdb->suppress_errors();
	$row		= $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $table WHERE option_name = %s LIMIT 1", $option));
	$wpdb->suppress_errors($suppress);

	if (is_object($row)) {
		return theplugin_maybe_array($row->option_value);
	} else {
		return $default;
	}
}

/**
 * Функция обновление опции в переданной таблице
 *
 * @param string $table
 * @param [type] $option
 * @param [type] $value
 * @return bool
 */
function theplugin_update_option_by($table, $option, $value)
{
	global $wpdb;

	if (is_scalar($option)) {
		$option = trim($option);
	}

	if (empty($table) || empty($option)) {
		return false;
	}

	$value		= sanitize_option($option, $value);
	$old_value	= theplugin_get_option_by($table, $option);

	/**
	 * Если старое значение совпадает с новым, то не обновляем.
	 */
	if ($value === $old_value || maybe_serialize($value) === maybe_serialize($old_value)) {
		return false;
	}

	if ($old_value !== false) {
		$serialized_value = maybe_serialize($value);
		$result = $wpdb->update(
			$table,
			array('option_value' => $serialized_value),
			array('option_name' => $option)
		);
		if (!$result) {
			return false;
		}
	} else {
		return theplugin_set_option_by($table, $option, $value);
	}

	return true;
}

/**
 * Функция получения опции из переданной таблицы ThePlugin
 *
 * @param [type] $option
 * @param boolean $default
 * @return [type]
 */
function theplugin_get_option($option, $default = false)
{
	global $wpdb;

	$table		= $wpdb->prefix . 'theplugin_options';
	return theplugin_get_option_by($table, $option, $default);
}

/**
 * Функция обновление опции ThePlugin
 *
 * @param [type] $option
 * @param [type] $value
 * @return bool
 */
function theplugin_update_option($option, $value)
{
	global $wpdb;

	$table		= $wpdb->prefix . 'theplugin_options';
	return theplugin_update_option_by($table, $option, $value);
}

/**
 * Получение опции всех параметров данных по типу темы
 *
 * @return array
 */
function theplugin_get_theme_mods()
{
	$mods = get_option('tp_theme_mods');
	if (!$mods) {
		$mods = theplugin_get_option('theme_mods');
	}

	if (false === $mods) {
		$mods = array();
	} else {
		$mods = theplugin_maybe_array($mods, true);
	}

	return $mods;
}

/**
 * Получение параметра данных по типу темы по переданному ключу
 *
 * @param [type] $name
 * @param boolean $default
 * @return [type]
 */
function theplugin_get_theme_mod($name, $default = false)
{
	$mods = theplugin_get_theme_mods();

	if (isset($mods[$name])) {
		return $mods[$name];
	}

	return $default;
}

/**
 * Обнволение параметров в опции `tp_theme_mods`
 *
 * @param [type] $name
 * @param [type] $value
 * @return bool
 */
function theplugin_set_theme_mod($name, $value)
{
	$mods		= theplugin_get_theme_mods();
	$old_value	= isset($mods[$name]) ? $mods[$name] : false;

	/**
	 * Filters the theme modification, or 'theme_mod', value on save.
	 *
	 * The dynamic portion of the hook name, `$name`, refers to the key name
	 * of the modification array. For example, 'header_textcolor', 'header_image',
	 * and so on depending on the theme options.
	 *
	 * @since 3.9.0
	 *
	 * @param mixed $value     The new value of the theme modification.
	 * @param mixed $old_value The current value of the theme modification.
	 */
	$mods[$name] = apply_filters("pre_set_theme_mod_{$name}", $value, $old_value);

	return update_option('tp_theme_mods', $mods);
}

/**
 * Получение email из `theplugin_get_theme_mod` по переданному ключу `manager_mail_ . $type`
 *
 * @param string $type
 * @return string
 */
function theplugin_get_manager_email($type = '')
{
	$mails = theplugin_get_theme_mod('manager_mail_' . $type);
	if ($mails)
		return $mails;

	return get_option('admin_email');
}

/**
 * Undocumented function
 *
 * @param [type] $template_names
 * @param boolean $load
 * @param boolean $require_once
 * @param array $args
 * @param [type] $dir
 * @return void
 */
function theplugin_locate_template($template_names, $load = false, $require_once = true, $args = array(), $dir = THEPLUGIN_DIR)
{
	$located = '';
	foreach ((array) $template_names as $template_name) {
		if (!$template_name) {
			continue;
		}

		if (file_exists($dir . '/' . $template_name)) {
			$located = $dir . '/' . $template_name;
			break;
		}
	}

	if ($load && '' !== $located) {
		load_template($located, $require_once, $args);
	}

	return $located;
}

function theplugin_get_template_part($slug, $name = null, $args = array())
{

	$templates	= array();
	$name		= (string) $name;
	if ('' !== $name) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	if (!theplugin_locate_template($templates, true, false, $args)) {
		return false;
	}
}

/**
 * Вывод содержимого шаблона в папках THE PLUGIN в виде строки
 *
 * @param [type] $slug
 * @param [type] $name
 * @param array $args
 * @return string
 */
function theplugin_get_template_part_return($slug, $name = null, $args = array())
{

	if (!$slug)
		return '';

	ob_start();
	theplugin_get_template_part($slug, $name, $args);
	return ob_get_clean();
}

/**
 * Вывод содержимого шаблона в папках активной темы в виде строки
 *
 * @param [type] $slug
 * @param [type] $name
 * @param array $args
 * @return string
 */
function theplugin_get_template_theme_part_return($slug, $name = null, $args = array())
{
	if (!$slug)
		return '';

	ob_start();
	get_template_part($slug, $name, $args);
	return ob_get_clean();
}

/**
 * Вывод содержимого WC-шаблонов в папках активной темы в виде строки
 *
 * @param [type] $slug
 * @param array $args
 * @return string
 */
function theplugin_get_template_wc_part_return($slug, $args = array())
{
	if (!$slug)
		return '';

	if (!function_exists('wc_get_template'))
		return '';

	ob_start();
	wc_get_template($slug, $args);
	return ob_get_clean();
}

/**
 * Проверка шаблона
 *
 * @param string $template
 * @return bool
 */
function theplugin_is_template($template = '')
{
	if (!$template)
		return false;

	$object_id		= get_queried_object_id();
	if ($object_id) {
		$page_template	= get_page_template_slug($object_id);
		if (strpos($page_template, $template) !== false) {
			return true;
		}
	}

	return false;
}

/**
 * Undocumented function
 *
 * @param string $listmail
 * @param string $subject
 * @param string $msg
 * @param boolean $meta
 * @return void
 */
function theplugin_send_mail($listmail = '', $subject = '', $msg = '', $meta = false)
{
	if (empty($listmail))
		return false;

	if (strpos($listmail, ',') !== false) {
		$mails = explode(',', $listmail);
	} else {
		$mails 		= array();
		$mails[] 	= $listmail;
	}

	$send	= array();
	$count	= 0;

	foreach ($mails as $mail) {
		$mail 		= trim($mail);
		$result		= false;
		if (wp_mail($mail, $subject, $msg, array('content-type: text/html'))) {
			$count++;
			$result = true;
		} else {
			$headers = '';
			if (get_option('admin_email')) {
				$headers  = "From: Mail PHP <" . get_option('admin_email') . ">" . PHP_EOL;
			}
			$headers .= "MIME-Version: 1.0" . PHP_EOL;
			$headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
			$headers .= "Content-Transfer-Encoding: quoted-printable" . PHP_EOL;

			if (mail($mail, get_bloginfo() . ' ' . $subject, $msg, $headers)) {
				$count++;
				$result = true;
			}
		}

		$send[] = array($mail, $result, current_time('Y-m-d H:i:s'));
	}

	// Если отправить вообще не удалось, и нет запроса для мета-данных
	if (empty($count) && !$meta)
		return false;

	// Если есть результаты по отправке или запрос для мета-данных
	if (!empty($send) || $meta)
		return $send;

	return false;
}

/**
 * Обработка переданных параметров для формы на предмет произвольных выводов данных
 *
 * @param array $args
 * @return array
 */
function theplugin_get_form_wrapper_filter_components($args = array())
{
	$defaults = array(
		'form'			=> [],
		'components'	=> [],
		'vars'			=> [],
	);

	$args = wp_parse_args($args, $defaults);

	if ($args['components']) {
		foreach ($args['components'] as $row => $rows) {
			if (is_array($rows) && $rows) {
				foreach ($rows as $i => $items) {

					// Если есть правила произвольного вывода компонентов
					if (isset($items['customize'])) {

						$defaults = array(
							'name'		=> '',
							'func'		=> '',
							'params'	=> [],
						);
						$customize = wp_parse_args($items['customize'], $defaults);

						// Проверяем наличие функции обработки
						$func_filter = $customize['func'];
						if (function_exists($func_filter) && $customize['name']) {
							// Задаём параметр функции обработки для наименования поля формы
							$params['name'] = $customize['name'];
							// Проверяем наличие других параметров для функции обработки
							if ($customize['params'] && is_array($customize['params'])) {
								// Берём значения из переданных параметров, если они есть
								foreach ($customize['params'] as $item) {
									if (isset($args['vars'][$item])) {
										$params[$item] = $args['vars'][$item];
									}
								}
							}

							// Получаем компоненты формы
							$elements = call_user_func($func_filter, $params);

							if ($elements) {
								theplugin_array_insert_after_key($args['components'][$row], $i, $elements);
							}
						}
					}
				}
			}
		}
	}

	if ($args['components']) {
		foreach ($args['components'] as $row => $rows) {
			if (is_array($rows) && $rows) {
				foreach ($rows as $i => $items) {
					if (isset($items['elements']) && is_array($items['elements'])) {
						foreach ($items['elements'] as $name => $item) {
							if (isset($item['options']) && isset($item['customize'])) {
								$func_filter = $item['customize'];
								if (function_exists($func_filter)) {
									$item['options'] = call_user_func($func_filter, $item['options']);
								}
							}

							$args['components'][$row][$i]['elements'][$name] = $item;
						}
					}
				}
			}
		}
	}

	return $args;
}

/**
 * Обработка переданных параметров для формы на предмет произвольных выводов данных
 *
 * @param array $args
 * @return array
 */
function theplugin_get_form_wrapper_filter_components_customize($rule = '')
{
	$options = [];

	if ($rule) {
		if (strpos($rule, '{{') !== false && strpos($rule, '}}') !== false) {
			preg_match('#{{([0-9]+)..([0-9]+)}}#', $rule, $data);
			if ($data) {
				$data[1] = absint($data[1]);
				$data[2] = absint($data[2]);
				if ($data[1] < $data[2]) {
					for ($i = $data[1]; $i <= $data[2]; $i++) {
						$options[$i] = $i;
					}
				} else {
					for ($i = $data[1]; $i >= $data[2]; $i--) {
						$options[$i] = $i;
					}
				}
			}

			preg_match('#{{option:([a-z-_]+)}}#iu', $rule, $data);
			if ($data) {
				$tp_options = theplugin_get_option(strtolower($data[1]));
				if ($tp_options && is_array($tp_options)) {
					$options = $tp_options;
				}
			}
		}
	}

	return $options;
}

/**
 * Получения правил обработки переданных данных при отправки форме
 *
 * @param array $args
 * @return array
 */
function theplugin_get_form_wrapper_post_keys($args = array())
{
	$post_keys = array();

	if ($args) {
		foreach ($args as $rows) {
			if (is_array($rows) && $rows) {
				foreach ($rows as $items) {
					if (isset($items['elements']) && is_array($items['elements'])) {
						foreach ($items['elements'] as $name => $item) {
							if (!in_array($item['type'], ['tags'])) {

								if (isset($item['valid'])) {
									$valid = $item['valid'];
								} else {
									$valid = strtr(
										$item['type'],
										[
											'textarea'	=> 'text',
											'select'	=> 'text',
											'checkbox'	=> 'text',
											'radio'		=> 'text',
										]
									);
								}

								// Добавляем ключ `label` для элементов типа `hidden`
								if (!isset($item['label'])) {
									$item['label'] = '';
								}

								$post_keys[$name] = array(
									$item['label'],
									$valid,
									(isset($item['required'])) ? $item['required'] : false,
								);
							}
						}
					}
				}
			}
		}
	}

	return $post_keys;
}

/**
 * Расширение прав доступа группы пользователей $role на типы постов $posts
 *
 * @param array $posts
 * @param string $role
 * @return void
 */
function theplugin_set_capability_by_custom_post_type($posts = array(), $role = 'editor')
{
	if (empty($posts))
		return '';

	if (is_scalar($posts)) {
		$posts = array($posts);
	}

	// Add new role for $role
	$role_wp = get_role($role);
	foreach ($posts as $cap_type) {
		$role_wp->add_cap('edit_' . $cap_type);
		$role_wp->add_cap('read_' . $cap_type);
		$role_wp->add_cap('delete_' . $cap_type);
		$role_wp->add_cap('edit_' . $cap_type . 's');
		$role_wp->add_cap('edit_others_' . $cap_type . 's');
		$role_wp->add_cap('publish_' . $cap_type . 's');
		$role_wp->add_cap('edit_published_' . $cap_type . 's');
		$role_wp->add_cap('edit_private_' . $cap_type . 's');
		$role_wp->add_cap('delete_' . $cap_type . 's');
		$role_wp->add_cap('delete_others_' . $cap_type . 's');
		$role_wp->add_cap('delete_published_' . $cap_type . 's');
		$role_wp->add_cap('delete_private_' . $cap_type . 's');
		$role_wp->add_cap('read_private_' . $cap_type . 's');
	}
}


/**
 * Функция определения типа устройства клиентского браузера
 * @source https://wp-kama.ru/function/wp_is_mobile
 *
 * @return bool
 */
function theplugin_is_mobile()
{

	if (!isset($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}

	/**
	 * Флаг для отключения проверки на мобильное устройство
	 */
	if (isset($_GET['unmobile'])) {
		return false;
	}

	$useragent = $_SERVER['HTTP_USER_AGENT'];
	if (
		// добавить '|android|ipad|playbook|silk' в первую регулярку для определения еще и tablet
		preg_match(
			'/(android|bb\d+|meego).+mobile|android|ipad|playbook|silk|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',
			$useragent
		)
		||
		preg_match(
			'/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
			substr($useragent, 0, 4)
		)
	)
		return true;
	return false;
}

/**
 * Обновление даты изменения поста по текущему времени
 *
 * @param [type] $post_id
 * @return int
 */
function theplugin_update_postdate($post_id)
{
	if (empty($post_id))
		return 0;

	$time = current_time('Y-m-d H:i:s');
	return wp_update_post(
		array(
			'ID'				=> $post_id, // ID записи для обновления
			'post_modified'		=> $time,
			'post_modified_gmt'	=> get_gmt_from_date($time)
		)
	);
}

/**
 * Обновление даты изменения поста и его родительского поста по текущему времени
 *
 * @param [type] $post_id
 * @return int
 */
function theplugin_update_postdate_parent($post_id)
{
	if (theplugin_update_postdate($post_id)) {

		// Получаем список родительских постов
		$ancestors = get_post_ancestors($post_id);
		if (!empty($ancestors)) {
			// Обновляем первый родительский пост
			$post_id = theplugin_update_postdate($ancestors[0]);
		}

		return $post_id;
	}

	return 0;
}

/**
 * Фильтр поиска записей только по заголовкам `post_title`
 *
 * @param [type] $search
 * @param [type] $wp_query
 * @return string
 */
function theplugin_search_by_title_only($search, $wp_query)
{
	global $wpdb;

	// skip processing - no search term in query
	if (empty($search))
		return $search;

	$q = $wp_query->query_vars;
	$n = !empty($q['exact']) ? '' : '%';
	$search = $searchand = '';
	foreach ((array) $q['search_terms'] as $term) {
		$term = $wpdb->esc_like($term);
		$search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
		$searchand = ' AND ';
	}

	if (!empty($search)) {
		$search = " AND ({$search}) ";
		if (!is_user_logged_in())
			$search .= " AND ($wpdb->posts.post_password = '') ";
	}

	return $search;
}

/**
 * Проверяем авторизацию пользователя на права админа/редактора
 *
 * @return bool
 */
function theplugin_is_redactor()
{
	$user = wp_get_current_user();
	if ($user->exists()) {
		if (current_user_can('administrator') || current_user_can('editor')) {
			return true;
		}
	}
	return false;
}
