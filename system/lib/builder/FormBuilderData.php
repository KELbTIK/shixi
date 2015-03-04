<?php

class SJB_FormBuilderData
{
	/**
	 * @var SJB_FieldsHolderData[]
	 */
	private $fieldsHoldersData;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $listingTypeID;

	/**
	 * @var string
	 */
	private $layout;

	/**
	 * @param array $data
	 */
	function __construct($data)
	{
		$this->setType($data);
		$this->setListingTypeID($data);
		if ($this->getType() == SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY) {
			$this->setLayout($data);
		}
		$this->setFieldsHoldersData($data);
	}

	/**
	 * @param array $data
	 * @throws Exception
	 */
	private function setFieldsHoldersData($data)
	{
		$fieldsHoldersData = SJB_Array::get($data, 'fieldsHolders', array());

		if (!is_array($fieldsHoldersData) || empty($fieldsHoldersData)) {
			throw new Exception('Builder fields holder data is required');
		}

		foreach ($fieldsHoldersData as $fieldsHolderData) {
			$fieldsHolderData = new SJB_FieldsHolderData($fieldsHolderData);
			$this->setFieldsHolder($fieldsHolderData);
		}
	}

	/**
	 * @param array $data
	 * @throws Exception
	 */
	private function setLayout($data)
	{
		$this->layout = (string) SJB_Array::get($data, 'layout');
		if (!$this->layout) {
			throw new Exception('Builder layout is required');
		}
	}

	/**
	 * @param array $data
	 * @throws Exception
	 */
	private function setListingTypeID($data)
	{
		$this->listingTypeID = (string) SJB_Array::get($data, 'listingTypeID');
		if (!$this->listingTypeID) {
			throw new Exception('Listing type id is required');
		}
	}

	/**
	 * @param array $data
	 * @throws Exception
	 */
	private function setType($data)
	{
		$this->type = (string) SJB_Array::get($data, 'type');
		if (!$this->type) {
			throw new Exception('Builder type is required');
		}
	}

	/**
	 * @param SJB_FieldsHolderData $fieldsHolderData
	 */
	private function setFieldsHolder(SJB_FieldsHolderData $fieldsHolderData)
	{
		$this->fieldsHoldersData[$fieldsHolderData->getID()] = $fieldsHolderData;
	}

	/**
	 * @return SJB_FieldsHolderData[]
	 */
	public function getFieldsHoldersData()
	{
		return $this->fieldsHoldersData;
	}

	/**
	 * @return string
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * @return string
	 */
	public function getListingTypeID()
	{
		return $this->listingTypeID;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}
