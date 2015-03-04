<?php

class SJB_I18N 
{	
	/**
	 * Транслятор
	 *
	 * @var I18NTranslator
	 */
	var $translator;
	
	/**
	 * @var I18NSwitchLanguageAgent
	 */
	var $langSwitcher;
	
	/**
	 * 
	 * @var SJB_I18NContext
	 */
	protected $context = null;
	
	/**
	 * 
	 * @var I18NAdmin
	 */
	private $admin = null;
	
	/**
	 * 
	 * @var I18NFormatterFactory
	 */
	public $formatterFactory = null;

	/**
	 * @var SJB_LanguageValidatorFactory
	 */
	public $languageValidatorFactory;

	/**
	 * @var SJB_TranslationValidatorFactory
	 */
	public $translationValidatorFactory;

	/**
	 * @var ReflectionFactory
	 */
	public $reflectionFactory;

	/**
	 * @var I18NPhraseSearcher
	 */
	public $phraseSearcher;

	/**
	 * @var I18NPhraseSearchCriteriaFactory
	 */
	public $phraseSearchCriteriaFactory;

	/**
	 * @var I18NFileHelper
	 */
	public $fileHelper;

	/**
	 * I18N
	 *
	 * @return SJB_I18N
	 */
	public static function getInstance()
	{
		if (!isset($GLOBALS['I18N_Instance'])) {
			$GLOBALS['I18N_Instance'] = SJB_I18N::create();
		}
		return $GLOBALS['I18N_Instance'];
	}

	function switchLang()
	{
		$this->langSwitcher->execute();
	}

	/**
	 * @static
	 * @return SJB_I18N
	 */
	public static function create()
	{
		$instance 		= new SJB_I18N();
		$settings 		= new SJB_Settings();
		$systemSettings = new SJB_System();
		$session	 	= new SJB_Session();
		
		$dateFormatter 		= new SJB_DateFormatter();
		$languageSettings 	= new I18NLanguageSettings();
		$context 			= new I18NContext();
		$admin 				= new I18NAdmin();
		$translator 		= new I18NTranslator();
		$datasource 		= I18NDataSource::getInstance();
			
		$langSwitcher 		= new I18NSwitchLanguageAgent();
		
		$translationValidatorFactory 	= new SJB_TranslationValidatorFactory();
		$languageValidatorFactory 		= new SJB_LanguageValidatorFactory();
		$generalValidationFactory 		= new GeneralValidationFactory();
		$reflectionFactory 				= new ReflectionFactory();
		
		$phraseSearcher 				= new I18NPhraseSearcher();
		$fullTextMatcher 				= new FullTextMatcher();
		$phraseSearchCriteriaFactory	= new I18NPhraseSearchCriteriaFactory();
		
		$formatterFactory 				= new I18NFormatterFactory();
		
		$fileHelper 					= new I18NFileHelper();
		
		$langSwitcher->setContext($context);
		$langSwitcher->setSession($session);
		$langSwitcher->setI18N($instance);

		$context->setSettings($settings);
		$context->setSession($session);
		$context->setLanguageSettings($languageSettings);
		$context->setSystemSettings($systemSettings);

		$fileSystem = SJB_ObjectMother::createFileSystem();
		$fileHelper->setContext($context);
		$fileHelper->setFileSystem($fileSystem);
		
		$datasource->init($context, $fileHelper);
		$admin->setDataSource($datasource);
		
		$languageSettings->setContext($context);
		$languageSettings->setDataSource($datasource);

		$translator->setContext($context);
		$translator->setDatasource($datasource);
		
		$languageValidatorFactory->setContext($context);
		$languageValidatorFactory->setGeneralValidationFactory($generalValidationFactory);
		$languageValidatorFactory->setReflectionFactory($reflectionFactory);
		$languageValidatorFactory->setLanguageDataSource($datasource);

		$translationValidatorFactory->setContext($context);
		$translationValidatorFactory->setGeneralValidationFactory($generalValidationFactory);
		$translationValidatorFactory->setReflectionFactory($reflectionFactory);
		$translationValidatorFactory->setLanguageDataSource($datasource);
		
		$phraseSearcher->setDataSource($datasource);
		$phraseSearcher->setMatcher($fullTextMatcher);
		
		$formatterFactory->setContext($context);
		
		$instance->setTranslator($translator);
		$instance->setAdmin($admin);
		$instance->setLangSwitcher($langSwitcher);
		$instance->setContext($context);
		$instance->setLanguageValidatorFactory($languageValidatorFactory);
		$instance->setTranslationValidatorFactory($translationValidatorFactory);
		$instance->setReflectionFactory($reflectionFactory);
		$instance->setPhraseSearcher($phraseSearcher);
		$instance->setPhraseSearchCriteriaFactory($phraseSearchCriteriaFactory);
		$instance->setFormatterFactory($formatterFactory);
		$instance->setFileHelper($fileHelper);
		
		return $instance;
	}

