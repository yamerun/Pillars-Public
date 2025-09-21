<?php

/**
 * Класс проверки валидности данных
 */
class THEPLUGIN_Data_Validation
{

	public $data_valid;
	public $data_label;

	function __construct($value)
	{
		$this->set_valid($value);
	}

	public function set_valid($value)
	{
		$this->data_valid = $value;
	}

	public function set_label($label)
	{
		$this->data_label = $label;
	}

	/**
	 * Очищение данных от HTML и PHP массива или едичного значения
	 */
	public function set_sanitize_data($tags = [])
	{
		if (is_array($this->data_valid)) {
			foreach ($this->data_valid as $key => $value) {
				$this->data_valid[$key] = $this->sanitize_html($value, $tags);
			}
		} else {
			$this->data_valid = $this->sanitize_html(null, $tags);
		}
	}

	/**
	 * Установка необработанных данных от HTML и PHP массива
	 */
	public function set_raw_data()
	{
		if (is_array($this->data_valid)) {
			foreach ($this->data_valid as $key => $value) {
				$this->data_valid[$key] = $value;
			}
		}
	}

	/**
	 * Очищение данных от HTML и PHP едичного значения
	 */
	public function sanitize_html($value = null, $tags = [])
	{
		if (is_null($value))
			$value = $this->data_valid;

		return wp_unslash(strip_tags(htmlspecialchars_decode(trim($value)), $tags));
	}

	/**
	 * Установка необработанных данных в формате Y-m-d
	 */
	public function set_date()
	{
		return date('Y-m-d', strtotime($this->data_valid));
	}

	/**
	 * Проверка данных на валидность тексту
	 */
	public function get_data()
	{
		return $this->data_valid;
	}

	/**
	 * Проверка данных на валидность обязательного заполнения
	 */
	public function is_data_required($required = false)
	{
		// Если есть условие на обязательное заполнение
		if ($required) {
			if (is_array($this->data_valid)) {
				// Проверяем каждый элемент массива
				foreach ($this->data_valid as $key => $value) {
					if ($value == '') {
						return false;
					}
				}
			} else if ($this->data_valid == '') {
				return false;
			}
		}

		return true;
	}

	/**
	 * Проверка данных на валидность тексту
	 */
	public function get_valid_text()
	{
		return $this->get_valid_data();
	}

	/**
	 * Проверка данных на валидность числам
	 */
	public function get_valid_numeric()
	{
		$args = array(
			'label' => __('Должны быть только цифры', 'theplugin'),
			'type'	=> 'numeric'
		);
		return $this->get_valid_data($args);
	}

	/**
	 * Проверка данных на валидность email
	 */
	public function get_valid_email()
	{
		$args = array(
			'label' => __('Некорректный email', 'theplugin'),
			'type'	=> 'email'
		);
		return $this->get_valid_data($args);
	}

	/**
	 * Проверка данных на валидность телефона
	 */
	public function get_valid_phone()
	{
		$args = array(
			'label' => __('Некорректный телефон', 'theplugin'),
			'type'  => 'phone'
		);
	}

	/**
	 * Проверка данных на валидность url
	 */
	public function get_valid_url()
	{
		$args = array(
			'label' => __('Некорректный url', 'theplugin'),
			'type'  => 'url'
		);
	}

	/**
	 * Проверка данных на валидность base64
	 */
	public function get_valid_base64()
	{
		$args = array(
			'label' => __('Некорректный данные для загрузки', 'theplugin'),
			'type'  => 'base64'
		);
		return $this->get_valid_data($args);
	}

	/**
	 * Проверка данных на валидность checkbox
	 */
	public function get_valid_confirm()
	{
		$args = array(
			'label' => __('Подтвердите условие', 'theplugin'),
			'type'  => 'confirm'
		);
	}

	/**
	 * Проверка данных на валидность date
	 */
	public function get_valid_date()
	{
		$args = array(
			'label' => __('Неверный формат даты', 'theplugin'),
			'type'  => 'date'
		);
	}

	private function get_valid_data($args = [])
	{

		$defaults = array(
			'label' => __('Ошибка формата', 'theplugin'),
			'preg'	=> '',
			'tags'	=> [],
			'type'	=> ''
		);

		$args = wp_parse_args($args, $defaults);

		$errors 	= '';

		if (is_array($this->data_valid)) {
			foreach ($this->data_valid as $key => $value) {
				if ($this->get_valid_data_func(array('type' => $args['type'], 'value' => $this->data_valid[$key]))) {
					$errors .= sprintf(
						'<li>%s <b>%s #%d</b></li>',
						$args['label'],
						$this->data_label,
						($key + 1)
					);
				}
			}
		} else {
			if ($this->get_valid_data_func(array('type' => $args['type'], 'value' => $this->data_valid))) {
				$errors .= sprintf(
					'<li>%s <b>%s</b></li>',
					$args['label'],
					$this->data_label
				);
			}
		}

		return $errors;
	}

