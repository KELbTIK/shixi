<?php

class SJB_FieldTreeImportHelper
{
	/**
	 * @var SimpleXMLElement
	 */
	private $xml;

	/**
	 * @var array
	 */
	private $treeValues = array();

	/**
	 * @param string $xmlString
	 */
	public function __construct($xmlString)
	{
		$this->xml = new SimpleXMLElement($xmlString, (LIBXML_NOERROR|LIBXML_NOWARNING));
	}

	public function parseAndGetValues()
	{
		$count = $this->xml->children()->count();
		if ($count > 0) {
			$this->parseXMLData($this->xml);
		}
		return $this->getPreparedValues();
	}

	/**
	 * @param SimpleXMLElement $xmlElements
	 */
	protected function parseXMLData(SimpleXMLElement $xmlElements)
	{
		/** @var $xmlElement SimpleXMLElement */
		foreach ($xmlElements->branch as $xmlElement) {
			/** @var $items SimpleXMLElement */
			$items = isset($xmlElement->items) ? $xmlElement->items : null;
			if ($items && $items->children()->count() > 0) {
				$this->parseXMLData($items);
			}
			else {
				$sid = (int) $xmlElement->sid;
				array_push($this->treeValues, $sid);
			}
		}
	}

	public function getPreparedValues()
	{
		return implode(',', $this->treeValues);
	}
}
