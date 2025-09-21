<?php
defined('ABSPATH') || exit;

/**
 * Класс вывода формы по переданным параметрам
 */
class THEPLUGIN_Form_Style_Wrapper extends THEPLUGIN_Form_Style
{
	/**
	 * Undocumented variable
	 *
	 * @var string
	 */
	protected $_prefix = 'form-style';

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	protected $_grid = array(
		'100'	=> 'full',
		'75'	=> 'three-quarters',
		'66'	=> 'two-thirds',
		'50'	=> 'half',
		'33'	=> 'third',
		'25'	=> 'quarter',
		'i75'	=> 'un-three-quarters',
		'i66'	=> 'un-two-thirds',
		'i50'	=> 'un-half',
		'i33'	=> 'un-third',
		'i25'	=> 'un-quarter',
		'0'		=> 'none'
	);

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	protected $_wrapper = array(
		'form-before'	=> '',
		'components'	=> '',
		'form-after'	=> '',
	);

	/**
	 * Счётчики разделов при наличии соответствующих паттернов
	 *
	 * @var array
	 */
	protected $_counter = array(
		'#'		=> '0',
		'##'	=> '00'
	);

	/**
	 * Undocumented function
	 *
	 * @param [type] $args
	 */
	function __construct($args)
	{
		self::init($args);
	}

	/**
	 * Undocumented function
	 *
	 * @param array $args
	 * @return void
	 */
	public function init($args = array())
	{
		$defaults = array(
			'form'			=> [],
			'components'	=> [],
			'vars'			=> [],
		);

		$args = wp_parse_args($args, $defaults);

		// Переназначение перфикса общего стиля
		if (isset($args['prefix']) && $args['prefix']) {
			$this->_prefix = $args['prefix'];
		}

		// Переназначение классов модальной сетки полей формы
		if (isset($args['grids']) && $args['grids'] && is_array($args['grids'])) {
			$this->_grid = $args['grids'];
		}

		$this->_wrapper = array(
			'form-before'	=> self::set_form_wrapper_before($args['form']),
			'components'	=> self::set_components_wrapper($args),
			'form-after'	=> self::set_form_wrapper_after($args['form']),
		);
	}

	/**
	 * Получение html-обёрток открывающего тега формы
	 *
	 * @param array $args
	 * @return string
	 */
	private function set_form_wrapper_before($args = [])
	{

		$defaults = array(
			'id'		=> '',
			'name'		=> '',
			'method'	=> '',
			'action'	=> '',
			'enctype'	=> '',
			'class'		=> [$this->_prefix],
			'attrs'		=> []
		);

		$args = wp_parse_args($args, $defaults);

		if (is_scalar($args['class'])) {
			$args['class'] = explode(' ', $args['class']);
		}

		if (!in_array($this->_prefix, $args['class'])) {
			array_unshift($args['class'], $this->_prefix);
		}

		$attrs = '';
		if ($args['attrs']) {
			foreach ($args['attrs'] as $key => $value) {
				$attrs .= $key . '="' . $value . '"';
			}
		}

		return sprintf(
			'<form%s%s%s%s%s%s%s>',
			parent::has_id($args['id']),
			($args['name']) ? " name=\"{$args['name']}\"" : '',
			parent::has_class($args['class']),
			($args['method']) ? " method=\"{$args['method']}\"" : '',
			($args['action']) ? " action=\"{$args['action']}\"" : '',
			($args['enctype']) ? " enctype=\"{$args['enctype']}\"" : '',
			($attrs) ? ' ' . $attrs : ''
		);
	}

	/**
	 * Получение html-обёрток полей формы
	 *
	 * @param array $args
	 * @return string
	 */
	private function set_components_wrapper($args = [])
	{
		$wrapper = '';

		if ($args['components']) {
			foreach ($args['components'] as $rows) {
				$wrapper .= '<div class="' . self::get_class_row() . '">' . PHP_EOL;
				if (is_array($rows) && $rows) {

					foreach ($rows as $items) {
						if (isset($items['customize']))
							continue;

						$wrapper .= '<div class="' . self::get_class_grid($items['grid']) . '">' . PHP_EOL;
						if ($items['elements'] && is_array($items['elements'])) {
							foreach ($items['elements'] as $name => $item) {
								$item['name'] = $name;
								if (isset($args['vars'][$name])) {
									$item['value'] = $args['vars'][$name];
								}

								// TODO подумать, как лучше автоматически проставлять нумерацию
								if (strpos($item['name'], '##')) {
									$this->_counter['##'] = absint($this->_counter['##']);
									$this->_counter['##']++;
									$item['name'] = str_replace('##', $this->_counter['##'], $item['name']);
								}

								if (strpos($item['name'], '#')) {
									$this->_counter['#'] = absint($this->_counter['#']);
									$this->_counter['#']++;
									$item['name'] = str_replace('##', $this->_counter['#'], $item['name']);
								}

								switch ($item['type']) {
									case 'text':
									case 'email':

									case 'search':
										$wrapper .= parent::get_input($item);
										break;
									case 'tel':
										$wrapper .= parent::get_tel($item);
										break;
									case 'url':
										$wrapper .= parent::get_url($item);
										break;
									case 'hidden':
										$wrapper .= parent::get_hidden($item);
										break;
									case 'select':
										$wrapper .= parent::get_select($item);
										break;
									case 'checkbox':
									case 'radio':
										$wrapper .= parent::get_checked($item);
										break;
									case 'textarea':
										$wrapper .= parent::get_textarea($item);
										break;
									case 'file':
										$wrapper .= parent::get_file($item);
										break;
									case 'privacy-policy':
										$wrapper .= parent::get_privacy_agree($item);
										break;
									case 'submit':
										$wrapper .= parent::get_button($item);
										break;
									case 'tags':
										$wrapper .= parent::get_tags($item);
										break;
									default:
										$wrapper .= parent::__get($item);
										break;
								}
							}
						}
						$wrapper .= '</div>' . PHP_EOL; // grid-end
					}
				}
				$wrapper .= '</div>' . PHP_EOL; // row-end
			}
		}

		return $wrapper;
	}

	/**
	 * Получение html-обёрток закрывающего тега формы
	 *
	 * @param array $args
	 * @return string
	 */
	private function set_form_wrapper_after($args = [])
	{

		$defaults = array(
			'id'		=> '',
			'name'		=> '',
			'method'	=> '',
			'action'	=> '',
			'enctype'	=> '',
			'class'		=> [$this->_prefix]
		);

		$args = wp_parse_args($args, $defaults);

		return '</form>';
	}

	/**
	 * Получение html-обёртки всей формы
	 *
	 * @return string
	 */
	public function get_wrapper()
	{
		return join(PHP_EOL, $this->_wrapper);
	}

	/**
	 * Получение класса обёртки блоков формы для модульной сетки
	 *
	 * @param string $size
	 * @return string
	 */
	private function get_class_grid($size = '100')
	{
		$prefix = ($this->_prefix) ? $this->_prefix . '__' : '';
		return $prefix . $this->_grid[$size];
	}

	/**
	 * Получение класса обёртки блоков формы для модульной сетки
	 *
	 * @param string $size
	 * @return string
	 */
	private function get_class_row($class = 'row')
	{
		$prefix = ($this->_prefix) ? $this->_prefix . '__' : '';
		return $prefix . $class;
	}
}
