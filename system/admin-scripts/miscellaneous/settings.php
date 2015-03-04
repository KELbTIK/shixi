<?php

class SJB_Admin_Miscellaneous_Settings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('configure_system_settings');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$form_submitted = SJB_Request::getVar('action');
		$page = SJB_Request::getVar('page');

		if ($form_submitted) {
			if (SJB_System::getSystemSettings("isDemo")) {
				$errors[] = "You don't have permissions for it. This is a Demo version of the software.";
			}
			else {
				if (!empty($_REQUEST['bad_words'])) {
					$_REQUEST['bad_words'] = trim($_REQUEST['bad_words']);
				}
				SJB_Settings::updateSettings($_REQUEST);
			}

			if ($form_submitted == 'apply_settings') {
				$tp->assign("page", $page);
			}
		}

		$i18n = SJB_I18N::getInstance();
		$tp->assign("settings", SJB_Settings::getSettings());
		$ds = DIRECTORY_SEPARATOR;
		$path = SJB_BASE_DIR . "system{$ds}cache{$ds}agents_bots.txt";
		$disable_bots = file_get_contents($path);
		$tp->assign("disable_bots", $disable_bots);
		$tp->assign("timezones", timezone_identifiers_list());

		if (!SJB_SubAdmin::getSubAdminSID()) {
			$tp->assign("subadmins", SJB_SubAdminManager::getAllSubAdminsInfo());
		}

		$tp->assign("errors", $errors);
		$tp->assign("i18n_domains", $i18n->getDomainsData());
		$tp->assign("i18n_languages", $i18n->getActiveLanguagesData());
		$tp->assign("countries", SJB_CountriesManager::getAllCountriesCodesAndNames());
		$tp->assign('listingEmailTemplates', SJB_EmailTemplateEditor::getEmailTemplatesByGroup(SJB_NotificationGroups::GROUP_ID_LISTING));
		$tp->assign('productEmailTemplates', SJB_EmailTemplateEditor::getEmailTemplatesByGroup(SJB_NotificationGroups::GROUP_ID_PRODUCT));
		$tp->assign('userEmailTemplates', SJB_EmailTemplateEditor::getEmailTemplatesByGroup(SJB_NotificationGroups::GROUP_ID_USER));
		$tp->assign('otherEmailTemplates', SJB_EmailTemplateEditor::getEmailTemplatesByGroup(SJB_NotificationGroups::GROUP_ID_OTHER));
		$tp->display("settings.tpl");
	}
}