<?php

defined('ABSPATH') || exit;

add_filter('theplugin_active_counters', 'theplugin_is_deactive_counters', 20, 1);

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

/**
 * Редактирование текстовых файлов БД заменой строчек с учётом сериализированных данных
 *
 * @param array $files
 * @param array $replaces
 * @param integer $limit
 * @return array|bool
 */
function theplugin_replace_in_db($files = [], $replaces = [], $limit = 0)
{
	if (!$files || !is_array($files))
		return false;

	// Проверка существования файла БД
	$db = $files[0];
	if (!file_exists($db))
		return false;

	// Удаляем существующий файл импорта отредактированных данных БД
	$filelog = $files[1];
	if (file_exists($filelog))
		unlink($filelog);

	$fileObj	= new SplFileObject($db);
	$logs		= [];

	foreach ($fileObj as $k => $line) {
		foreach ($replaces as $old => $new) {
			// Формируем паттерн сериализированных строчек
			$pattern = sprintf('#s:([0-9]{1,}):\\\"%s#iu', wp_slash($old));
			preg_match_all($pattern, $line, $matches);
			if (count($matches[0])) {

				$logs[$k]['matches'] = $matches;

				foreach ($matches[1] as $m => $count) {
					$count		+= mb_strlen($new) - mb_strlen($old);	// Изменяем количество символов в сериализированных данных
					$pattern	= sprintf('s:%d:\"%s', $count, wp_slash($new));
					$line		= str_replace($matches[0][$m], $pattern, $line);

					$logs[$k]['line'][$m] = $line;
				}
			}
		}

		$line = strtr($line, $replaces);

		$fp = fopen($filelog, 'a');
		fwrite($fp, $line);
		fclose($fp);

		if ($limit <= $k && $limit)
			break;
	}

	return $logs;
}