	/**
	 * @param I18NTranslator $translator
	 */
	function setTranslator(I18NTranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param I18NAdmin $admin
	 */
	function setAdmin(I18NAdmin $admin)
	{
		$this->admin = $admin;
	}

	/**
	 * @param I18NSwitchLanguageAgent $langSwitcher
	 */
	function setLangSwitcher(I18NSwitchLanguageAgent $langSwitcher)
	{
		$this->langSwitcher = $langSwitcher;
	}

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @param SJB_LanguageValidatorFactory $factory
	 */
	function setLanguageValidatorFactory(SJB_LanguageValidatorFactory $factory)
	{
		$this->languageValidatorFactory = $factory;
	}

	/**
	 * @param SJB_TranslationValidatorFactory $factory
	 */
	function setTranslationValidatorFactory(SJB_TranslationValidatorFactory $factory)
	{
		$this->translationValidatorFactory = $factory;
	}

	/**
	 * @param ReflectionFactory $factory
	 */
	function setReflectionFactory(ReflectionFactory $factory)
	{
		$this->reflectionFactory = $factory;
	}

	/**
	 * @param I18NPhraseSearcher $phraseSearcher
	 */
	function setPhraseSearcher(I18NPhraseSearcher $phraseSearcher)
	{
		$this->phraseSearcher = $phraseSearcher;
	}

	/**
	 * @param I18NPhraseSearchCriteriaFactory $phraseSearchCriteriaFactory
	 */
	function setPhraseSearchCriteriaFactory(I18NPhraseSearchCriteriaFactory $phraseSearchCriteriaFactory)
	{
		$this->phraseSearchCriteriaFactory = $phraseSearchCriteriaFactory;
	}

	/**
	 * @param I18NFormatterFactory $formatterFactory
	 */
	function setFormatterFactory(I18NFormatterFactory $formatterFactory)
	{
		$this->formatterFactory = $formatterFactory;
	}

	/**
	 * @param I18NFileHelper $fileHelper
	 */
	function setFileHelper(I18NFileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}

	/**
	 * @return string
	 */
	function getDefaultDomain()
	{
		return $this->context->getDefaultDomain();
	}

	/**
	 * @return I18NFileHelper
	 */
	function getFileHelper()
	{
		return $this->fileHelper;
	}
	
	function gettext($domain_id, $phrase_id, $mode = null)
	{
		return $this->translator->gettext($domain_id, $phrase_id, $mode);
	}
	
	function getInt($number)
	{
		$formatter = $this->formatterFactory->getIntFormatter();
		return $formatter->getOutput($number);
	}

	function getFloat($number)
	{
		$formatter = $this->formatterFactory->getFloatFormatter();
		return $formatter->getOutput($number);
	}

	function getDate($date)
	{
		$formatter = $this->formatterFactory->getDateFormatter();
		return $formatter->getOutput($date);
	}
	
	function getInput($type, $value)
	{
		if (!$this->formatterFactory->doesFormatterExist($type)) {
			SJB_Logger::error('UNDEFINED_TYPE');
			return $value;
		}
		
		$formatter = $this->formatterFactory->getFormatter($type);
		return $formatter->getInput($value);
	}
	
	function isValidFloat($value)
	{
		$formatter = $this->formatterFactory->getFloatFormatter();
		return $formatter->isValid($value);
	}
	
	function isValidInteger($value)
	{
		$formatter = $this->formatterFactory->getIntFormatter();
		return $formatter->isValid($value);
	}
	
	function isValidDate($value)
	{
		$formatter = $this->formatterFactory->getDateFormatter();
		return $formatter->isValid($value);
	}
		
	function getDomainsData() 
	{		
		$domainsData = $this->admin->getDomainsData();
		$result = array();
		for ($i = 0; $i < count($domainsData); $i++) {
			$result[] = $domainsData[$i]->getID();
		}
		return $result;
	}
	
	function &searchPhrases(&$criteria)
	{		
		$phrasesData =& $this->phraseSearcher->search($criteria);
		
		foreach (array_keys($phrasesData) as $i) {
			$phraseData = $phrasesData[$i];
			
			$translationsData = $phraseData->getTranslations();
			$translations = array();
			foreach ($translationsData as $key => $value){
				$translationData = $translationsData[$key];
				$translations[$translationData->getLanguageID()] = $translationData->getTranslation();
			}
			$phrase_data = array(
				'id'			=> $phraseData->getID(),
				'domain'		=> $phraseData->getDomainID(),
				'translations'	=> $translations,
			);
			
			$phrases_data[] = $phrase_data;
		}
		
		return $phrases_data;
	}
	
	function &getPhraseSearchCriteriaFactory()
	{
		return $this->phraseSearchCriteriaFactory;
	}
	
	function phraseExists($phraseId, $domainId) 
	{
		$domainExistsValidator = $this->translationValidatorFactory->createDomainExistsValidator();
		
		$dataReflector = $this->reflectionFactory->createConstantReflector($domainId);		
		$phraseExistsValidator = $this->translationValidatorFactory->createPhraseExistsValidator();
		$phraseExistsValidator->setDataReflector($dataReflector);
		
		return $domainExistsValidator->isValid($domainId) && $phraseExistsValidator->isValid($phraseId);
	}
    
	function translationIsValid($translations)
	{
		return true;
	}

	function addDomain($name) 
	{		
		return $this->admin->addDomain($name);
	}	
	
	function addPhrase($phrase_data) 
	{		
		$phraseData = PhraseData::createPhraseDataFromClient($phrase_data);
		return $this->admin->addPhrase($phraseData);
	}	
	
	function updatePhrase($phrase_data) 
	{
		$phraseData = PhraseData::createPhraseDataFromClient($phrase_data);
		return $this->admin->updatePhrase($phraseData);
	}	
	
	function deletePhrase($phrase_id, $domain_id) 
	{
		return $this->admin->deletePhrase($phrase_id, $domain_id);
	}	
	
	function getPhraseData($phrase_id, $domain_id)
	{
		$phraseData =& $this->admin->getPhraseData($phrase_id, $domain_id);
		
		$translations = array();
		$translationsData = $phraseData->getTranslations();
		
		foreach ($translationsData as $key => $value) {
			$translationData = $translationsData[$key];
			$translations[$translationData->getLanguageID()] = $translationData->getTranslation();
		}
		
		$phrase_data = array(
			'id'			=> $phraseData->getID(),
			'domain'		=> $phraseData->getDomainID(),
			'translations'	=> $translations,
		);
		
		return $phrase_data;
	}
	
	function createAddTranslationValidator($translations)
	{
		return $this->translationValidatorFactory->createAddTranslationValidator($translations);
	}
	
	function createUpdateTranslationValidator($translations)
	{
		return $this->translationValidatorFactory->createUpdateTranslationValidator($translations);
	}
	
	/********** L A N G U A G E S **********/
	/**
	 * @param array $lang_data
	 */
	function addLanguage($lang_data) 
	{
		$langData = LangData::createLangDataFromClient($lang_data);
		$this->admin->addLanguage($langData);
	}
	
	function getLanguageData($lang_id) 
	{		
		$langData = $this->admin->getLanguageData($lang_id);		
		
		$lang_data = array (
			'id' 					=> $langData->getID(),
			'caption' 				=> $langData->getCaption(),
			'activeFrontend' 		=> $langData->getActiveFrontend(),
			'activeBackend' 		=> $langData->getActiveBackend(),
			'is_default' 			=> $this->context->getDefaultLang() === $langData->getID(),
			'theme' 				=> $langData->getTheme(),
			'date_format' 			=> $langData->getDateFormat(),
			'decimal_separator' 	=> $langData->getDecimalSeparator(),
			'thousands_separator' 	=> $langData->getThousandsSeparator(),	
			'decimals' 				=> $langData->getDecimals(),
			'rightToLeft'			=> $langData->getRightToLeft(),
			'currencySignLocation'  => $langData->getCurrencySignLocation(),
		);
		
		return $lang_data;
	}
	
	function updateLanguage($lang_data)
	{
		$langData = LangData::createLangDataFromClient($lang_data);
		$this->admin->updateLanguage($langData);
	}	
	
	function deleteLanguage($lang_id)
	{
		if ($this->admin->deleteLanguage($lang_id)) {
			if ($lang_id == $this->context->getLang()) {
				$defaultLang = $this->context->getDefaultLang();
				$this->context->setLang($defaultLang);
				SJB_System::setGlobalTemplateVariable('current_language', $defaultLang);
			}
			return true;
		}
		return false;
	}
		
	function getLanguagesData() 
	{
		$langs_data = array();
		$langsData = $this->admin->getLanguagesData();
		
		foreach($langsData as $langData) {
			$langs_data[] = array(
				'id' 					=> $langData->getID(),
				'caption' 				=> $langData->getCaption(),
				'activeFrontend' 		=> $langData->getActiveFrontend(),
				'activeBackend' 		=> $langData->getActiveBackend(),
				'is_default' 			=> $this->context->getDefaultLang() === $langData->getID(),
				'theme' 				=> $langData->getTheme(),
				'date_format' 			=> $langData->getDateFormat(),
				'decimal_separator' 	=> $langData->getDecimalSeparator(),
				'thousands_separator' 	=> $langData->getThousandsSeparator(),	
				'decimals' 				=> $langData->getDecimals(),
				'rightToLeft'			=> $langData->getRightToLeft(),
				'currencySignLocation'  => $langData->getCurrencySignLocation(),
			);
		}
		
		return $langs_data;	
	}
		
	function getActiveLanguagesData() 
	{
		$langs_data = array();
		$langsData = $this->admin->getLanguagesData();

		foreach($langsData as $langData) {
			if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
				$lang_is_active = $langData->getActiveFrontend();
			} else {
				$lang_is_active = $langData->getActiveBackend();
			}

			if ($lang_is_active) {
				$langs_data[] = array(
					'id' 					=> $langData->getID(),
					'caption' 				=> $langData->getCaption(),
					'activeFrontend' 		=> $langData->getActiveFrontend(),
					'activeBackend' 		=> $langData->getActiveBackend(),
					'is_default' 			=> $this->context->getDefaultLang() === $langData->getID(),
					'theme' 				=> $langData->getTheme(),
					'date_format' 			=> $langData->getDateFormat(),
					'decimal_separator' 	=> $langData->getDecimalSeparator(),
					'thousands_separator' 	=> $langData->getThousandsSeparator(),	
					'decimals' 				=> $langData->getDecimals(),
					'rightToLeft'			=> $langData->getRightToLeft(),
					'currencySignLocation'  => $langData->getCurrencySignLocation(),
				);
			}
		}
		
		return $langs_data;	
	}

