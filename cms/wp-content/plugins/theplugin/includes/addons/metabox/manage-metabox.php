<?php

defined('ABSPATH') || exit;

/**
 * Получение текущего значение мета-поля
 *
 * @param string $selector
 * @param int $post_id
 * @param boolean $format_value
 * @return [type]
 */
function theplugin_check_get_field($selector, $post_id, $format_value = true)
{
	// ACF Plugin
	if (function_exists('get_field') && false) {
		$field = get_field($selector, $post_id, $format_value);
		if ($field) {
			return $field;
		}
	}

	return get_post_meta($post_id, $selector, $format_value);
}
/**
 *  Проверяем наличие данных в ACF, если есть, то полностью удаляем
 *	Нужно для перевода всех мета-данных из ACF в классические мета-данные
 *
 * @param string $selector
 * @param int $post_id
 * @return void
 */
function theplugin_delete_get_field($selector, $post_id)
{
	// ACF Plugin
	if (function_exists('get_field') && false) {
		$field = get_field($selector, $post_id);
		if ($field) {
			delete_field($selector, $post_id);
		}
	}
}

/**
 * Обёртка для вывода мета-данных
 *
 * @param array $args
 * @return string
 */
function theplugin_get_components_panel($args = [])
{

	$defaults = array(
		'post_id' 		=> 0,
		'post_meta' 	=> '',
		'input_id' 		=> '',
		'input_default'	=> '',
		'input_type'	=> 'text',
		'label' 		=> ''
	);
	$args = wp_parse_args($args, $defaults);

	if (!isset($args['input_value']) || is_null($args['input_value'])) {
		$args['input_value'] = theplugin_check_get_field($args['post_meta'], $args['post_id']);
	}

	ob_start();
	if ($args['input_type'] != 'hidden') { ?>
		<div class="components-panel__row">
			<div class="components-base-control css-wdf2ti-Wrapper">
				<div class="components-base-control__field css-11vcxb9-StyledField">
					<?php switch ($args['input_type']):
							/* CHECKBOX */
						case 'checkbox':
							$ckecked = ($args['input_value'] == $args['input_default'] && !empty($args['input_value'])) ? ' checked' : ''; ?>
							<input id="<?= $args['input_id'] ?>" name="<?= $args['input_id'] ?>" class="components-checkbox-control__inputs" type="checkbox" value="<?= $args['input_default'] ?>" <?= $ckecked ?>>
							<label class="components-checkbox-control__label" for="<?= $args['input_id'] ?>"><?= $args['label'] ?></label>
						<?php break;
							/* SELECT */
						case 'select': ?>
							<label class="components-base-control__label css-pezhm9-StyledLabel e1puf3u2" for="<?= $args['input_id'] ?>"><?= $args['label'] ?></label>
							<select class="components-text-control__input" id="<?= $args['input_id'] ?>" name="<?= $args['input_id'] ?>">
								<?php foreach ($args['input_default'] as $value => $option) : $ckecked = ($args['input_value'] == $value && !empty($args['input_value'])) ? ' selected' : '';
									echo sprintf(
										'<option value="%s"%s>%s</option>',
										$value,
										$ckecked,
										$option
									);
								endforeach; ?>
							</select>
						<?php break;
						case 'info': ?>
							<label class="components-base-control__label css-pezhm9-StyledLabel e1puf3u2" for="<?= $args['input_id'] ?>"><?= $args['label'] ?></label>
							<p id="<?= $args['input_id'] ?>"><?= $args['input_value'] ?></p>
						<?php break;
						case 'link': ?>
							<label class="components-base-control__label css-pezhm9-StyledLabel e1puf3u2" for="<?= $args['input_id'] ?>"><?= $args['label'] ?></label>
							<p id="<?= $args['input_id'] ?>"><a href="<?= $args['input_value'] ?>" target="_blank"><?= $args['input_value'] ?></a></p>
						<?php break;
						default: ?>
							<label class="components-base-control__label css-pezhm9-StyledLabel e1puf3u2" for="<?= $args['input_id'] ?>"><?= $args['label'] ?></label>
							<input class="components-text-control__input" type="<?= $args['input_type'] ?>" id="<?= $args['input_id'] ?>" name="<?= $args['input_id'] ?>" value="<?= $args['input_value'] ?>">
					<?php break;
					endswitch; ?>
				</div>
			</div>
		</div>
	<?php
	} else {
	?>
		<input class="components-text-control__input" type="<?= $args['input_type'] ?>" id="<?= $args['input_id'] ?>" name="<?= $args['input_id'] ?>" value="<?= $args['input_value'] ?>">
<?php }
	return ob_get_clean();
}

