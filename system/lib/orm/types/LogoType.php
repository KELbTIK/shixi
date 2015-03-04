<?php

class SJB_LogoType extends SJB_UploadFileType
{
	function SJB_LogoType($property_info)
	{
		parent::SJB_UploadFileType($property_info);
		$this->default_template = 'logo.tpl';
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
		$fileInfo = SJB_UploadFileManager::getUploadedFileInfo($this->property_info['value']);
		$thumbFileInfo = SJB_UploadFileManager::getUploadedFileInfo($this->property_info['value']."_thumb");

		return array(
			'file_url' => $upload_manager->getUploadedFileLink($this->property_info['value'], $fileInfo),
			'file_name' => empty($fileInfo['file_name']) ? null : $fileInfo['file_name'],
			'thumb_file_url' => $upload_manager->getUploadedFileLink($this->property_info['value'] . "_thumb", $thumbFileInfo),
			'thumb_file_name' => empty($thumbFileInfo['file_name']) ? null : $thumbFileInfo['file_name'],
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
		if (!empty($this->property_info['value'])) {
			$fileId = $this->property_info['id'] . '_' .$this->object_sid;
			$this->property_info['value'] = $fileId;
			$uploadManager = new SJB_UploadPictureManager();
			$uploadManager->setUploadedFileID($fileId);
			$uploadManager->setHeight($this->property_info['height']);
			$uploadManager->setWidth($this->property_info['width']);
			$uploadManager->uploadPicture($this->property_info['id'], $this->property_info);
			if (SJB_UploadPictureManager::doesFileExistByID($fileId)) {
				return $fileId;
			}
		}
		return '';
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'width',
				'caption'	=> 'Width', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> 100,
				'is_required'=> false,
				'is_system'	=> true
				),
			array(
				'id'		=> 'height',
				'caption'	=> 'Height', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_required'=> false,
				'is_system'	=> true
				),
			array(
				'id'		=> 'second_width',
				'caption'	=> 'Width', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> 100,
				'is_required'=> false,
				'is_system'	=> true
				),
			array(
				'id'		=> 'second_height',
				'caption'	=> 'Height', 
				'type'		=> 'integer',
				'minimum'	=> '1',
				'value'		=> '100',
				'is_required'=> false,
				'is_system'	=> true
				),
		);
	}
}
