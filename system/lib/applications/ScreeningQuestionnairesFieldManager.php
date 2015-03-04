<?php

class SJB_ScreeningQuestionnairesFieldManager extends SJB_ObjectDBManager
{
	var $fields_info;

	public static function saveQuestion($question)
	{
		$result = parent::saveObject('questions', $question);
		if ($question->getOrder())
			return true;
		$questionnaire_sid = $question->getProperty('questionnaire_sid');
		$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM questions WHERE questionnaire_sid  = ?n", $questionnaire_sid->value);
		$max_order = empty($max_order) ? 0 : $max_order;
		SJB_DB::query("UPDATE questions SET questionnaire_sid = ?n, `order` = ?n WHERE sid = ?n",
			$questionnaire_sid->value, ++$max_order, $question->getSID());
		return $result;
	}

	public static function getFieldInfoBySID($field_sid)
	{
		$field_info = parent::getObjectInfo("questions", $field_sid);
		self::setComplexFields($field_info);
		return $field_info;
	}

	public static function setComplexFields(&$field_info)
	{
		if (in_array($field_info['type'], array('list', 'multilist', 'boolean')))
			$field_info['list_values'] = self::getListValuesBySID($field_info['sid']);
	}

	public static function getListValuesBySID($field_sid)
	{
		$fieldListItemManager = new SJB_ScreeningQuestionnairesListItemManager();
		$values = $fieldListItemManager->getHashedListItemsByFieldSIDForApply($field_sid);
		$field_values = array();
		foreach ($values as $value) {
			$field_values[] = array('id' => $value['value'], 'caption' => $value['value'], 'score' => $value['score']);
		}
		return $field_values;
	}

	public static function getFieldsInfoByQuestionnairesSID($sid)
	{
		$questionsSIDs = SJB_DB::query('SELECT * FROM questions WHERE `questionnaire_sid`=?n ORDER BY `order`', $sid);
		$info = array();
		foreach ($questionsSIDs as $question) {
			$info[] = self::getFieldInfoBySID($question['sid']);
		}
		return $info;
	}

	public static function deleteQuestionsByParentSID($sid)
	{
		$fields = SJB_DB::query("SELECT sid FROM questions WHERE questionnaire_sid = ?n", $sid);
		foreach ($fields as $field) {
			self::deleteQuestionBySID($field['sid']);
		}
	}

	public static function deleteQuestionBySID($sid)
	{
		$info = self::getFieldInfoBySID($sid);
		if (!strcasecmp("list", $info['type']) || !strcasecmp("boolean", $info['type']) || !strcasecmp("multilist", $info['type'])) {
			SJB_DB::query("DELETE FROM questions_field_list WHERE field_sid = ?n" . $sid);
		}
		return parent::deleteObjectInfoFromDB("questions", $sid);
	}

	public static function getFieldBySID($field_sid)
	{
		$field_info = SJB_ScreeningQuestionnairesFieldManager::getFieldInfoBySID($field_sid);

		if (empty($field_info))
			return null;
		$field = new SJB_ScreeningQuestionnairesField($field_info);
		$field->setSID($field_sid);
		return $field;
	}

	public static function moveUpFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM questions WHERE  sid = ?n", $field_sid);
		if (empty($field_info))
			return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM questions WHERE questionnaire_sid = ?n AND `order` < ?n",
			$field_info['questionnaire_sid'], $current_order);
		if ($up_order == 0)
			return false;
		SJB_DB::query("UPDATE questions SET `order` = ?n WHERE `order` = ?n AND questionnaire_sid = ?n",
			$current_order, $up_order, $field_info['questionnaire_sid']);
		SJB_DB::query("UPDATE questions SET `order` = ?n WHERE sid = ?n", $up_order, $field_sid);
		return true;
	}

	public static function moveDownFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM questions WHERE sid = ?n", $field_sid);
		if (empty($field_info))
			return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$less_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM questions WHERE questionnaire_sid = ?n AND `order` > ?n",
			$field_info['questionnaire_sid'], $current_order);
		if ($less_order == 0)
			return false;
		SJB_DB::query("UPDATE questions SET `order` = ?n WHERE `order` = ?n AND questionnaire_sid = ?n",
			$current_order, $less_order, $field_info['questionnaire_sid']);
		SJB_DB::query("UPDATE questions SET `order` = ?n WHERE sid = ?n", $less_order, $field_sid);
		return true;
	}
}