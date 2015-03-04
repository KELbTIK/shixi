<?php

class SJB_Captcha extends SJB_Object
{
	public $details = null;

	private $isNotValidate = false;
	private $isCaptcha = false;
	private $captchaError = array();

	/**
	 * @var SJB_Captcha
	 */
	private static $instance;

	/**
	 * @var SJB_TemplateProcessor
	 */
	private $tp;

	/**
	 * @var SJB_Form
	 */
	private $captchaForm = null;

	/**
	 * @param SJB_TemplateProcessor $tp
	 * @param array $info
	 * @return SJB_Captcha
	 */
	public static function getInstance(SJB_TemplateProcessor $tp, $info = array())
	{
		if (self::$instance === null) {
			$windowType = SJB_Request::isAjax() ? 'modal' : '';
			self::$instance = new self($info, $windowType);
			self::$instance->createCaptchaForm();
		}
		self::$instance->init($tp);
		return self::$instance;
	}

	/**
	 * @param array $info
	 * @param bool $type
	 */
	private function SJB_Captcha($info = array(), $type = false)
	{
		$this->details = new SJB_CaptchaDetails($info, $type);
	}

	/**
	 * captcha form creating
	 */
	private function createCaptchaForm()
	{
		$this->captchaForm = new SJB_Form($this);
	}

	/**
	 * captcha initialization
	 */
	private function init(SJB_TemplateProcessor $tp)
	{
		if ($this->isCaptchaEnable()) {
			$this->isCaptcha = true;
			$this->tp = $tp;
			$this->captchaForm->registerTags($this->tp);
		}
	}

	/**
	 * @return bool
	 */
	private function isCaptchaEnable()
	{
		$isCaptcha = false;
		if (SJB_PluginManager::isPluginActive('CaptchaPlugin') && SJB_Session::getValue('CURRENT_THEME') != 'mobile') {
			$userType = SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE') ? 'user' : 'admin';
			$captchaSettingName = '';
			if (!$currentFunction = SJB_Request::getVar('currentFunction')) {
				$currentFunction = SJB_Array::get(SJB_System::getModuleManager()->getCurrentFunction(), 1);
			}

			switch ($currentFunction) {
				case 'apply_now':
					$captchaSettingName = 'contactUserCaptcha';
					break;
				case 'flag_listing':
					$captchaSettingName = 'flagListingCaptcha';
					break;
				case 'tell_friend':
					$captchaSettingName = 'tellFriendCaptcha';
					break;
				case 'contact_form':
					$captchaSettingName = 'contactUsCaptcha';
					break;
				default:
					if (SJB_System::getSettingByName('captcha_max_allowed_auth_attempts') != 0) {
						if (SJB_Session::getValue($userType . 'LoginCounter') >= SJB_System::getSettingByName('captcha_max_allowed_auth_attempts')) {
							$isCaptcha = true;
							if (SJB_Session::getValue($userType . 'LoginCounter') == SJB_System::getSettingByName('captcha_max_allowed_auth_attempts')) {
								SJB_Session::setValue($userType . 'LoginCounter', SJB_Session::getValue($userType . 'LoginCounter') + 1);
								$this->isNotValidate = true;
							}
						}
					}
			}
			if (!$isCaptcha) {
				$isCaptcha = SJB_System::getSettingByName($captchaSettingName) == 1;
			}
		}

		return $isCaptcha;
	}

	/**
	 * @param array $errors
	 * @return bool
	 */
	public function isValid(array &$errors = array())
	{
		if (SJB_Request::getMethod() == 'POST' && $this->isCaptcha) {
			if ($this->isNotValidate) {
				return false;
			}
			$this->captchaForm->isDataValid($this->captchaError);
			$errors = array_merge(array_flip($this->captchaError), $errors);
		}

		return $this->captchaError ? false : true;
	}

	/**
	 * Display
	 */
	public function display()
	{
		if ($this->isCaptcha) {
			$this->tp->assign('displayMode', SJB_Request::getVar('displayMode', 'label'));
			$this->tp->assign('captcha', array_pop($this->captchaForm->form_fields));
			$this->tp->display('captchaHandle.tpl');
		}
	}
}

class SJB_CaptchaDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;

	function SJB_CaptchaDetails($info, $type = false)
	{
		$details_info = self::getDetails($type);

		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}
		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

		$sorted_details_info = array();
		foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
			$detail_info['value'] = '';
			if (isset($info[$detail_info['id']]))
				$detail_info['value'] = $info[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($type = false)
	{
		$details =  array
			   (
					array
					(
						'id'		=> 'captcha',
						'caption'	=> 'Enter code from image', 
						'type'		=> 'captcha',
						'length'	=> '20',
						'windowType'=> $type,
						'is_required'=> true,
						'is_system'=> true,
						'order'			=> 1,
					),
				);
				
		return $details;
	}
}
	