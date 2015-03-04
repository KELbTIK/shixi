<?php

class SJB_Applications_AddQuestionnaire extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('use_screening_questionnaires');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'add');
		$submit = SJB_Request::getVar('submit', false);
		$template = SJB_Request::getVar('template_name', 'add_questionnaire.tpl');
		$sid = SJB_Request::getVar('sid', null, null, 'int');
		$edit = SJB_Request::getVar('edit', false);
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$sid = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}
		$errors = array();
		$questionnaireInfo = SJB_ScreeningQuestionnaires::getInfoBySID($sid);
		if (!empty($questionnaireInfo['sid']) && !SJB_ScreeningQuestionnaires::isUserOwnerQuestionnaire(SJB_UserManager::getCurrentUserSID(), $questionnaireInfo['sid'])) {
			SJB_FlashMessages::getInstance()->addError('NOT_OWNER');
		}
		else if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires')) {
			$questionnaireInfo = $questionnaireInfo ? $questionnaireInfo : array();
			$questionnaireInfo = array_merge($questionnaireInfo, $_REQUEST);
			$questionnaire = new SJB_ScreeningQuestionnaires($questionnaireInfo);
			if ($submit) {
				$questionnaire->addProperty(
					array('id' => 'user_sid',
						'type' => 'id',
						'value' => SJB_UserManager::getCurrentUserSID(),
						'is_system' => true)
				);
			}
			if (isset($sid) && !is_null($sid)) {
				$questionnaire->setSID($sid);
			}
			$addForm = new SJB_Form($questionnaire);
			$addForm->registerTags($tp);
			switch ($submit) {
				case 'add':
					if ($addForm->isDataValid($errors)) {
						SJB_ScreeningQuestionnaires::save($questionnaire);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/screening-questionnaires/add-questions/{$questionnaire->sid}/");
					}
					else {
						$action = 'add';
						$questionnaire->deleteProperty('user_sid');
						$addForm = new SJB_Form($questionnaire);
						$addForm->registerTags($tp);
					}
					break;
				case 'edit':
					if ($addForm->isDataValid($errors)) {
						SJB_ScreeningQuestionnaires::save($questionnaire);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/screening-questionnaires/edit/{$questionnaire->sid}/?edit=1");
					}
					else {
						$tp->assign('sid', $_REQUEST['sid']);
						$questionnaire->deleteProperty('user_sid');
						$addForm = new SJB_Form($questionnaire);
						$addForm->registerTags($tp);
						$action = 'edit';
					}
					break;
			}
			$form_fields = $addForm->getFormFieldsInfo();
			$tp->assign('form_fields', $form_fields);
			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign
			(
				'METADATA',
				array
				(
					'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
				)
			);
			$tp->assign('edit', $edit);
			$tp->assign('request', $questionnaireInfo);
			$tp->assign('sid', $sid);
			$tp->assign('action', $action);
			$tp->assign('errors', $errors);
			$tp->display($template);
		}
	}
}
