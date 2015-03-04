<?php

class SJB_PicturesType extends SJB_Type
{
	function SJB_PicturesType($property_info)
	{
		parent::SJB_Type($property_info);

		$this->sql_type 		= 'UNSIGNED'; 
		$this->default_template = 'pictures.tpl';
	}

	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$gallery = new SJB_ListingGallery();
		$gallery->setListingSID($this->object_sid);

		$pictures_info = $gallery->getPicturesInfo();

		$newPropertyVariables = array(
						'pictures' 			 => $pictures_info,
						'number_of_pictures' => count($pictures_info),
					);
		return array_merge($newPropertyVariables, $propertyVariables);
	}

	function getValue()
	{
        $gallery = new SJB_ListingGallery();
		$gallery->setListingSID($this->object_sid);
		return $gallery->getPicturesInfo();
	}

	function getDisplayValue()
	{
		if (!empty($this->object_sid)) {
		    return $this->getValue();
		}
		return $this->property_info['value'];
	}

	function getSQLValue()
	{
		return $this->property_info['value'] ?: 0;
	}
}

