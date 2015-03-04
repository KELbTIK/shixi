<?php

class SJB_Questions extends SJB_Object
{
	function SJB_Questions($info = array(), $questionnaire_sid = 0)
	{
		$this->db_table_name = 'questions';
		$this->details = new SJB_QuestionsDetails($info, $questionnaire_sid);
	}
}

class SJB_QuestionsDetails extends SJB_ObjectDetails
{
	var $properties = array();
	var $details;

	function SJB_QuestionsDetails($info, $questionnaire_sid)
	{
		$details_info = SJB_QuestionsDetails::getDetails($questionnaire_sid);
		$sort_array = array();
		$sorted_details_info = array();
		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}

        foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
		    $detail_info['value'] = '';
		    if (isset($info[$detail_info['id']]))
				$detail_info['value'] = $info[$detail_info['id']];
			if ($detail_info['type'] == 'boolean')
				$detail_info['is_required'] = 0;
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($questionnaire_sid)
	{
		return SJB_ScreeningQuestionnairesFieldManager::getFieldsInfoByQuestionnairesSID($questionnaire_sid);
	}
}

