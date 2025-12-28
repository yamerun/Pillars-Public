<?php

defined('ABSPATH') || exit;


add_action('admin_init',  'theplugin_dashboard_fields');
add_action('admin_init',  'theplugin_dashboard_additional_fields');

function theplugin_dashboard_fields()
{
	/**
	 * Секции
	 */
	// Контакты
	add_settings_section(
		'tp_section_customize_id',	// ID секции, пригодится ниже
		'Стилизация',					// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	// Контакты
	add_settings_section(
		'tp_section_contacts_id',	// ID секции, пригодится ниже
		'Контакты',					// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	// Обратная связь
	add_settings_section(
		'tp_section_feedback_id',	// ID секции, пригодится ниже
		'Обратная связь',					// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	// Элементы контента
	add_settings_section(
		'tp_section_contents_id',	// ID секции, пригодится ниже
		'Элементы контента',				// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	// WP Mail SMTP
	add_settings_section(
		'tp_section_wp_mail_smtp_id',	// ID секции, пригодится ниже
		'Настройка WP Mail SMTP',				// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	// Коды метрики
	add_settings_section(
		'tp_section_counters_id',	// ID секции, пригодится ниже
		'Коды метрики',				// заголовок (не обязательно)
		'',							// функция для вывода HTML секции (необязательно)
		'theplugin_settings'		// ярлык страницы
	);

	/**
	 * Опция `tp_theme_mods`
	 * аналогично опции theme_mods_{имя_темы}
	 */
	// регистрируем опцию
	register_setting(
		'theplugin_manage_settings',	// название настроек из предыдущего шага
		'tp_theme_mods',				// ярлык опции
	);

	// Текст копирайта
	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'information_copyright',
		'field_label'		=> 'Текст копирайта:',
		'section_id'		=> 'tp_section_customize_id'
	));

	// Время отсчёта прав
	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'information_copyright_year',
		'field_label'		=> 'Время отсчёта прав:',
		'section_id'		=> 'tp_section_customize_id'
	));

	// Theme color
	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'tabs_theme_color',
		'field_label'		=> 'Цвет таба браузера:',
		'section_id'		=> 'tp_section_customize_id',
		'args'				=> array('type' => 'color')
	));

	// добавление поля
	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_phone_1',
		'field_label'		=> 'Основной телефон:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_phone_1_descript',
		'field_label'		=> 'Основной телефон описание:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_phone_2',
		'field_label'		=> 'Дополнительный телефон:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_phone_2_descript',
		'field_label'		=> 'Допол. телефон описание:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_email',
		'field_label'		=> 'Email:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_address_1',
		'field_label'		=> 'Адрес строка #1:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_address_2',
		'field_label'		=> 'Адрес строка #2:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_work_1',
		'field_label'		=> 'Время работы #1:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'contacts_work_2',
		'field_label'		=> 'Время работы #2:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'social_links',
		'field_label'		=> 'Ссылки на соцсети:',
		'filed_callback'	=> 'theplugin_dashboard_fields_customize_textarea',
		'args'				=> array('description' => 'Шорткод [tp-social-links]')
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'yandex_map_company',
		'field_label'		=> 'Код компании в Яндекс.Картах:',
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'logo_footer_desc',
		'field_label'		=> 'Описание под лого в подвале:',
		'filed_callback'	=> 'theplugin_dashboard_fields_customize_textarea',
		'section_id'		=> 'tp_section_contents_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'manager_mail_feedback',
		'field_label'		=> 'Email для обратной связи:',
		'section_id'		=> 'tp_section_feedback_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'yandex_smartcaptcha_key',
		'field_label'		=> 'Публичный ключ Yandex SmartCaptcha:',
		'section_id'		=> 'tp_section_feedback_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'yandex_smartcaptcha_secret',
		'field_label'		=> 'Приватный ключ Yandex SmartCaptcha:',
		'section_id'		=> 'tp_section_feedback_id'
	));

	/**
	 * Опции для WP Mail SMTP
	 */
	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_auth',
		'field_label'		=> 'Auth:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_host',
		'field_label'		=> 'Host:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_port',
		'field_label'		=> 'Port:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_username',
		'field_label'		=> 'Username:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_password',
		'field_label'		=> 'Password:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id',
		'args'				=> ['type' => 'password']
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_from',
		'field_label'		=> 'From:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	theplugin_dashboard_fields_customize(array(
		'field_id'			=> 'wp_mail_smpt_fromname',
		'field_label'		=> 'From Name:',
		'section_id'		=> 'tp_section_wp_mail_smtp_id'
	));

	// 'tp_section_wp_mail_smtp_id'

	/**
	 * Опция `tp_counters_code_footer_id`
	 */
	// регистрируем опцию
	register_setting(
		'theplugin_manage_settings',	// название настроек из предыдущего "шага"
		'tp_counters_code_head_id',		// ярлык опции
		array(
			'sanitize_callback'	=> 'esc_html',	// функция очистки
			'default'			=> null
		)
	);

	// добавление поля
	add_settings_field(
		'tp_counters_code_head_id',
		esc_html('В тегах <head></head>'),
		'theplugin_dashboard_fields_textarea', // название функции для вывода
		'theplugin_settings', // ярлык страницы
		'tp_section_counters_id', // ID секции, куда добавляем опцию
		array(
			'label_for'	=> 'tp_counters_code_head_id',
			'class'		=> 'tp-class', // для элемента <tr>
			'name'		=> 'tp_counters_code_head_id', // любые доп параметры в колбэк функцию
		)
	);

	/**
	 * Опция `tp_counters_code_footer_id`
	 */
	// регистрируем опцию
	register_setting(
		'theplugin_manage_settings',	// название настроек из предыдущего шага
		'tp_counters_code_footer_id',			// ярлык опции
		array(
			'sanitize_callback'	=> 'esc_html',	// функция очистки
			'default'			=> null
		)
	);

	// добавление поля
	add_settings_field(
		'tp_counters_code_footer_id',
		esc_html('После тега </footer>'),
		'theplugin_dashboard_fields_textarea', // название функции для вывода
		'theplugin_settings', // ярлык страницы
		'tp_section_counters_id', // ID секции, куда добавляем опцию
		array(
			'label_for'	=> 'tp_counters_code_footer_id',
			'class'		=> 'tp-class', // для элемента <tr>
			'name'		=> 'tp_counters_code_footer_id', // любые доп параметры в колбэк функцию
		)
	);
}

