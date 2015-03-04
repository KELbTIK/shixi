<?php

class SJB_Admin_Miscellaneous_Plugins extends SJB_Function
{
	private $socialMediaPlugins = array(
		'LinkedinSocialPlugin'      => 'linkedin',
		'FacebookSocialPlugin'      => 'facebook',
		'GooglePlusSocialPlugin'    => 'googleplus',
		'TwitterIntegrationPlugin'  => 'twitter'
	);

	public function isAccessible()
	{
		$plugin = SJB_Request::getVar('plugin', null);
		switch ($plugin) {
			case 'PhpBBBridgePlugin':
				$this->setPermissionLabel('set_phpbb_plug-in');
				break;
			case 'LinkedinSocialPlugin':
				$this->setPermissionLabel('set_linkedin_plug-in');
				break;
			case 'FacebookSocialPlugin':
				$this->setPermissionLabel('set_facebook_plug-in');
				break;
			case 'WordPressBridgePlugin':
				$this->setPermissionLabel('set_wordpress_plug-in');
				break;
			case 'TwitterIntegrationPlugin':
				$this->setPermissionLabel('set_twitter_plug-in');
				break;
			case 'ShareThisPlugin':
				$this->setPermissionLabel('set_sharethisplugin');
				break;
			case 'CaptchaPlugin':
				$this->setPermissionLabel('set_captchaplugin');
				break;
			case 'IndeedPlugin':
				$this->setPermissionLabel('set_indeedplugin');
				break;
			case 'JujuPlugin':
				$this->setPermissionLabel('set_jujuplugin');
				break;
			case 'SimplyHiredPlugin':
				$this->setPermissionLabel('set_simplyhiredplugin');
				break;
			case 'GooglePlusSocialPlugin':
				$this->setPermissionLabel('set_googleplusplugin');
				break;
			case 'GoogleAnalyticsPlugin':
				$this->setPermissionLabel('set_googleanalyticsplugin');
				break;
			case 'BeyondPlugin':
				$this->setPermissionLabel('set_beyondplugin');
				break;

			default:
				$this->setPermissionLabel(
					array(
						'manage_plug-ins',
						'set_phpbb_plug-in',
						'set_linkedin_plug-in',
						'set_facebook_plug-in',
						'set_wordpress_plug-in',
						'set_twitter_plug-in',
						'set_sharethisplugin',
						'set_captchaplugin',
						'set_indeedplugin',
						'set_jujuplugin',
						'set_simplyhiredplugin',
						'set_googleplugin',
						'set_googleplusplugin',
						'set_googleanalyticsplugin',
						'set_beyondplugin',
					)
				);
				break;
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$saved = false;
		$action = SJB_Request::getVar('action');
		$form_submitted = SJB_Request::getVar('submit');
		$template = 'plugins.tpl';
		$errors = array();
		if (SJB_Request::getVar('error', false))
			$errors[] = SJB_Request::getVar('error', false);
		$messages = array();
		if (SJB_Request::getVar('message', false))
			$messages[] = SJB_Request::getVar('message', false);

		switch ($action) {
			case 'save':
				$paths = SJB_Request::getVar('path');
				$active = SJB_Request::getVar('active');
				$subAdminSID = SJB_SubAdmin::getSubAdminSID();
				if (SJB_System::getSystemSettings('isDemo'))
					$errors[] = 'You don\'t have permissions for it. This is a Demo version of the software.';
				else {
					foreach ($paths as $key => $path) {
						$config = SJB_PluginManager::getPluginConfigFromIniFile($path);
						// check subadmins permissions
						if ($subAdminSID) {
							switch ($key) {
								case 'FacebookSocialPlugin':
									if (!$this->acl->isAllowed('set_facebook_plug-in', $subAdminSID))
										continue(2);
									break;
								case 'LinkedinSocialPlugin':
									if (!$this->acl->isAllowed('set_linkedin_plug-in', $subAdminSID))
										continue(2);
									break;
								case 'PhpBBBridgePlugin':
									if (!$this->acl->isAllowed('set_phpbb_plug-in', $subAdminSID))
										continue(2);
									break;
								case 'TwitterIntegrationPlugin':
									if (!$this->acl->isAllowed('set_twitter_plug-in', $subAdminSID))
										continue(2);
									break;
								case 'WordPressBridgePlugin':
									if (!$this->acl->isAllowed('set_wordpress_plug-in', $subAdminSID))
										continue(2);
									break;
								case 'ShareThisPlugin':
									if (!$this->acl->isAllowed('set_sharethisplugin', $subAdminSID))
										continue(2);
									break;
								case 'CaptchaPlugin':
									if (!$this->acl->isAllowed('set_captchaplugin', $subAdminSID))
										continue(2);
									break;
								case 'IndeedPlugin':
									if (!$this->acl->isAllowed('set_indeedplugin', $subAdminSID))
										continue(2);
									break;
								case 'JujuPlugin':
									if (!$this->acl->isAllowed('set_jujuplugin', $subAdminSID))
										continue(2);
									break;
								case 'SimplyHiredPlugin':
									if (!$this->acl->isAllowed('set_simplyhiredplugin', $subAdminSID))
										continue(2);
									break;
								case 'GoogleAnalyticsPlugin':
									if (!$this->acl->isAllowed('set_googleanalyticsplugin', $subAdminSID))
										continue(2);
									break;
								case 'BeyondPlugin':
									if (!$this->acl->isAllowed('set_beyondplugin', $subAdminSID))
										continue(2);
									break;
							}
						}
						$config['active'] = $active[$key];
						$saved = SJB_PluginManager::savePluginConfigIntoIniFile($path, $config);
						if (!$saved)
							$errors[] = 'Failed to save ' . $key . ' settings';
					}
				}
				SJB_PluginManager::reloadPlugins();
				break;

			case 'save_settings':
				$request = $_REQUEST;
				$request = self::checkRequiredFields($request);
				if (!isset($request['setting_errors'])) {
					SJB_Settings::updateSettings($request);
					if ($form_submitted == 'save') {
						break;
					} else if ($form_submitted == 'apply') {
						$pluginName = SJB_Request::getVar('plugin');
						SJB_HelperFunctions::redirect('?action=settings&plugin=' . $pluginName);
					}
				}
				else {
					unset($request['setting_errors']);
					$errors = $request;
				}

			case 'settings':
				$pluginName = SJB_Request::getVar('plugin');
				$plugin = SJB_PluginManager::getPluginByName($pluginName);
				if (isset($plugin['name'])) {
					$pluginObj = new $plugin['name'];
					$settings = $pluginObj->pluginSettings();
					$template = 'plugin_settings.tpl';
					$savedSettings = SJB_Settings::getSettings();
					SJB_Event::dispatch('RedefineSavedSetting', $savedSettings, true);
					SJB_Event::dispatch('RedefineTemplateName', $template, true);
					$tp->assign('plugin', $plugin);
					$tp->assign('settings', $settings);
					$tp->assign('savedSettings', $savedSettings);
				}
				break;

			case 'editCaptcha':
				$info = $_REQUEST;
				SJB_Event::dispatch('editCaptcha', $info, true);
				foreach ($info as $key => $val) {
					$tp->assign($key, $val);
				}
				$template = $info['template'];
				break;
		}

		$listPlugins = SJB_PluginManager::getAllPluginsList();
		$plugins = array();
		foreach ($listPlugins as $key => $plugin) {
			$group = !empty($plugin['group']) ? $plugin['group'] : 'Common';
			$plugins[$group][$key] = $plugin;
			if (array_key_exists($key, $this->socialMediaPlugins)) {
				$plugins[$group][$key]['socialMedia'] = $this->socialMediaPlugins[$key];
			}
		}
		$tp->assign('saved', $saved);
		$tp->assign('groups', $plugins);
		$tp->assign('errors', $errors);
		$tp->assign('messages', $messages);
		$tp->display($template);
	}

	public static function checkRequiredFields($settings)
	{
		if (isset($settings['plugin'])) {
			$pluginObj      = new $settings['plugin'];
			$settingsFields = $pluginObj->pluginSettings();
			$errors         = array();
			foreach ($settingsFields as $settingsField) {
				if (!empty($settingsField['is_required']) && $settingsField['is_required'] === true && empty($settings[$settingsField['id']])) {
					if (($settingsField['id'] != 'jobAMaticDomain') || (empty($settingsField['simplyHiredSiteUrl']) && ($settings['simplyHiredSiteUrl'] == 'simplyhired.com') && ($settingsField['id'] == 'jobAMaticDomain'))) {
						$errors[$settingsField['caption']] = $settingsField['caption'].' is empty';
					}
				}
				else if (!empty($settingsField['validators'])) {
					foreach ($settingsField['validators'] as $validator) {
						$isValid = $validator::isValid($settings[$settingsField['id']]);
						if ($isValid !== true) {
							$errors[$settingsField['caption']] = $isValid;
						}
					}
				}
			}

			if ($errors) {
				$errors['setting_errors'] = true;
				return $errors;
			}
		}

		return $settings;
	}
}