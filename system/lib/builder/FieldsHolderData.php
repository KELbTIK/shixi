<?php

class SJB_FieldsHolderData
{
	/**
	 * @var string
	 */
	private $ID;

	/**
	 * @var array
	 */
	private $fields;
	private $htmlValues;
	private $complexParents;

	/**
	 * @param array $data
	 */
	function __construct($data)
	{
		$this->setID($data);
		$this->setFields($data);
		$this->setHtmlValues($data);
		$this->setComplexParents($data);
	}

	/**
	 * @throws Exception
	 */
	private function setID($data)
	{
		$this->ID = (string) SJB_Array::get($data, 'id');
		if (!$this->ID) {
			throw new Exception('Fields Holder ID is missing');
		}
	}

	/**
	 * @param array $data
	 */
	private function setFields($data)
	{
		$this->fields = SJB_Array::get($data, 'fields', array());
		if (!is_array($this->fields)) {
			$this->fields = array();
		}
	}

	/**
	 * @param array $data
	 */
	private function setHtmlValues($data)
	{
		$this->htmlValues = SJB_Array::get($data, 'htmlValues');
	}

	/**
	 * @param array $data
	 */
	private function setComplexParents($data)
	{
		$this->complexParents = SJB_Array::get($data, 'complex');
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @return string
	 */
	public function getID()
	{
		return $this->ID;
	}

	public function getFieldHtmlValue($fieldID)
	{
		return SJB_Array::get($this->htmlValues, $fieldID);
	}

	public function getComplexField($fieldID)
	{
		return SJB_Array::get($this->complexParents, $fieldID);
	}
}
