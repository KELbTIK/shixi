<?php


class GeneralValidationFactory
{
	function createValidatorBatch(&$dataReflector, &$factoryReflector)
	{
		require_once('Validation/ValidatorBatch.php');
		$validator = new ValidatorBatch();
		$validator->setDataReflector($dataReflector);
		$validator->setValueValidatorFactoryReflector($factoryReflector);
		return $validator;
	}
	
	function createAndValidator()
	{
		require_once('Validation/AndValidator.php');
		$validator = new AndValidator();
		$validators = func_get_args();
		
		for ($i = 0; $i < count($validators); $i++) {
			$validator->add($validators[$i]);
		}
		
		return $validator;
	}
	
	function createFormatValidator($regex, $valid_symbols)
	{
		require_once('Validation/FormatValidator.php');
		$validator = new FormatValidator();
		$validator->setRegex($regex);
		$validator->setValidSymbols($valid_symbols);
		return $validator;
	}
	
	function createMaxLengthValidator($max_length)
	{
		require_once('Validation/MaxLengthValidator.php');
		$validator = new MaxLengthValidator();
		$validator->setMaxLength($max_length);
		return $validator;
	}
	
	function createRegexValidator($regex)
	{
		require_once('Validation/RegexValidator.php');
		$validator = new RegexValidator();
		$validator->setRegex($regex);
		return $validator;
	}
	
	function createNotValidator(&$source_validator)
	{
		require_once('Validation/NotValidator.php');
		$validator = new NotValidator();
		$validator->setValidator($source_validator);
		return $validator;
	}

	function createNotEmptyValidator()
	{
		require_once('Validation/NotEmptyValidator.php');
		return new NotEmptyValidator();
	}

	function createEqualToValidator($base_value)
	{
		require_once('Validation/EqualToValidator.php');
		$validator = new EqualToValidator();
		$validator->setBaseValue($base_value);
		return $validator;
	}
	
	
}

?>