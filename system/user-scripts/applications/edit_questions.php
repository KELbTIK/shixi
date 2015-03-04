<?php

class SJB_Applications_EditQuestions extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$sid = SJB_Request::getVar('sid', null, null, 'int');
		$edit = SJB_Request::getVar('edit', false);

		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$sid = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
			$questionSID = isset($passed_parameters_via_uri[1]) ? $passed_parameters_via_uri[1] : false;
		}
		if (SJB_ScreeningQuestionnaires::isUserOwnerQuestionnaire(SJB_UserManager::getCurrentUserSID(), $sid)) {
			switch ($action)
			{
				case 'delete':
					if ($questionSID)
						SJB_ScreeningQuestionnairesFieldManager::deleteQuestionBySID($questionSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-questions/{$sid}/");
					break;
				case 'move_up':
					$questionSID = SJB_Request::getVar('question', false);
					if ($questionSID)
						SJB_ScreeningQuestionnairesFieldManager::moveUpFieldBySID($questionSID);
					break;
				case 'move_down':
					$questionSID = SJB_Request::getVar('question', false);
					if ($questionSID)
						SJB_ScreeningQuestionnairesFieldManager::moveDownFieldBySID($questionSID);
					break;
			}

			$questionnaire_field = new SJB_ScreeningQuestionnairesField($_REQUEST);
			$questions = array();
			if ($sid) {
				$questions = SJB_ScreeningQuestionnairesFieldManager::getFieldsInfoByQuestionnairesSID($sid);
				$tp->assign('questionnarieInfo', SJB_ScreeningQuestionnaires::getInfoBySID($sid));
			}

			$add_questionnaire_field_form = new SJB_Form($questionnaire_field);

			$add_questionnaire_field_form->registerTags($tp);
			$form_is_submitted = SJB_Request::getVar('action', '') == 'add';
			$errors = null;
			$answers = SJB_Request::getVar('answer', false);
			$score = SJB_Request::getVar('score', false);
			$answer_boolean = SJB_Request::getVar('answer_boolean', false);
			$score_boolean = SJB_Request::getVar('score_boolean', false);
			$type = SJB_Request::getVar('type', false);

			if (is_array($answers)) {
				foreach ($answers as $key => $val) {
					if ($key != 0 && $val == null) {
						unset($answers[$key]);
					}
				}
			}

			if ($form_is_submitted && $add_questionnaire_field_form->isDataValid($errors)) {

				$questionnaire_field->addProperty(
					array('id' => 'questionnaire_sid',
						'type' => 'id',
						'value' => $sid,
						'is_system' => true)
				);
				$id = $questionnaire_field->getProperty('caption');
				$questionnaire_field->addProperty(
					array('id' => 'id',
						'type' => 'string',
						'value' => md5($id->value),
						'is_system' => true)
				);

				SJB_ScreeningQuestionnairesFieldManager::saveQuestion($questionnaire_field);
				if ($type == 'boolean') {
					$request['list_multiItem_value'] = $answer_boolean;
					$request['field_sid'] = $questionnaire_field->sid;
					$request['score'] = $score_boolean;
					$edit_list_controller = new SJB_ScreeningQuestionnairesListController($request);
					if ($edit_list_controller->isvalidFieldSID()) {
						$edit_list_controller->saveItem(true);
					}
				}
				elseif ($type != 'string') {
					$request['list_multiItem_value'] = $answers;
					$request['field_sid'] = $questionnaire_field->sid;
					$request['score'] = $score;
					$edit_list_controller = new SJB_ScreeningQuestionnairesListController($request);
					if ($edit_list_controller->isvalidFieldSID()) {
						$edit_list_controller->saveItem(true);
					}
				}
				$questionnaire_sid = $questionnaire_field->getSID();
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-questions/{$sid}/");

			} else {
				switch ($type) {
					case 'boolean':
						$tp->assign('answer_boolean', $answer_boolean);
						$tp->assign('score_boolean', $score_boolean);
						break;
					case 'multilist':
					case 'list':
						$tp->assign('answers', $answers);
						$tp->assign('score', $score);
						break;
				}
				$tp->assign('edit', $edit);
				$tp->assign('sid', $sid);
				$tp->assign('questions', $questions);
				$tp->assign('action', 'edit');
				$tp->assign('errors', $errors);
				$add_questionnaire_field_form->registerTags($tp);
				$tp->assign('form_fields', $add_questionnaire_field_form->getFormFieldsInfo());
				$tp->display('edit_questions.tpl');
			}
		}
	}
}