	public function getActiveFrontendLanguagesData()
	{
		$activeLanguagesData = array();
		$availableLanguagesData = $this->admin->getLanguagesData();

		foreach($availableLanguagesData as $langData) {
			$languageIsActive = $langData->getActiveFrontend();

			if ($languageIsActive) {
				$activeLanguagesData[] = array(
					'id' 					=> $langData->getID(),
					'caption' 				=> $langData->getCaption(),
					'activeFrontend' 		=> $langData->getActiveFrontend(),
					'activeBackend' 		=> $langData->getActiveBackend(),
					'is_default' 			=> $this->context->getDefaultLang() === $langData->getID(),
					'theme' 				=> $langData->getTheme(),
					'date_format' 			=> $langData->getDateFormat(),
					'decimal_separator' 	=> $langData->getDecimalSeparator(),
					'thousands_separator' 	=> $langData->getThousandsSeparator(),
					'decimals' 				=> $langData->getDecimals(),
					'rightToLeft'			=> $langData->getRightToLeft(),
					'currencySignLocation'  => $langData->getCurrencySignLocation(),
				);
			}
		}

		return $activeLanguagesData;
	}
	
	function languageExists($lang_id) 
	{
		$validator = $this->languageValidatorFactory->createLanguageExistsValidator();
		return $validator->isValid($lang_id);
	}
	
