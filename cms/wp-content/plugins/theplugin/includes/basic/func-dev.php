<?php

/**
 * Отключение/включение кодов метрики в `tp_counters_code_head_id` и `tp_counters_code_footer_id`
 */
add_action('init', function () {
	$name = 'is-deactive-counters';
	if (isset($_GET[$name])) {
		$flag = absint($_GET[$name]);
		if ($flag) {
			setcookie($name, $flag, time() + 365 * 86400, '/', 'pillars.ru');
		} else {
			setcookie($name, 0, time() - 86400, '/', 'pillars.ru');
		}
	}
});

function theplugin_get_dump($val = '', $pre = true)
{
	$tags = array('', '');
	if ($pre === true) {
		$tags = array('<pre>', '</pre>');
	} else if ($pre === false) {
		$tags = array('<!--', '-->');
	}
	echo $tags[0];
	var_dump($val);
	echo  $tags[1];
}

function theplugin_get_dump_return($val, $pre = true)
{
	ob_start();
	theplugin_get_dump($val, $pre);
	return ob_get_clean();
}

/**
 * Сохранение переданной переменной в лог-файл
 *
 * @param [type] $msg
 * @param string $file
 * @param string $dir папка сохранения файла лога, по умолчанию в `THEPLUGIN_DIR`
 * @return bool
 */
function theplugin_get_log($msg = null, $file = '', $dir = '')
{
	if (is_null($msg)) {
		$msg = 'is_NULL';
	}

	if ($msg != '') {
		if (is_array($msg) || is_object($msg)) {
			$msg = theplugin_get_dump_return($msg, '');
		}

		if (!$dir)
			$dir = THEPLUGIN_DIR;

		$filelog 	= (empty($file)) ? $dir . '/theplugin.log' : $dir . '/' . $file;
		$fp 		= fopen($filelog, 'a');

		if (fwrite($fp, '[' . gmdate('Y-m-d H:i:s') . ' UTC] ' . $msg . PHP_EOL)) {
			return true;
		} else {
			return false;
		}
		fclose($fp);
	} else {
		return false;
	}
}

/**
 * Отключение кодов метрики в `tp_counters_code_head_id` и `tp_counters_code_footer_id` при наличии соотвествующего запроса
 *
 * @param bool $active
 * @return bool
 */
function theplugin_is_deactive_counters($active)
{
	$name = 'is-deactive-counters';
	if (isset($_GET[$name]) || isset($_COOKIE[$name]))
		return false;

	return $active;
}
add_filter('theplugin_active_counters', 'theplugin_is_deactive_counters', 20, 1);
