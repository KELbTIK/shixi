<?php

class SJB_Applications_EditQuestion extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$sid = SJB_Request::getVar('sid', null, null, 'int');
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$sid = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}
		$request['field_sid'] = $sid;
		$display_list_controller = new SJB_ScreeningQuestionnairesDisplayListController($request);

		$questionInfo = SJB_ScreeningQuestionnairesFieldManager::getFieldInfoBySID($sid);

		if (!empty($questionInfo['questionnaire_sid']) && SJB_ScreeningQuestionnaires::isUserOwnerQuestionnaire(SJB_UserManager::getCurrentUserSID(), $questionInfo['questionnaire_sid'])) {
			$questionInfo = array_merge($questionInfo, $_REQUEST);
			$questionnaire_field = new SJB_ScreeningQuestionnairesField($questionInfo);
			$questionnaire_field->deleteProperty('maxlength');
			$questionnaire_field->deleteProperty('template');
			$add_questionnaire_field_form = new SJB_Form($questionnaire_field);
			$add_questionnaire_field_form->registerTags($tp);
			$form_is_submitted = SJB_Request::getVar('action', '') == 'add';
			$errors = null;
			$type = SJB_Request::getVar('type', false);
			$savedType = $display_list_controller->field->getProperty('type')->value;
			$type = $type ? $type : $savedType;
			$answers = SJB_Request::getVar('answer', false);
			$score = SJB_Request::getVar('score', false);
			$answer_boolean = SJB_Request::getVar('answer_boolean', false);
			$score_boolean = SJB_Request::getVar('score_boolean', false);


			if ($type != 'string') {
				$answers = $answers ? $answers : $display_list_controller->list_items['answer'];
				$score = $score ? $score : $display_list_controller->list_items['score'];
				$answer_boolean = $answer_boolean ? $answer_boolean : $display_list_controller->list_items['answer'];
				$score_boolean = $score_boolean ? $score_boolean : $display_list_controller->list_items['score'];
			}
			if ($answer_boolean && $score_boolean) {
				foreach ($answer_boolean as $key => $val) {
					$score_boolean[strtolower($val)] = $score_boolean[$key];
				}
			}
			if ($form_is_submitted && $add_questionnaire_field_form->isDataValid($errors)) {
				$questionnaire_field->addProperty(
					array('id' => 'questionnaire_sid',
						'type' => 'id',
						'value' => $questionInfo['questionnaire_sid'],
						'is_system' => true)
				);
				$id = $questionnaire_field->getProperty('caption');
				$questionnaire_field->addProperty(
					array('id' => 'id',
						'type' => 'string',
						'value' => md5($id->value),
						'is_system' => true)
				);

				$questionnaire_field->setSID($sid);
				$questionnairesListItemManager = new SJB_ScreeningQuestionnairesListItemManager();
				$questionnairesListItemManager->deleteItemsByFieldSID($sid);
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
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-questions/{$questionInfo['questionnaire_sid']}/?edit=1");

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
				$tp->assign('errors', $errors);
				$tp->assign('action', 'edit');
				$tp->assign('questionnaire_sid', $questionInfo['questionnaire_sid']);
				$add_questionnaire_field_form->registerTags($tp);
				$tp->assign('form_fields', $add_questionnaire_field_form->getFormFieldsInfo());
				$tp->display('add_questions.tpl');
			}
		}
	}
}