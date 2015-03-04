<?php

class SJB_Admin_TemplateManager_UploadLogo extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$theme = SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default');
		$tp->assign('theme', $theme);
		$errors = array();
		$message = '';
		$alternativeText = SJB_Request::getVar('logoAlternativeText', '');

		switch (SJB_Request::getVar('action', '')) {

			case 'save':
				if (isset($_FILES['logo']['error'])) {
					if ($_FILES['logo']['error'] == UPLOAD_ERR_OK) {
						if (SJB_System::getSystemSettings('isDemo')) {
							$errors[] = 'NOT_ALLOWED_IN_DEMO';
						} else {
							$themePath = SJB_TemplatePathManager::getAbsoluteThemePath($theme);
							if (move_uploaded_file($_FILES['logo']['tmp_name'], "{$themePath}main/images/logo.png")) {
								$message = 'Logo has been uploaded successfully';
							}
						}
					} else {
						switch ($_FILES['logo']['error']) {
							case UPLOAD_ERR_INI_SIZE:
								$errors[] = 'File size exceeds system limit. Please check the file size limits on your hosting or upload another file.';
								break;
							case UPLOAD_ERR_FORM_SIZE:
								$errors[] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
								break;
							case UPLOAD_ERR_PARTIAL:
								$errors[] = 'The uploaded file was only partially uploaded';
								break;
							case UPLOAD_ERR_NO_FILE:
								// Разрешим изменять текст без аплоада лого
								break;
							case UPLOAD_ERR_NO_TMP_DIR:
								$errors[] = 'Missing a temporary folder';
								break;
							case UPLOAD_ERR_CANT_WRITE:
								$errors[] = 'Failed to write file to disk';
								break;
							default:
								$errors[] = 'File upload error';
						}
						if ($alternativeText == SJB_Settings::getSettingByName('logoAlternativeText')) {
							$errors[] = 'Upload a logo or enter alternative text';
						}
					}
				}

				if (SJB_Settings::getSettingByName('logoAlternativeText') === false) {
					SJB_Settings::addSetting('logoAlternativeText', $alternativeText);
				} else {
					if ($alternativeText != SJB_Settings::getSettingByName('logoAlternativeText')) {
						SJB_Settings::updateSetting('logoAlternativeText', $alternativeText);
						if (!$message) {
							$message = 'Alternative text has been updated successfully';
						} else {
							$message = 'Logo and Alternative text has been uploaded successfully';
						}
					}
				}

				break;
		}

		$tp->assign('errors', $errors);
		$tp->assign('message', $message);
		$tp->assign('uploadMaxFilesize', SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->assign('logoAlternativeText', SJB_Request::getVar('logoAlternativeText', SJB_Settings::getSettingByName('logoAlternativeText')));
		$tp->display('upload_logo.tpl');
	}
}