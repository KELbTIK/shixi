<?php

class SJB_PictureType extends SJB_UploadFileType
{
	function SJB_PictureType($property_info)
	{
		parent::SJB_UploadFileType($property_info);
		$this->default_template = 'picture.tpl';
	}
	
	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$upload_manager = new SJB_UploadPictureManager();
		$upload_manager->setFileGroup("pictures");
		
		$newPropertyVariables =  array(						
						'value'	=> array(
							'file_url' => $upload_manager->getUploadedFileLink($this->property_info['value']),
							'file_name' => $upload_manager->getUploadedFileName($this->property_info['value']),
						),
					);
		return array_merge($newPropertyVariables, $propertyVariables);
	}
	
	function getValue()
	{
		$upload_manager = new SJB_UploadPictureManager();
		$upload_manager->setFileGroup("pictures");
		return array('file_url' => $upload_manager->getUploadedFileLink($this->property_info['value']),
					'file_name' => $upload_manager->getUploadedFileName($this->property_info['value']),
					);
	}

	function isValid()
	{
		$file_id = $this->property_info['id'] . "_" .$this->object_sid;
		$this->property_info['value'] = $file_id;
		$upload_manager = new SJB_UploadPictureManager();
		if ($upload_manager->isValidUploadedPictureFile($this->property_info['id'])) {
			return true;
		}
		
		return $upload_manager->getError();
	}
	
	function getSQLValue()
	{
		if (is_array($this->property_info['value']) && ! empty($this->property_info['value']['import'])) {
			return $this->property_info['value']['import'];
		} else {
			$fileId = $this->property_info['id'] . '_' .$this->object_sid;
			$this->property_info['value'] = $fileId;
			$uploadManager = new SJB_UploadPictureManager();
			$uploadManager->setUploadedFileID($fileId);
			$uploadManager->setHeight($this->property_info['height']);
			$uploadManager->setWidth($this->property_info['width']);
			$uploadManager->uploadPicture($this->property_info['id']);
			if (SJB_UploadPictureManager::doesFileExistByID($fileId)) {
				return $fileId;
			}
			return '';
		}
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'width',
				'caption'	=> 'Width', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_system' => true,
				'is_required'=> true,
				),
			array(
				'id'		=> 'height',
				'caption'	=> 'Height', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_system' => true,
				'is_required'=> true,
				),
		);
	}
}
