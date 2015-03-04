<?php


class SJB_TranslationValidatorFactory
{
	var $generalValidationFactory;

	/**
	 * @var I18NDatasource
	 */
	protected $langDataSource;
	
	function createAddTranslationValidator($translation_data)
	{	
		$dataReflector = $this->reflectionFactory->createHashtableReflector($translation_data);
		$this->setDataReflector($dataReflector);
		
		$factoryReflector = $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
		
		$batch->add('phraseId', 'NotEmptyValidator', 'PHRASE_ID_IS_EMPTY');
		$batch->add('phraseId', 'PhraseIDLengthValidator', 'TOO_LONG_PHRASE_ID');
		$batch->add('phraseId', 'PhraseNotExistsValidator', 'PHRASE_ALREADY_EXISTS');
		$batch->add('domainId', 'DomainExistsValidator', 'DOMAIN_NOT_EXISTS');
		for($i = 0; $i < count($translation_data['translations']); $i++){
			$batch->add("['translations'][$i]['LanguageId']", 'LanguageExistsValidator', 'LANGUAGE_NOT_EXISTS');
			$batch->add("['translations'][$i]['Translation']", 'TranslationLengthValidator', array(
					'TOO_LONG_TRANSLATION', $translation_data['translations'][$i]['LanguageCaption']
				)
			);
		}
		return $batch;
	}

	function createUpdateTranslationValidator($translation_data)
	{	
		$dataReflector = $this->reflectionFactory->createHashtableReflector($translation_data);
		$this->setDataReflector($dataReflector);
		
		$factoryReflector = $this->reflectionFactory->createFactoryReflector($this);
		
		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);
		
		$batch->add('phraseId', 'PhraseExistsValidator', 'PHRASE_NOT_EXISTS');
		$batch->add('domainId', 'DomainExistsValidator', 'DOMAIN_NOT_EXISTS');
		for($i = 0; $i < count($translation_data['translations']); $i++){
			$batch->add("['translations'][$i]['LanguageId']", 'LanguageExistsValidator', 'LANGUAGE_NOT_EXISTS');
			$batch->add("['translations'][$i]['Translation']", 'TranslationLengthValidator', array(
					'TOO_LONG_TRANSLATION', $translation_data['translations'][$i]['LanguageCaption']
				)
			);
		}
		return $batch;
	}

	function setDataReflector(&$dataReflector){
		$this->dataReflector =& $dataReflector;
	}

	/**
	 * @param I18NDatasource $langDataSource
	 */
	function setLanguageDataSource($langDataSource)
	{
		$this->langDataSource = $langDataSource;
	}
	
	function setReflectionFactory(&$reflectionFactory)
	{
		$this->reflectionFactory =& $reflectionFactory;
	}
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	function setGeneralValidationFactory(&$generalValidationFactory)
	{
		$this->generalValidationFactory =& $generalValidationFactory;
	}

	function createPhraseIDLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getPhraseIDMaxLength());
	}

	function createPhraseExistsValidator()
	{
		$validator = new SJB_PhraseExistsValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		$validator->setDataReflector($this->dataReflector);
		return $validator;
	}

	function createPhraseNotExistsValidator()
	{
		$source_validator = $this->createPhraseExistsValidator();
		return $this->generalValidationFactory->createNotValidator($source_validator);
	}

	function createDomainExistsValidator()
	{
		$validator = new SJB_DomainExistsValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		return $validator;
	}

	function createLanguageExistsValidator()
	{
		$validator = new SJB_LanguageExistsValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		return $validator;
	}
	
	function createTranslationLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getTranslationMaxLength());
	}

	function createNotEmptyValidator()
	{
		return $this->generalValidationFactory->createNotEmptyValidator();
	}

}

