<?php


class SJB_LanguageValidatorFactory
{
	var $generalValidationFactory;

	/**
	 * @var HashtableReflector
	 */
	var $dataReflector;
	/**
	 * @var ReflectionFactory
	 */
	protected $reflectionFactory;

	/**
	 * @var I18NDatasource
	 */
	protected $langDataSource;
	
	function &createAddLanguageValidator($lang_data)
	{	
		$dataReflector =& $this->reflectionFactory->createHashtableReflector($lang_data);
		$this->setDataReflector($dataReflector);
		
		$factoryReflector =& $this->reflectionFactory->createFactoryReflector($this);

		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);

		$batch->add('languageId', 'IDSymbolsValidator', 'LANGUAGE_ID_CONTAINS_NOT_ALLOWED_SYMBOLS');
		$batch->add('languageId', 'NotEmptyValidator', 'LANGUAGE_ID_IS_EMPTY');
		$batch->add('languageId', 'LanguageIDLengthValidator', 'TOO_LONG_LANGUAGE_ID');
		$batch->add('languageId', 'LanguageNotExistsValidator', 'LANGUAGE_ALREADY_EXISTS');
		$batch->add('caption', 'LanguageCaptionLengthValidator', 'TOO_LONG_LANGUAGE_CAPTION');
		$batch->add('caption', 'NotEmptyValidator', 'LANGUAGE_CAPTION_IS_EMPTY');
		$batch->add('date_format', 'DateFormatLengthValidator', 'TOO_LONG_DATE_FORMAT');
		$batch->add('date_format', 'DateFormatValidator', 'INVALID_DATE_FORMAT');
		$batch->add('decimal_separator', 'DecimalsSeparatorValidator', 'INVALID_DECIMALS_SEPARATOR');
		$batch->add('thousands_separator', 'ThousandsSeparatorValidator', 'INVALID_THOUSANDS_SEPARATOR');
		$batch->add('thousands_separator', 'DifferentThousandsAndDecimalSeparatorsValidator', 'Thousands and decimal separators should be different');
		$batch->add('decimals', 'DecimalsValidator', 'INVALID_DECIMALS');
		$batch->add('decimals', 'NotEmptyValidator', 'DECIMALS_IS_EMPTY');
		