/**
 * Undocumented function
 *
 * @param [type] $args
 * @return void
 */
function theplugin_dashboard_fields_customize_text($args)
{
	$defaults = array(
		'type' => 'text'
	);
	$args = wp_parse_args($args, $defaults);

	// получаем значение из базы данных
	$options	= (array) get_option('tp_theme_mods');
	$value		= '';
	if (isset($options[$args['theme_mod']])) {
		$value		= $options[$args['theme_mod']];
	}

	printf(
		'<input type="%s" id="%s" name="%s" value="%s" placeholder="%s" />%s',
		esc_attr($args['type']),
		esc_attr($args['name']),
		esc_attr($args['name']),
		esc_html($value),
		esc_html($args['theme_mod']),
		(isset($args['description'])) ? '<p class="description">' . $args['description'] . '</p>' : ''
	);
}


function theplugin_dashboard_fields_textarea($args)
{
	// получаем значение из базы данных
	$value = get_option($args['name']);

	printf(
		'<textarea id="%s" name="%s" rows="6" cols="60">%s</textarea>',
		esc_attr($args['name']),
		esc_attr($args['name']),
		esc_attr($value)
	);
}

/**
 * Undocumented function
 *
 * @param [type] $args
 * @return void
 */
function theplugin_dashboard_fields_customize_textarea($args)
{
	$defaults = array(
		'type' => 'text'
	);
	$args = wp_parse_args($args, $defaults);

	// получаем значение из базы данных
	$options	= (array) get_option('tp_theme_mods');
	$value		= '';
	if (isset($options[$args['theme_mod']])) {
		$value		= $options[$args['theme_mod']];
	}

	printf(
		'<textarea id="%s" name="%s" rows="6" cols="60">%s</textarea>%s',
		esc_attr($args['name']),
		esc_attr($args['name']),
		esc_attr($value),
		(isset($args['description'])) ? '<p class="description">' . $args['description'] . '</p>' : ''
	);
}

function theplugin_dashboard_fields_customize($args = array())
{
	$defaults = array(
		'field_id'			=> '',
		'field_label'		=> '',
		'filed_callback'	=> 'theplugin_dashboard_fields_customize_text',
		'plugin_uri'		=> 'theplugin_settings',
		'section_id'		=> 'tp_section_contacts_id',
		'args'				=> array()
	);

	$args = wp_parse_args($args, $defaults);

	if ($args['field_id'] && $args['field_label']) {

		// добавление поля
		add_settings_field(
			'tp_theme_mods[' . $args['field_id'] . ']',
			esc_html($args['field_label']),
			$args['filed_callback'],		// название функции для вывода
			$args['plugin_uri'],			// ярлык страницы
			$args['section_id'],			// ID секции, куда добавляем опцию
			wp_parse_args(array(
				'label_for'	=> 'tp_theme_mods[' . $args['field_id'] . ']',
				'class'		=> 'tp-class', // для элемента <tr>
				'name'		=> 'tp_theme_mods[' . $args['field_id'] . ']', // любые доп параметры в колбэк функцию
				'theme_mod'	=> $args['field_id']
			), $args['args'])
		);
	}
}

/**
 * Дополнительные данные для страницы `Контакты`
 *
 * @return void
 */
function theplugin_dashboard_additional_fields()
{

	$args = [
		'contacts_address_production'			=> 'Адрес производства:',
		'contacts_phone_production'				=> 'Телефон производства:',
		'contacts_phone_production_descript'	=> 'Телефон производства описание:',
		'contacts_email_production'				=> 'Email производства:',

		'contacts_address_supply'			=> 'Адрес Отдела снабжения:',
		'contacts_phone_supply'				=> 'Телефон Отдела снабжения:',
		'contacts_phone_supply_descript'	=> 'Телефон Отдела снабжения описание:',
		'contacts_email_supply'				=> 'Email Отдела снабжения:',

		'contacts_address_hr'			=> 'Адрес Отдела кадров:',
		'contacts_phone_hr'				=> 'Телефон Отдела кадров:',
		'contacts_phone_hr_descript'	=> 'Телефон Отдела кадров описание:',
		'contacts_email_hr'				=> 'Email Отдела кадров:',
	];

	foreach ($args as $key => $label) {
		theplugin_dashboard_fields_customize(array(
			'field_id'			=> $key,
			'field_label'		=> $label,
		));
	}
}
