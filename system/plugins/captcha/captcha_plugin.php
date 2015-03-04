<?php

require_once("ReCaptcha.php");
require_once("customCaptcha.php");

class CaptchaPlugin extends SJB_PluginAbstract 
{
	function pluginSettings()
	{
		$reCaptchaPubkey = SJB_Settings::getSettingByName('reCaptchaPubkey');
		$reCaptchaPrivkey = SJB_Settings::getSettingByName('reCaptchaPrivkey');
		if ($reCaptchaPubkey && $reCaptchaPrivkey) {
			$i18n = SJB_I18N::getInstance();
			$lang = $i18n->getCurrentLanguage();
			$theme = SJB_Settings::getSettingByName('reCaptchaTheme');
			$theme = $theme?$theme:'white';
			$reCaptcha = new SJB_ReCaptcha();
			$reCaptcha->setPubkey($reCaptchaPubkey);
			$reCaptcha->setPrivkey($reCaptchaPrivkey);
			$reCaptcha->setOption('lang', $lang);
			$reCaptcha->setOption('theme', $theme);
			$reCaptchaView = $reCaptcha->render();
		}

		$customCaptcha = new SJB_CustomCaptcha();
		$fonts = self::getFonts();
		$font = SJB_Settings::getSettingByName('custCaptchaFont')?SJB_Settings::getSettingByName('custCaptchaFont'):'arial';
		$fontSize = SJB_Settings::getSettingByName('custCaptchaFontSize')?SJB_Settings::getSettingByName('custCaptchaFontSize'):36;
		$width = SJB_Settings::getSettingByName('custCaptchaWidth')?SJB_Settings::getSettingByName('custCaptchaWidth'):200;
		$height = SJB_Settings::getSettingByName('custCaptchaHeight')?SJB_Settings::getSettingByName('custCaptchaHeight'):100;
		$wordlen = SJB_Settings::getSettingByName('custCaptchaWordlen')?SJB_Settings::getSettingByName('custCaptchaWordlen'):5;
		$dotNoise = SJB_Settings::getSettingByName('custDotNoiseLevel')?SJB_Settings::getSettingByName('custDotNoiseLevel'):100;
		$lineNoise = SJB_Settings::getSettingByName('custLineNoiseLevel')?SJB_Settings::getSettingByName('custLineNoiseLevel'):5;
		$customCaptcha->setFont(SJB_BASE_DIR."system/plugins/captcha/fonts/".$fonts[$font]);
		$customCaptcha->setFontSize($fontSize);
		$customCaptcha->setImgDir(SJB_BASE_DIR . 'system/cache/captcha/');
		$customCaptcha->setImgUrl(str_replace('admin', '', SJB_System::getSystemSettings('SITE_URL')).'/system/cache/captcha/');
		$customCaptcha->setWidth($width);
		$customCaptcha->setHeight($height);
		$customCaptcha->setWordlen($wordlen);
		$customCaptcha->setDotNoiseLevel($dotNoise);
		$customCaptcha->setLineNoiseLevel($lineNoise);
		$customCaptcha->setKeepSession(true);
		$customCaptchaView = '';
		try {
			$customCaptcha->generate();
			$customCaptchaView = $customCaptcha->render();
		}
		catch (Exception $ex) {
			$customCaptchaView = $ex->getMessage();
		}

		return array(
			array (
				'id'			=> 'captcha_type',
				'caption'		=> '',
				'type'			=> 'list',
				'list_values'	=> array(
					array(
					'id'		=>'kCaptcha',
					'caption'	=> 'kCaptcha',
					),
					array(
					'id'		=>'reCaptcha',
					'caption'	=> 'ReCaptcha',
					'view'      => $reCaptchaView,
					),
					array(
					'id'		=>'customCaptcha',
					'caption'	=> 'Custom Captcha',
					'view' 		=> $customCaptchaView,
					),
				),
				'length'		=> '50',
				'order'			=> null,
			),
		);
	}

