<?php

/**
 * Класс загрузки файлов на сервер средствами WP
 */
class THEPLUGIN_Upload_File
{

	public $get_file;

	public $f_file;
	public $f_path;
	public $f_size;

	function __construct($args)
	{
		$this->init($args);
	}

	public function init($args = [])
	{

		$defaults = array(
			'filename'  => '',
			'filepath'  => '',
			'filesize'  => wp_max_upload_size()
		);

		$args           = wp_parse_args($args, $defaults);
		$this->f_file   = $args['filename'];
		$this->f_path   = $args['filepath'];
		$this->f_size   = $args['filesize'];

		$confirm_file = $this->confirm_file();

		if ($confirm_file['confirm']) {
			$this->get_file = $this->callback();
		} else {
			$this->get_file = $confirm_file;
		}
	}

	public function confirm_file()
	{
		if (empty($this->f_file) || !isset($this->f_file))
			return array('confirm' => false, 'message' => 'Empty filename or path.');

		if (isset($this->f_file['error']) && $this->f_file['error'])
			return array('confirm' => false, 'message' => 'File upload error.');

		if (empty($this->f_file))
			return array('confirm' => false, 'message' => 'File is not selected.');

		return array('confirm' => true, 'message' => '');
	}

	public function check_mime_type($args = [])
	{

		$defaults   = ['mime_type' => get_allowed_mime_types()];
		$args       = wp_parse_args($args, $defaults);

		$file_mime = mime_content_type($this->f_file['tmp_name']);
		if (!in_array($file_mime, $args['mime_type']) || empty($args['mime_type']))
			return array('confirm' => false, 'message' => 'WordPress doesn\'t allow this type of uploads.');

		return array('confirm' => $file_mime, 'message' => '');
	}

	public function upload_file($filesize = '')
	{

		$filesize = (empty($filesize)) ? $this->f_size : $filesize;

		if ($this->f_file['size'] > $filesize)
			return array('confirm' => false,  'message' => 'It is too large than expected.');

		if (!move_uploaded_file($this->f_file['tmp_name'], $this->f_path))
			return array('confirm' => false, 'message' => 'File upload error.');

		return array('confirm' => $this->f_path, 'message' => '');
	}

	public function success()
	{
		return 'File uploaded successfully';
	}

	public function get_file_confirm()
	{
		$file = $this->get_file;
		return $file['confirm'];
	}

	public function get_file_message()
	{
		$file = $this->get_file;
		return $file['message'];
	}

	public function callback()
	{
	}
}

/**
 * Загрузка изображения в Медиа-библиотеку WP
 */
add_filter('wp_image_editors', function ($array) {
	return array('WP_Image_Editor_GD',);
});
