<?php

namespace SJB\Smarty;

class Resource extends \Smarty_Resource_Custom
{
	/**
	 * @var \SJB_TemplateSupplier
	 */
	protected $templateSupplier = null;

	public function __construct($templateSupplier)
	{
		$this->templateSupplier = $templateSupplier;
	}

	protected function fetch($name, &$source, &$mtime)
	{
		$this->templateSupplier->template_source($name, $source, $mtime);
	}

	/**
	 * Fetch a template's modification time from database
	 *
	 * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the compile template source.
	 * @param string $name template name
	 * @return integer timestamp (epoch) the template was modified
	 */
	protected function fetchTimestamp($name)
	{
		return $this->templateSupplier->fetchTemplateTimestamp($name);
	}

}