	private function get_valid_data_func($args = [])
	{
		$defaults = array(
			'type' 	=> '',
			'value'	=> ''
		);

		$args = wp_parse_args($args, $defaults);

		$invalid = false;
		switch ($args['type']) {
			case 'numeric':
				if ($args['value'] != '' && !is_numeric($args['value'])) {
					$invalid = true;
				}
				break;
			case 'email':
				if ($args['value'] != '' && !filter_var($args['value'], FILTER_VALIDATE_EMAIL)) {
					$invalid = true;
				}
				break;
			case 'phone':
				if ($args['value'] != '' && preg_replace('#-\+\(\)[0-9]\s#', '', $args['value'])) {
					$invalid = true;
				}
				break;
			case 'url':
				$url = strtolower(strtr($args['value'], [
					'https://'	=> '',
					'http://'	=> '',
					'wwww.'		=> ''
				]));

				if (!preg_match('#([а-яё-]+).рф#iu', $url, $matches)) {
					$url = 'https://' . $url;
					if ($args['value'] != '' && !wp_http_validate_url($url)) {
						$invalid = true;
					}
				}
				break;
			case 'base64':
				if ($args['value'] != '' && !base64_decode($args['value'])) {
					$invalid = true;
				}
				break;
			case 'confirm':
				if ($args['value'] != 'on') {
					$invalid = true;
				}
				break;
			case 'date':
				if ($args['value'] != '' && !strtotime($args['value'])) {
					$invalid = true;
				}
				break;
			default:
				if ($args['value'] == '') {
					$invalid = true;
				}
				break;
		}

		return $invalid;
	}
}


/**
 * Класс проверки валидности данных переданных методом POST
 */
class THEPLUGIN_Data_Validation_Form
{

	public $data_keys;
	public $data_request;
	public $data_form;
	public $data_errors;

	function __construct($args, $request = array())
	{
		$this->set_post_keys($args, $request);
	}

	public function set_post_keys($args = [], $request = array())
	{
		if (empty($request) && isset($_POST)) {
			$request = $_POST;
		}

		if (empty($request) || empty($args)) {
			$this->data_keys = [];
			return false;
		}

		$this->data_keys 	= $args;
		$this->data_request = $request;
		$this->data_form	= false;
		$this->data_errors 	= [];
		$this->get_valid_post_keys();
	}

	public function get_valid_post_keys()
	{
		if (empty($this->data_keys)) {
			$this->data_errors = ['title' => 'Проверьте наличие запроса'];
			return false;
		}

		$unvalid_labels	= [];
		foreach ($this->data_request as $key => $value) {
			$form[$key] = new THEPLUGIN_Data_Validation($value);
			$form[$key]->set_label($this->data_keys[$key][0]);
			if (in_array($this->data_keys[$key][1], array('base64'))) {
				$form[$key]->set_raw_data();
			} else if ($this->data_keys[$key][1] == 'date') {
				$form[$key]->set_date();
			} else {
				$form[$key]->set_sanitize_data();
			}
			/* Search for empty data */
			if (!$form[$key]->is_data_required($this->data_keys[$key][2])) {
				$unvalid_labels[] = $this->data_keys[$key][0];
			}
		}

		if (!empty($unvalid_labels)) {
			$this->data_errors = ['title' => 'Проверьте заполнение всех обязательных полей', 'content' => implode(', ', $unvalid_labels)];
			return false;
		}

		$unvalid_labels	= [];
		foreach ($this->data_keys as $key => $data) {
			if (!isset($form[$key])) {
				$unvalid_labels[] = '<li>Нет запроса <b data-key="' . $key . '">' . $data[0] . '</b></li>';
			} else {
				switch ($data[1]) {
					case 'numeric':
						if ($form[$key]->get_valid_numeric() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_numeric();
						}
						break;
					case 'email':
						if ($form[$key]->get_valid_email() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_email();
						}
						break;
					case 'tel':
						if ($form[$key]->get_valid_phone() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_phone();
						}
						break;
					case 'base64':
						if ($form[$key]->get_valid_base64() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_base64();
						}
						break;
					case 'confirm':
						if ($form[$key]->get_valid_confirm() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_confirm();
						}
						break;
					case 'date':
						if ($form[$key]->get_valid_date() != '') {
							$unvalid_labels[] = $form[$key]->get_valid_date();
						}
						break;
					default:
						# code...
						break;
				}
			}
		}


		if (!empty($unvalid_labels)) {
			$this->data_errors = ['title' => 'Ошибки', 'content' => '<p>Исправьте ошибки:</p><ul>' .  implode('', $unvalid_labels) . '</ul>'];
			return false;
		}

		$this->data_form = [];
		foreach ($form as $key => $data) {
			$this->data_form[$key] = $form[$key]->get_data();
		}
	}

	public function get_valid_data()
	{
		return $this->data_form;
	}

	public function errors()
	{
		if (empty($this->data_errors))
			return false;

		return $this->data_errors;
	}
}
