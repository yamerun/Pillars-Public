<?php
defined('ABSPATH') || exit;

/**
 * Класс вывода полей формы по переданным параметрам
 */
class THEPLUGIN_Form_Style
{

	/**
	 * Вывод шаблона неопределённого значения `input[type]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function __get($args = array())
	{
		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `input[type="text"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_text($args = array())
	{
		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `input[type="email"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_email($args = array())
	{
		$args['type'] = 'email';

		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `input[type="search"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_search($args = array())
	{
		$args['type'] = 'search';

		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `input[type="tel"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_tel($args = array())
	{
		$args['type'] = 'tel';
		$args['inputmode'] = 'numeric';
		$args['autocomplete'] = 'off';

		$wrapper = self::get_input($args);
		$wrapper .= '
		<div class="phone-country-code">
			<div class="phone-country-code__container">
				<div class="phone-country-code__flag"></div>
				<div class="phone-country-code__dial">+7</div>
				<div class="phone-country-code__arrow"></div>
			</div>
			<ul class="phone-country-code__list d-none" role="listbox" aria-label="Список стран">
				<li class="phone-country-code__item" tabindex="-1" role="option" data-dial-code="7" data-country-code="ru" aria-selected="true">
					<div class="phone-country-code__flag"></div>
					<span class="phone-country-code__name">Russia (Россия)</span>
					<span class="phone-country-code__item-dial">+7</span>
				</li>
			</ul>
		</div>';

		return $wrapper;
	}

	/**
	 * Вывод шаблона для `input[type="url"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_url($args = array())
	{
		$args['type'] = 'url';
		$args['inputmode']	= 'url';
		$args['type']		= 'text';

		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `input[type="file"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_file($args = array())
	{
		$wrapper	= '';
		$defaults	= array(
			'id'			=> '',
			'name'			=> '',
			'class'			=> 'file-wrapper',
			'required'		=> false,
			'value'			=> '',
			'class-text'	=> 'input-file-text',
			'accept'		=> '.pdf, .doc, .docx, .xls, .xlsx, .txt'
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name']) {
			$wrapper .= sprintf(
				'<label %s><input type="file" name="%s" %s class="input-file" accept="%s"%s><div %s>%s</div>%s<span class="input-message"></span></label>' . PHP_EOL,
				self::has_class($args['class']),
				$args['name'],
				self::is_required($args['required']),
				$args['accept'],
				self::has_placeholder($args['placeholder']),
				self::has_class($args['class-text']),
				($args['placeholder']) ? $args['placeholder'] : $args['label'],
				($args['required']) ? ' <abbr title="' . __('обязательно') . '">*</abbr>' : '',
			);
		}

		return $wrapper;
	}

	/**
	 * Вывод шаблона для `input[type="hidden"]`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_hidden($args = array())
	{
		$args['type'] = 'hidden';
		$args['autocomplete'] = 'off';

		return self::get_input($args);
	}

	/**
	 * Вывод шаблона для `Политики конфидициальности`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_privacy_agree($args = array())
	{
		$wrapper	= '';
		$defaults	= array(
			'id'			=> '',
			'name'			=> '',
			'class'			=> '',
			'required'		=> false,
			'value'			=> '',
			'checked'		=> false,
			'class'			=> 'confirm-wrapper',
			'class-text'	=> 'input-checked-text',
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name']) {
			$wrapper .= sprintf(
				'<label %s><input type="hidden" name="%s" %s %s><span></span><div %s>%s</div></label>' . PHP_EOL,
				self::has_class($args['class']),
				$args['name'],
				self::is_required($args['required']),
				($args['checked']) ? ' value="on"' : '',
				self::has_class($args['class-text']),
				$args['placeholder'],
			);
		}

		return $wrapper;
	}

	/**
	 * Вывод шаблона для `select`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_select($args = array())
	{
		$wrapper	= '';
		$defaults	= array(
			'id'			=> '',
			'name'			=> '',
			'class'			=> '',
			'label'			=> '',
			'required'		=> false,
			'value'			=> '',
			'readonly'		=> false,
			'disabled'		=> false,
			'multiple'		=> false,
			'options'		=> [],
			'default'		=> [
				'key'		=> '',
				'value'		=> '',
				'disabled'	=> false,
			]
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name']) {

			$wrapper .= sprintf(
				'%s<select%s name="%s"%s%s%s%s%s>' . PHP_EOL,
				self::get_label($args),
				self::has_id($args['id']),
				$args['name'],
				self::is_required($args['required']),
				self::has_class($args['class']),
				self::is_readonly($args['readonly']),
				self::is_disabled($args['disabled']),
				self::is_multiple($args['multiple']),
			);

			if (isset($args['default']['value']) && $args['default']['value']) {
				$wrapper .= sprintf(
					'<option value="%s"%s>%s</option>' . PHP_EOL,
					$args['default']['key'],
					self::is_disabled($args['default']['disabled']),
					$args['default']['value'],
				);
			}

			if ($args['options']) {
				// Если значение строка/число, то задаём в массив для множественного выбора
				if (is_scalar($args['value'])) {
					$args['value'] = array($args['value']);
				}

				foreach ($args['options'] as $value => $option) {
					if (is_array($option)) {
						$wrapper .= sprintf('<optgroup label="%s">', $value);
						foreach ($option as $val => $opt) {
							$wrapper .= sprintf(
								'<option value="%s"%s>%s</option>' . PHP_EOL,
								$val,
								(in_array($val, $args['value'])) ? ' selected' : '',
								$opt,
							);
						}
						$wrapper .= '</optgroup>';
					} else {
						$wrapper .= sprintf(
							'<option value="%s"%s>%s</option>' . PHP_EOL,
							$value,
							(in_array($value, $args['value'])) ? ' selected' : '',
							$option,
						);
					}
				}
			}

			$wrapper .= '</select>' . PHP_EOL;
		}

		return $wrapper;
	}


	/**
	 * Вывод шаблона для `checkbox` и `radio`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_checked($args = array())
	{
		$wrapper	= '';
		$defaults	= array(
			'id'			=> '',
			'name'			=> '',
			'class'			=> 'input-checked-wrapper',
			'class-text'	=> 'input-checked-text',
			'label'			=> '',
			'type'			=> 'checkbox',
			'required'		=> false,
			'value'			=> '',
			'disabled'		=> false,
			'options'		=> [],
			'wrapper'		=> ['before' => '', 'after' => '']
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name']) {

			$wrapper .= self::get_label($args);

			if ($args['options']) {
				// Если значение строка/число, то задаём в массив для множественного выбора
				if (is_scalar($args['value'])) {
					$args['value'] = array($args['value']);
				}

				// Если тип `checkbox`, то добавляем к имени `[]` для мультизначений
				if ($args['type'] == 'checkbox') {
					$args['name'] .= '[]';
				}

				foreach ($args['options'] as $value => $option) {
					$wrapper .= sprintf(
						'%s<label class="%s"><input type="%s" name="%s" value="%s"%s%s%s><span></span><div class="%s">%s</div></label>%s',
						$args['wrapper']['before'],
						$args['class'],
						$args['type'],
						$args['name'],
						$value,
						self::is_required($args['required']),
						self::is_disabled($args['disabled']),
						(in_array($value, $args['value'])) ? ' checked' : '',
						$args['class-text'],
						$option,
						$args['wrapper']['after'],
					);
				}
			}
		}

		return $wrapper;
	}

	/**
	 * Вывод шаблона `textarea`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_textarea($args = array())
	{
		$defaults = array(
			'id'			=> '',
			'name'			=> '',
			'class'			=> '',
			'label'			=> '',
			'required'		=> false,
			'placeholder'	=> '',
			'value'			=> '',
			'readonly'		=> false,
			'disabled'		=> false,
			'autocomplete'	=> '',	// on | off
			'pattern'		=> '',
			'maxlength' 	=> 0,
			'minlength' 	=> 0,
			'inputmode'		=> '',
			'rows'			=> 3
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name']) {
			return sprintf(
				'%s<textarea name="%s"%s%s%s%s%s%s%s%s%s%s%s%s>%s</textarea>' . PHP_EOL,
				self::get_label($args),
				$args['name'],
				self::has_id($args['id']),
				self::has_placeholder($args['placeholder']),
				self::is_required($args['required']),
				self::has_class($args['class']),
				self::is_readonly($args['readonly']),
				self::is_disabled($args['disabled']),
				self::has_autocomplete($args['autocomplete']),
				self::has_pattern($args['pattern']),
				self::has_minlength($args['minlength']),
				self::has_maxlength($args['maxlength']),
				self::has_inputmode($args['inputmode']),
				(absint($args['rows'])) ? ' rows="' . absint($args['rows']) . '"' : '',
				esc_textarea($args['value']),
			);
		}

		return '';
	}

	/**
	 * Вывод шаблона элемента `button`
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_button($args = array())
	{
		$defaults = array(
			'id'			=> '',
			'name'			=> '',
			'type'			=> 'submit',
			'class'			=> '',
			'required'		=> false,
			'value'			=> '',
			'label'			=> '',
			'disabled'		=> false,
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['type']) {
			return sprintf(
				'<button %s type="%s"%s%s%s>%s</button>' . PHP_EOL,
				($args['name']) ? "name=\"{$args['name']}\"" : '',
				$args['type'],
				self::has_class($args['class']),
				self::is_disabled($args['disabled']),
				($args['value']) ? " value=\"{$args['value']}\"" : '',
				$args['label']
			);
		}

		return '';
	}

	/**
	 * Вывод шаблона произвольных тегов, как заголовки, пояснительные тексты и пр.
	 *
	 * @param array $args
	 * @return string
	 */
	public function get_tags($args = [])
	{
		$defaults = array(
			'id'			=> '',
			'name'			=> '',
			'value'			=> '',
			'class'			=> 'input-checked-text',
			'wrapper'		=> 'div',
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['value']) {
			return sprintf(
				'<%s%s%s>%s</%s>' . PHP_EOL,
				$args['wrapper'],
				self::has_id($args['id']),
				self::has_class($args['class']),
				$args['value'],
				$args['wrapper'],
			);
		}

		return '';
	}

