<?php

class SJB_Admin_TemplateManager_DeleteUploadedFile extends SJB_Function
{
	public function execute()
	{
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$etSID 		= SJB_Array::get($passed_parameters_via_uri, 0);
		}

		$field_id = SJB_Request::getVar('field_id', null);
		$etInfo = SJB_EmailTemplateEditor::getEmailTemplateInfoBySID($etSID);

		if (is_null($etSID) || is_null($field_id)) {
			$errors['PARAMETERS_MISSED'] = 1;
		}
		elseif (is_null($etInfo) || !isset($etInfo[$field_id])) {
			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
		}
		else {
			$uploaded_file_id = $etInfo[$field_id];
			SJB_UploadFileManager::deleteUploadedFileByID($uploaded_file_id);
			$etInfo[$field_id] = '';
			$emailTemplate = new SJB_EmailTemplate($etInfo);
			$emailTemplate->setSID($etSID);
			SJB_EmailTemplateEditor::saveEmailTemplate($emailTemplate);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-email-templates/'
					. $emailTemplate->getPropertyValue('group') . '/' . $etSID);
		}

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('errors', isset($errors) ? $errors : null);
		$tp->display('delete_uploaded_file.tpl');
	}
}