	public static function editCaptcha($info)
	{
		$captchaType = !empty($info['type'])?$info['type']:'';
		$info['template'] = '../../../system/plugins/captcha/edit_captcha.tpl';
		$event = !empty($info['event'])?$info['event']:false;
		$settings = array();
		$errors = array();
		switch ($captchaType) {
			case 'reCaptcha':
				if ($event == 'save' && !empty($info['settings'])) {
					foreach ($info['settings'] as $setting => $val) {
						if ($val == '')
							$errors[$setting] = 'empty';
					}
					if (!$errors) {
						SJB_Settings::updateSettings($info['settings']);
						if (SJB_Request::getVar('submit') == 'save') {
                            SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL').'/system/miscellaneous/plugins/?action=settings&plugin=CaptchaPlugin');
                        }
					}
				}
				$settings = array(
					array (
						'id'			=> 'reCaptchaPubkey',
						'caption'		=> 'Public Key',
						'type'			=> 'string',
						'length'		=> '50',
						'order'			=> null,
						'comment'		=> 'To get these Keys go to http://google.com/recaptcha/admin/create,<br/> register/sign in and then create a reCAPTCHA key following the given instructions.'
					),
					array (
						'id'			=> 'reCaptchaPrivkey',
						'caption'		=> 'Private Key',
						'type'			=> 'string',
						'length'		=> '50',
						'order'			=> null,
						'comment'		=> 'To get these Keys go to http://google.com/recaptcha/admin/create,<br/> register/sign in and then create a reCAPTCHA key following the given instructions.'
					),
					array (
						'id'			=> 'reCaptchaTheme',
						'caption'		=> 'Theme',
						'type'			=> 'list',
						'list_values'	=> array(
							array(
							'id'		=>'red',
							'caption'	=> 'red',
							),
							array(
							'id'		=>'white',
							'caption'	=> 'white',
							),
							array(
							'id'		=>'blackglass',
							'caption'	=> 'blackglass',
							),
							array(
							'id'		=>'clean',
							'caption'	=> 'clean',
							),
						),
						'length'		=> '50',
						'order'			=> null,
					),
				);

				break;
			case 'customCaptcha':
				if ($event == 'save' && !empty($info['settings'])) {
					foreach ($info['settings'] as $setting => $val) {
						if ($val == '')
							$errors[$setting] = 'empty';
					}
					if (!$errors) {
						SJB_Settings::updateSettings($info['settings']);
						if (SJB_Request::getVar('submit') == 'save') {
                            SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL').'/system/miscellaneous/plugins/?action=settings&plugin=CaptchaPlugin');
                        }
					}
				}
				$fontsDir = dir(SJB_BASE_DIR."system/plugins/captcha/fonts");
				$fonts = array();
				$i = 0;
				while (false !== ($entry = $fontsDir->read())) {
					if (strstr($entry, '.ttf') || strstr($entry, '.TTF')) {
				 		$fonts[$i]['id'] = $fonts[$i]['caption'] = str_replace('.TTF', '', str_replace('.ttf', '', $entry));
				 		$i++;
					}
				}

				$settings = array(
					array (
						'id'			=> 'custCaptchaFont',
						'caption'		=> 'Font',
						'type'			=> 'list',
						'length'		=> '50',
						'list_values'	=> $fonts,
						'order'			=> null,
					),
					array (
						'id'			=> 'custCaptchaFontSize',
						'caption'		=> 'Font Size',
						'type'			=> 'integer',
						'length'		=> '50',
						'comment'		=> 'Allow you to specify the font size in pixels for generating the CAPTCHA. The default is 24px.',
						'order'			=> null,
					),
					array (
						'id'			=> 'custCaptchaHeight',
						'caption'		=> 'Height',
						'type'			=> 'integer',
						'length'		=> '50',
						'order'			=> null,
					),
					array (
						'id'			=> 'custCaptchaWidth',
						'caption'		=> 'Width',
						'type'			=> 'integer',
						'length'		=> '50',
						'order'			=> null,
					),
					array (
						'id'			=> 'custCaptchaWordlen',
						'caption'		=> 'Word Length',
						'type'			=> 'integer',
						'comment'		=> 'Allow you to specify the length of the generated "word" in characters.',
						'length'		=> '50',
						'order'			=> null,
					),
					array (
						'id'			=> 'custDotNoiseLevel',
						'caption'		=> 'Dot Noise Level',
						'type'			=> 'integer',
						'length'		=> '50',
						'order'			=> null,
					),
					array (
						'id'			=> 'custLineNoiseLevel',
						'caption'		=> 'Line Noise Level',
						'type'			=> 'integer',
						'length'		=> '50',
						'order'			=> null,
					)
				);
				break;
		}
		$info['fieldErrors'] = $errors;
		$info['savedSettings'] = SJB_Settings::getSettings();
		$info['settings'] = $settings;
		return $info;
	}

	public static function getFonts()
	{
		$fontsDir = dir(SJB_BASE_DIR."system/plugins/captcha/fonts");
		$fonts = array();
		$i = 0;
		while (false !== ($entry = $fontsDir->read())) {
			if (strstr($entry, '.ttf') || strstr($entry, '.TTF')) {
				$fonts[str_replace('.TTF', '', str_replace('.ttf', '', $entry))] = $entry;
			}
		}

		return $fonts;
	}