	/**
	 * Вывод общего шаблона для `input`
	 *
	 * @param array $args
	 * @return string
	 */
	protected function get_input($args = array())
	{
		$defaults = array(
			'id'			=> '',
			'name'			=> '',
			'type'			=> 'text',
			'class'			=> '',
			'label'			=> '',
			'required'		=> false,
			'value'			=> '',
			'placeholder'	=> '',
			'readonly'		=> false,
			'disabled'		=> false,
			'autocomplete'	=> '',	// on | off
			'pattern'		=> '',
			'maxlength' 	=> 0,
			'minlength' 	=> 0,
			'inputmode'		=> ''
		);

		$args = wp_parse_args($args, $defaults);

		if ($args['name'] && $args['type']) {

			if (in_array($args['type'], ['tel', 'numeric', 'decimal', 'search', 'url']) && !isset($args['inputmode'])) {
				$args['inputmode']	= $args['type'];
				$args['type']		= 'text';
			}

			return sprintf(
				'%s<input%s name="%s" type="%s"%s%s%s value="%s"%s%s%s%s%s%s%s /><span class="input-message"></span>' . PHP_EOL,
				self::get_label($args),
				self::has_id($args['id']),
				$args['name'],
				$args['type'],
				self::has_placeholder($args['placeholder']),
				self::is_required($args['required']),
				self::has_class($args['class']),
				$args['value'],
				self::is_readonly($args['readonly']),
				self::is_disabled($args['disabled']),
				self::has_autocomplete($args['autocomplete']),
				self::has_pattern($args['pattern']),
				self::has_minlength($args['minlength']),
				self::has_maxlength($args['maxlength']),
				self::has_inputmode($args['inputmode']),
			);
		}

		return '';
	}