		return $batch;
	}
	
	
	function &createUpdateLanguageValidator($lang_data)
	{	
		$dataReflector = $this->reflectionFactory->createHashtableReflector($lang_data);
		$this->setDataReflector($dataReflector);
		
		$factoryReflector = $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
		
		$batch->add('caption', 'LanguageCaptionLengthValidator', 'TOO_LONG_LANGUAGE_CAPTION');
		$batch->add('caption', 'NotEmptyValidator', 'LANGUAGE_CAPTION_IS_EMPTY');
		$batch->add('date_format', 'DateFormatLengthValidator', 'TOO_LONG_DATE_FORMAT');
		$batch->add('date_format', 'DateFormatValidator', 'INVALID_DATE_FORMAT');
		$batch->add('decimal_separator', 'DecimalsSeparatorValidator', 'INVALID_DECIMALS_SEPARATOR');
		$batch->add('thousands_separator', 'ThousandsSeparatorValidator', 'INVALID_THOUSANDS_SEPARATOR');
		$batch->add('thousands_separator', 'DifferentThousandsAndDecimalSeparatorsValidator', 'Thousands and decimal separators should be different');
		$batch->add('decimals', 'DecimalsValidator', 'INVALID_DECIMALS');
		$batch->add('decimals', 'NotEmptyValidator', 'DECIMALS_IS_EMPTY');
		$batch->add('active', 'DefaultLanguageMustBeActiveValidator', 'Default language cannot be deactivated');
		
		return $batch;
	}
	
	function createDeleteLanguageValidator($lang_id)
	{	
		$dataReflector =& $this->reflectionFactory->createConstantReflector($lang_id);
		$factoryReflector =& $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
		
		$batch->add('', 'LanguageExistsValidator', 'LANGUAGE_NOT_EXISTS');
		$batch->add('', 'LanguageIsNotDefaultValidator', 'LANGUAGE_IS_DEFAULT');
		
		return $batch;
	}
	
	function &createSetDefaultLanguageValidator($lang_id)
	{	
		$dataReflector =& $this->reflectionFactory->createConstantReflector($lang_id);
		$factoryReflector =& $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
		
		$batch->add('', 'LanguageExistsValidator', 'LANGUAGE_NOT_EXISTS');
		$batch->add('', 'LanguageIsActiveValidator', 'LANGUAGE_IS_NOT_ACTIVE');
		
		return $batch;
	}
	
	function &createImportLanguageValidator($lang_file_data)
	{	
		$dataReflector =& $this->reflectionFactory->createHashtableReflector($lang_file_data);
		$this->setDataReflector($dataReflector);
		
		$factoryReflector =& $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
				
		$batch->add('languageId', 'LanguageNotExistsValidator', 'LANGUAGE_ALREADY_EXISTS');
		$batch->add('lang_file_path', 'LanguageFileValidator', 'LANGUAGE_FILE_IS_INVALID');
		
		return $batch;
	}

	/**
	 * @param I18NDatasource $langDataSource
	 */
	function setLanguageDataSource($langDataSource)
	{
		$this->langDataSource = $langDataSource;
	}

	/**
	 * @param ReflectionFactory $reflectionFactory
	 */
	function setReflectionFactory($reflectionFactory)
	{
		$this->reflectionFactory = $reflectionFactory;
	}
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	function setGeneralValidationFactory(&$generalValidationFactory)
	{
		$this->generalValidationFactory =& $generalValidationFactory;
	}

	/**
	 * @param HashtableReflector $dataReflector
	 */
	function setDataReflector(HashtableReflector $dataReflector)
	{
		$this->dataReflector = $dataReflector;
	}

	
	function createThousandsSeparatorValidator()
	{
		$formatValidator = $this->generalValidationFactory->createFormatValidator("/(.)/", $this->context->getValidThousandsSeparators());
		$lengthValidator = $this->generalValidationFactory->createMaxLengthValidator(1);		
		return $this->generalValidationFactory->createAndValidator($formatValidator, $lengthValidator);
	}
	
	function createDecimalsSeparatorValidator()
	{
		$formatValidator = $this->generalValidationFactory->createFormatValidator("/(.)/", $this->context->getValidDecimalsSeparators());
		$lengthValidator = $this->generalValidationFactory->createMaxLengthValidator(1);		
		return $this->generalValidationFactory->createAndValidator($formatValidator, $lengthValidator);
	}
	
	function createDateFormatValidator()
	{
		return $this->generalValidationFactory->createFormatValidator("/%(.?)/", $this->context->getDateFormatValidSymbols());
	}
	
	function createDateFormatLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getDateFormatMaxLength());
	}
	
	function createDecimalsValidator()
	{
		return $this->generalValidationFactory->createRegexValidator("/^\d?$/");
	}
	
	function createLanguageCaptionLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getLanguageCaptionMaxLength());
	}
	
	function createLanguageNotExistsValidator()
	{
		$source_validator = $this->createLanguageExistsValidator();
		return $this->generalValidationFactory->createNotValidator($source_validator);
	}
	
	function createLanguageExistsValidator()
	{
		$validator = new SJB_LanguageExistsValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		return $validator;
	}
	
	function createLanguageIDLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getLanguageIDMaxLength());
	}

	function createNotEmptyValidator()
	{
		return $this->generalValidationFactory->createNotEmptyValidator();
	}
	
	function createIDSymbolsValidator()
	{
		return $this->generalValidationFactory->createRegexValidator('/^[0-9a-zA-Z_]+$/');
	}
	
	function createLanguageIsDefaultValidator()
	{
		$validator = new SJB_LanguageIsDefaultValidator();
		$validator->setContext($this->context);
		return $validator;	
	}
	
	function createLanguageIsNotDefaultValidator()
	{
		$source_validator = $this->createLanguageIsDefaultValidator();
		return $this->generalValidationFactory->createNotValidator($source_validator);
	}
	
	function createLanguageIsActiveValidator()
	{
		$validator = new SJB_LanguageIsActiveValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		$validator->setLanguageExistsValidator($this->createLanguageExistsValidator());
		return $validator;
	}
	
	function createDefaultLanguageMustBeActiveValidator()
	{
		$validator = new SJB_DefaultLanguageMustBeActiveValidator();
		$validator->setDataReflector($this->dataReflector);
		$validator->setLanguageIsNotDefaultValidator($this->createLanguageIsNotDefaultValidator());
		return $validator;
	}
	
	function createDifferentThousandsAndDecimalSeparatorsValidator()
	{
		$equalToValidator = $this->generalValidationFactory->createEqualToValidator($this->dataReflector->get('decimal_separator'));
		return $this->generalValidationFactory->createNotValidator($equalToValidator);
	}
		
	function createLanguageFileValidator()
	{
		$validator = new SJB_LanguageFileValidator();
		$validator->setDataReflector($this->dataReflector);
		return $validator;	
	}
	
}