	public static function getCaptchaProperties($properties)
	{
		$captcha_type = SJB_Settings::getSettingByName('captcha_type');
		switch ($captcha_type){
			case 'reCaptcha':
				$reCaptchaPubkey = SJB_Settings::getSettingByName('reCaptchaPubkey');
				$reCaptchaPrivkey = SJB_Settings::getSettingByName('reCaptchaPrivkey');
				if ( $reCaptchaPubkey && $reCaptchaPrivkey) {
					$i18n = SJB_I18N::getInstance();
					$lang = $i18n->getCurrentLanguage();
					$theme = SJB_Settings::getSettingByName('reCaptchaTheme');
					$theme = $theme?$theme:'white';
					$reCaptcha = new SJB_ReCaptcha();
					$reCaptcha->setPubkey($reCaptchaPubkey);
					$reCaptcha->setPrivkey($reCaptchaPrivkey);
					$reCaptcha->setOption('lang', $lang);
					$reCaptcha->setOption('theme', $theme);
					$reCaptchaView = $reCaptcha->render(null, null, $properties['windowType']);
					$properties['type'] = 'reCaptcha';
					$properties['captchaView'] = $reCaptchaView;
				}
				break;
			case 'customCaptcha':
					$customCaptcha = new SJB_CustomCaptcha();
					$fonts = self::getFonts();
					$font = SJB_Settings::getSettingByName('custCaptchaFont')?SJB_Settings::getSettingByName('custCaptchaFont'):'arial';
					$fontSize = SJB_Settings::getSettingByName('custCaptchaFontSize')?SJB_Settings::getSettingByName('custCaptchaFontSize'):36;
					$width = SJB_Settings::getSettingByName('custCaptchaWidth')?SJB_Settings::getSettingByName('custCaptchaWidth'):200;
					$height = SJB_Settings::getSettingByName('custCaptchaHeight')?SJB_Settings::getSettingByName('custCaptchaHeight'):100;
					$wordlen = SJB_Settings::getSettingByName('custCaptchaWordlen')?SJB_Settings::getSettingByName('custCaptchaWordlen'):5;
					$dotNoise = SJB_Settings::getSettingByName('custDotNoiseLevel')?SJB_Settings::getSettingByName('custDotNoiseLevel'):100;
					$lineNoise = SJB_Settings::getSettingByName('custLineNoiseLevel')?SJB_Settings::getSettingByName('custLineNoiseLevel'):5;
					$customCaptcha->setFont(SJB_BASE_DIR."system/plugins/captcha/fonts/".$fonts[$font]);
					$customCaptcha->setFontSize($fontSize);
					$customCaptcha->setImgDir(SJB_BASE_DIR . 'system/cache/captcha/');
					$customCaptcha->setImgUrl(str_replace('admin', '', SJB_System::getSystemSettings('SITE_URL')).'/system/cache/captcha/');
					$customCaptcha->setWidth($width);
					$customCaptcha->setHeight($height);
					$customCaptcha->setWordlen($wordlen);
					$customCaptcha->setDotNoiseLevel($dotNoise);
					$customCaptcha->setLineNoiseLevel($lineNoise);
					$customCaptcha->setKeepSession(true);
					$customCaptcha->generate();
					$customCaptchaView = $customCaptcha->render();
					$properties['type'] = 'customCaptcha';
					$properties['captchaView'] = $customCaptchaView;
					$properties['id'] = $customCaptcha->getId();
				break;
		}
		return $properties;
	}
	
	public static function captchaValidation($property_info)
	{
		$captcha_type = SJB_Settings::getSettingByName('captcha_type');
		$validation = false;
		switch ($captcha_type){
			case 'reCaptcha':
				$reCaptchaPubkey = SJB_Settings::getSettingByName('reCaptchaPubkey');
				$reCaptchaPrivkey = SJB_Settings::getSettingByName('reCaptchaPrivkey');
				$reCaptcha = new Zend_Captcha_ReCaptcha();
				$reCaptcha->setPubkey($reCaptchaPubkey);
				$reCaptcha->setPrivkey($reCaptchaPrivkey);
				$validation = $reCaptcha->isValid($property_info['value']); 
				break;
			case 'customCaptcha':
				$customCaptcha = new SJB_CustomCaptcha();
				$validation = $customCaptcha->isValid($property_info['value']);
				break;
			case 'kCaptcha':
					$validation = $property_info;
				break;
		}
		return $validation;
	}
	
	public static function getPropertyInfo($property_info)
	{
		$captcha_type = SJB_Settings::getSettingByName('captcha_type');
		switch ($captcha_type){
			case 'reCaptcha':
				$property_info['value']['recaptcha_challenge_field'] = SJB_Request::getVar('recaptcha_challenge_field','');
				$property_info['value']['recaptcha_response_field'] = SJB_Request::getVar('recaptcha_response_field','');
			break;
		}
		return $property_info;
	}
}
//http://framework.zend.com/manual/en/zend.captcha.operation.html