	/**
	 * Вывод стандартного лейбла
	 *
	 * @param array $args
	 * @return string
	 */
	private function get_label($args = array())
	{
		$defaults = array(
			'id'		=> '',
			'label'		=> '',
			'required'	=> false
		);

		$args = wp_parse_args($args, $defaults);

		// Если `id` задано, то выводим шаблон
		if ($args['label']) {
			return sprintf(
				'<label%s>%s%s</label>' . PHP_EOL,
				($args['id']) ? ' for="' . $args['id'] . '"' : '',
				$args['label'],
				($args['required']) ? ' <abbr title="' . __('обязательно') . '">*</abbr>' : ''
			);
		}

		return '';
	}

	/**
	 * Вывод шаблона id поля формы
	 *
	 * @param string $id
	 * @return string
	 */
	protected function has_id($id = '')
	{
		if ($id) {
			return sprintf(' id="%s"', $id);
		}

		return '';
	}

	/**
	 * Вывод шаблона замещаюего текст или подсказки
	 *
	 * @param string $placeholder
	 * @return string
	 */
	private function has_placeholder($placeholder = '')
	{
		if ($placeholder) {
			return sprintf(' placeholder="%s"', $placeholder);
		}

		return '';
	}

	/**
	 * Вывод шаблона css-класса поля формы
	 *
	 * @param string|array $class
	 * @return string
	 */
	protected function has_class($class = '')
	{
		if (is_array($class)) {
			$class = trim(join(' ', $class));
		}

		if ($class) {
			return sprintf(' class="%s"', $class);
		}

		return '';
	}

