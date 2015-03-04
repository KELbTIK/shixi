<?php

class ValidatorBatch
{
	var $validationTable = array();
	var $errors = array();

	function setDataReflector($reflector)
	{
		$this->dataReflector = $reflector;
	}

	function setValueValidatorFactoryReflector($reflector)
	{
		$this->valueValidatorFactoryReflector = $reflector;
	}

	function add($property_id, $validator_id, $error)
	{
		$this->validationTable[] = array($property_id, $validator_id, $error);
	}

	function isValid()
	{
		$this->errors = array();
		foreach ($this->validationTable as $validationRow) {
			$value = $this->dataReflector->get($validationRow[0]);
			$valueValidator = $this->valueValidatorFactoryReflector->create($validationRow[1]);
			if (!$valueValidator->isValid($value)) {
				$this->errors[$validationRow[0]] = $validationRow[2];
			}
		}
		return empty($this->errors);
	}

	function getErrors()
	{
		return $this->errors;
	}
}

