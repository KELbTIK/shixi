<?php


class SJB_Object
{
	var $sid;
	var $db_table_name;
	protected $objectType;
	
	/**
	 * 
	 * @var SJB_ObjectDetails
	 */
	var $details;
	var $errors;

	/**
	 * @return SJB_ObjectDetails
	 */
	function getDetails()
	{
		return $this->details;
	}
	
	function getProperties()
	{
		return $this->details->getProperties();
	}
	
	function setSID($sid)
	{
		$this->details->setObjectSID($sid);
		$this->sid = $sid;
	}

	function getSID()
	{
		return $this->sid;
	}

	function getID()
	{
		$id = $this->getPropertyValue('id');
		if (!empty($id))
			return $id;
		return $this->sid;
	}

	function addProperty($property_info)
	{
		$this->details->addProperty($property_info);
	}

	function deleteProperty($property_id)
	{
		$this->details->deleteProperty($property_id);
	}
	
	/**
	 * @param string $property_id
     * @return SJB_ObjectProperty
	 */
	function getProperty($property_id)
	{
	    return $this->details->getProperty($property_id);
	}
	
	function makePropertyNotRequired($property_id)
	{
		return $this->details->makePropertyNotRequired($property_id);
	}

	function dontSaveProperty($property_id)
	{
		return $this->details->dontSaveProperty($property_id);
	}

    function propertyIsSet($property_id)
	{
		return $this->details->propertyIsSet($property_id);
	}

	function getPropertyDisplayValue($property_id)
	{
		$property = $this->details->getProperty($property_id);
		if (!empty($property))
			return $property->getDisplayValue();
		return null;
	}

	function getPropertyValue($property_id)
	{
		$property = $this->details->getProperty($property_id);
		if (!empty($property))
			return $property->getValue();
		return null;
	}

	function setPropertyValue($property_id, $value)
	{
		$property = $this->details->getProperty($property_id);
		if (!empty($property))
			return $property->setValue($value);
		return false;
	}

	function getErrors()
	{
		return $this->errors;
	}

	public function getPropertyList()
	{
		$result = array();
		$property_list = array_keys($this->getProperties());
		foreach ($property_list as $property_name)
			$result[$property_name] = $property_name;
		return $result;
	}
	
	public function getPropertyInfo($propertyID)
	{
		$property = $this->details->getProperty($propertyID);
		if ($property) {
			return $property->type->property_info;
		}
		
		return null;
	}
	
	public function setPropertyInfo($propertyID, $propertyInfo)
	{
		$this->details->getProperty($propertyID)->type->property_info = $propertyInfo;
	}
	
	public function getChild($propertyID)
	{
		return $this->details->getProperty($propertyID)->type->child;
	}


	/**
	 * Changes values of float numbers from formatted into valid format for saving in DB
	 * Used in invoices, products, taxes etc.
	 */
	public function setFloatNumbersIntoValidFormat()
	{
		$objectDetails = $this->getDetails();
		$properties = $objectDetails->getProperties();
		foreach ($properties as $property) {
			if ($property->isComplex()) {
				$complexProperties = $property->type->complex->getProperties();
				$newComplexPropertyValues = array();
				foreach ($complexProperties as $complexPropertyName => $complexProperty) {
					$newValues = array();
					$values =  $complexProperty->getValue();
					if ($complexProperty->getType() == 'float') {
						if (is_array($values)) {
							foreach ($values as $key => $value) {
								if (SJB_I18N::getInstance()->isValidFloat($value)) {
									$newValues[$key] = SJB_I18N::getInstance()->getInput('float', $value);
								} else {
									$newValues[$key] = $value;
								}
							}
						} else {
							if (SJB_I18N::getInstance()->isValidFloat($values)) {
								$newValues = SJB_I18N::getInstance()->getInput('float', $values);
							} else {
								$newValues = $values;
							}
						}
					} else {
						$newValues = $values;
					}
					$newComplexPropertyValues[$complexPropertyName] = $newValues;
				}
				$property->setValue($newComplexPropertyValues);
			}
			elseif ($property->getType() == 'float') {
				$currentValue = $property->getValue();
				if (SJB_I18N::getInstance()->isValidFloat($currentValue)) {
					$property->setValue(SJB_I18N::getInstance()->getInput('float', $currentValue));
				} else {
					$property->setValue($currentValue);
				}
			}
		}
	}

	public function getObjectType()
	{
		return $this->objectType;
	}
}