	/**
	 * Вывод шаблона обязательности поля формы к заполнению
	 *
	 * @param boolean $require
	 * @return string
	 */
	private function is_required($require = false)
	{
		if ($require) {
			return ' required="require"';
		}

		return '';
	}

	/**
	 * Вывод шаблона атрибута `только для чтение` поля формы
	 *
	 * @param boolean $readonly
	 * @return string
	 */
	private function is_readonly($readonly = false)
	{
		if ($readonly) {
			return ' readonly';
		}

		return '';
	}

	/**
	 * Вывод шаблона диактиватиции поля формы
	 *
	 * @param boolean $disabled
	 * @return string
	 */
	private function is_disabled($disabled = false)
	{
		if ($disabled) {
			return ' disabled';
		}

		return '';
	}

	/**
	 * Вывод шаблона атрибута для множественного выбора поля формы
	 *
	 * @param boolean $multiple
	 * @return string
	 */
	private function is_multiple($multiple = false)
	{
		if ($multiple) {
			return ' multiple';
		}

		return '';
	}

	/**
	 * Вывод шаблона автозаполнения поля формы
	 *
	 * @param string $pattern
	 * @return string
	 */
	private function has_autocomplete($autocomplete = '')
	{
		if (in_array($autocomplete, ['on', 'off'])) {
			return sprintf(' autocomplete="%s"', $autocomplete);
		}

		return '';
	}

	/**
	 * Вывод шаблона допустимых символов поля формы
	 *
	 * @param string $pattern
	 * @return string
	 */
	private function has_pattern($pattern = '')
	{
		if ($pattern) {
			return sprintf(' pattern="%s"', $pattern);
		}

		return '';
	}

	/**
	 * Вывод шаблона максимального количества символов поля формы
	 *
	 * @param string $pattern
	 * @return string
	 */
	private function has_maxlength($maxlength = 0)
	{
		$maxlength = absint($maxlength);

		if ($maxlength) {
			return sprintf(' maxlength="%d"', $maxlength);
		}

		return '';
	}

	/**
	 * Вывод шаблона минимального количества символов поля формы
	 *
	 * @param string $pattern
	 * @return string
	 */
	private function has_minlength($minlength = 0)
	{
		$minlength = absint($minlength);

		if ($minlength) {
			return sprintf(' minlength="%d"', $minlength);
		}

		return '';
	}

	/**
	 * Вывод шаблона доступного экранного набора поля формы
	 *
	 * @param string $inputmode
	 * @return string
	 */
	private function has_inputmode($inputmode = '')
	{
		if (in_array($inputmode, ['none', 'text', 'numeric', 'decimal', 'tel', 'email', 'search', 'url'])) {
			return sprintf(' inputmode="%s"', $inputmode);
		}

		return '';
	}
}