	function isLanguageActive($lang_id)
	{
		$validator = $this->languageValidatorFactory->createLanguageIsActiveValidator();
		return $validator->isValid($lang_id);
	}
	
	function setDefaultLanguage($lang_id) 
	{
		$this->context->setDefaultLang($lang_id);
	}
	
	function getCurrentLanguage()
	{
		return $this->context->getLang();
	}
	
	function createAddLanguageValidator($lang_data)
	{
		return $this->languageValidatorFactory->createAddLanguageValidator($lang_data);
	}

	function createUpdateLanguageValidator($lang_data)
	{
		return $this->languageValidatorFactory->createUpdateLanguageValidator($lang_data);
	}

	function createDeleteLanguageValidator($lang_id)
	{
		return $this->languageValidatorFactory->createDeleteLanguageValidator($lang_id);
	}
	
	function createSetDefaultLanguageValidator($lang_id)
	{
		return $this->languageValidatorFactory->createSetDefaultLanguageValidator($lang_id);
	}
	
	function createImportLanguageValidator($lang_file_data)
	{
		return $this->languageValidatorFactory->createImportLanguageValidator($lang_file_data);
	}

	function getDomainPhrases($domainId)
	{
		return $this->admin->getDomainPhrases($domainId);
	}
		
	function importLangFile($file_name, $file_path)
	{
		$languageID = $this->fileHelper->getLanguageIDForImportFile($file_name);
		$file_paths = $this->fileHelper->getFilePathToLangFiles($languageID);
		$trAdminFactory = new Translation2AdminFactory();

		$trAdmin = $trAdminFactory->createTrAdmin($file_path, true, true, $file_path);
		$trAdmin->getLanguagePages();
		// set new lang paths
		$trAdmin->storage->setFileName($file_paths['languages']);
		$trAdmin->storage->setPagesFileNameOption($file_paths['pages']);

		//check meta separators
		if (isset($trAdmin->storage->_data['languages'][$languageID]['meta'])) {
			$meta = unserialize($trAdmin->storage->_data['languages'][$languageID]['meta']);
			if (isset($meta['decimal_separator']) && isset($meta['thousands_separator'])) {
				if (!$meta['decimal_separator']) {
					$meta['decimal_separator'] = (!$meta['thousands_separator'] || $meta['thousands_separator'] == ',') ? '.' : ',';
				}
				if (!$meta['thousands_separator']) {
					$meta['thousands_separator'] = (!$meta['decimal_separator'] || $meta['decimal_separator'] == ',') ? '.' : ',';
				}
			} else {
				$meta['decimal_separator'] = '.';
				$meta['thousands_separator'] = ',';
			}
			$trAdmin->storage->_data['languages'][$languageID]['meta'] = serialize($meta);
		}
		$trAdmin->storage->_saveData();
		$fileSystem = SJB_ObjectMother::createFileSystem();
		$fileSystem->deleteFile($file_path);
		return true;
	}
	
	function getFilePathToLangFile($lang_id)
	{
		return $this->fileHelper->getFilePathToLangFile($lang_id);
	}

	function exportLanguage($lang_id)
	{
		return $this->admin->exportLanguage($lang_id);
	}

	function getFileNameForLangExportFile($lang_id)
	{
		return $this->fileHelper->getFileNameForLangExportFile($lang_id);
	}
}

