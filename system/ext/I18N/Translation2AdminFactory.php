<?php


class Translation2AdminFactory
{
	/**
	 * @var I18NContext
	 */
	public $context;

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context = $context;
	}


	/**
	 * @param $file_name
	 * @param bool $save_on_shutdown
	 * @param bool $getEx
	 * @param string $file_name_pages
	 * @return object|Translation2_Admin
	 */
	function createTrAdmin($file_name, $save_on_shutdown = false, $getEx = false, $file_name_pages = '')
	{
		list($driver, $options) = $this->_getTrMetaData($file_name, $save_on_shutdown, $getEx, $file_name_pages);

		$tr_admin = Translation2_Admin::factory($driver, $options);

		return $tr_admin;
	}

	/**
	 * @param string $file_name
	 * @param $save_on_shutdown
	 * @param bool $getEx
	 * @param string $file_name_pages
	 * @return array
	 */
	function _getTrMetaData($file_name, $save_on_shutdown, $getEx = false, $file_name_pages = '')
	{
		if ($getEx)
			$driver = 'XML_EX';
		else
			$driver = 'XML';

		$options = array
		(
			'filename' 			=> $file_name,
			'save_on_shutdown' 	=> $save_on_shutdown,
			'filename_pages'	=> $file_name_pages,
		);	
		
		return array($driver, $options);
	}
}