/**
 * Поле формы для выбора изображения из Медиа Библиотеки
 * @source https://misha.agency/wordpress/uploader-metabox-option-pages.html
 *
 * @param array $args
 * @return string
 */
function theplugin_get_image_uploader_field($args)
{
	// следующая строчка нужна только для использования на страницах настроек
	// $value = get_option($args['name']);
	// следующая строчка нужна только для использования в мета боксах
	$args = wp_parse_args($args, [
		'value' => 0,
		'name'	=> '',
		'label'	=> 'Добавить изображение',
		'desc'	=> ''
	]);

	if (!$args['name'])
		return '';

	$value		= $args['value'];
	$wp_dir		= wp_get_upload_dir();
	$default	= $wp_dir['baseurl'] . '/placeholder.png';

	if ($value && ($image_attributes = wp_get_attachment_image_src($value, array(300, 300)))) {
		$src = $image_attributes[0];
	} else {
		$src = $default;
	}
	return '
	<div>
		<label for="' . $args['name'] . '">' . $args['label'] . '</label>
		<p class="description">' . $args['desc'] . '</p>
		<img data-src="' . $default . '" src="' . $src . '" width="300" class="tp-wp-image__container" style="max-width:100%;" />
		<div>
			<input type="hidden" name="' . $args['name'] . '" id="' . $args['name'] . '" value="' . $value . '" />
			<button type="submit" class="tp-wp-image-upload__button button">Добавить изображение</button>
			<button type="submit" class="tp-wp-image-remove__button button">×</button>
		</div>
	</div>
	';
}

/**
 * Поле формы для выбора файла из Медиа Библиотеки
 *
 * @param array $args
 * @return string
 */
function theplugin_get_file_uploader_field($args)
{
	// следующая строчка нужна только для использования на страницах настроек
	// $value = get_option($args['name']);
	// следующая строчка нужна только для использования в мета боксах
	$args = wp_parse_args($args, [
		'value' => 0,
		'name'	=> '',
		'label'	=> 'Добавить файл',
		'desc'	=> ''
	]);

	if (!$args['name'])
		return '';

	$value		= $args['value'];
	$wp_dir		= wp_get_upload_dir();

	if ($value && ($file_url = wp_get_attachment_url($value))) {

		// TODO Продумать для мульти-сайта

		$caption	= get_the_title($value);
		$path		= str_replace(get_site_url() . '/', ABSPATH, $file_url);
		$pathinfo	= pathinfo($path);
		$name		= $pathinfo['basename'];
	} else {
		$file_url = '#';
		$caption = '–';
		$name = '–';
	}
	return '
	<div class="tp-wp-file">
		<label for="' . $args['name'] . '">' . $args['label'] . '</label>
		<p class="description">' . $args['desc'] . '</p>
		<div class="tp-wp-file__container">
			<div class="file-icon">
				<img data-name="icon" src="' . site_url() . '/wp-includes/images/media/archive.png" alt="">
			</div>
			<div class="file-info">
				<p><strong data-name="title">' . $caption . '</strong></p>
				<p><strong>Имя файла:</strong><a data-name="filename" href="' . $file_url . '" target="_blank">' . $name . '</a></p>
			</div>
		</div>
		<div>
			<input type="hidden" name="' . $args['name'] . '" id="' . $args['name'] . '" value="' . $value . '" />
			<button type="submit" class="tp-wp-file-upload__button button">Добавить файл</button>
			<button type="submit" class="tp-wp-file-remove__button button">×</button>
		</div>
	</div>
	';
}

/**
 * Сохранения мета-данных по переданному ID поста, его формата и наименование мета-полей
 *
 * @param int $post_id
 * @param string $post_type
 * @param array $data
 * @return void
 */
function theplugin_save_postdata($post_id, $post_type, $data)
{
	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	if (!isset($_POST['theplugin_post_meta_noncename']))
		return;
	if (!wp_verify_nonce($_POST['theplugin_post_meta_noncename'], 'theplugin_post_meta_action'))
		return;

	// если это автосохранение ничего не делаем
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// проверяем права юзера
	if (!current_user_can('edit_post', $post_id))
		return;

	// Проверяем требуемый тип данных
	if (get_post_type($post_id) != $post_type)
		return;

	// Все ОК. Теперь, нужно найти и сохранить данные
	foreach ($data as $key => $postmeta) {
		theplugin_delete_get_field($postmeta, $post_id);

		if (isset($_POST[$key])) {
			$value = sanitize_text_field($_POST[$key]);
			update_post_meta($post_id, $postmeta, $value);
		} else {
			update_post_meta($post_id, $postmeta, '');
		}
	}
}
