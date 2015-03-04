<?php

class SJB_ScreeningQuestionnairesField extends SJB_Object
{
	var $questionnaire_sid;
	var $field_type;
	var $order;
	
	function SJB_ScreeningQuestionnairesField($questionnaire_field_info, $questionnaire_sid = 0)
	{
		$this->db_table_name = 'questions';
		$this->details = new SJB_ScreeningQuestionnairesFieldDetails($questionnaire_field_info);
		$this->setQuestionnaireSID($questionnaire_sid);
		$this->field_type = isset($questionnaire_field_info['type']) ? $questionnaire_field_info['type'] : null;
		$this->order = isset($questionnaire_field_info['order']) ? $questionnaire_field_info['order'] : null;
	}
	
	function setQuestionnaireSID($questionnaire_sid)
	{
		$this->questionnaire_sid = $questionnaire_sid;
	}
	
	function getOrder()
	{
		return $this->order;
	}
	
	function getQuestionnaireTypeSID()
	{
		return $this->questionnaire_type_sid;
	}
	
	function getFieldType()
	{
		return $this->field_type;
	}
}

class SJB_ScreeningQuestionnairesFieldDetails extends SJB_ObjectDetails
{
	
	function SJB_ScreeningQuestionnairesFieldDetails($questionnaire_field_info)
	{
		$details_info = SJB_ScreeningQuestionnairesFieldDetails::getDetails($questionnaire_field_info);
		foreach ($details_info as $detail_info) {
			if (isset($questionnaire_field_info[$detail_info['id']]))
				$detail_info['value'] = $questionnaire_field_info[$detail_info['id']];
			else 
				$detail_info['value'] = isset($detail_info['value']) ? $detail_info['value'] : '';
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($questionnaire_field_info)
	{
		
		$common_details_info = array(
				array (
					'id'		=> 'caption',
					'caption'	=> 'Question', 
					'type'		=> 'string',
					'length'	=> '20',
                    'table_name'=> 'questions',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array (
					'id'		=> 'is_required',
					'caption'	=> 'Required', 
					'type'		=> 'boolean',
					'length'	=> '20',
                    'table_name'=> 'questions',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array (
					'id'		=> 'type',
					'caption'	=> 'Answer Type',
					'type'		=> 'list',
					'list_values' => array(
											array(
												'id' => 'string',
												'caption' => 'Text',
											),
											array(
												'id' => 'boolean',
												'caption' => 'Yes/No',
											),
											array(
												'id' => 'multilist',
												'caption' => 'List of answers with multiple choice',
											),
											array(
												'id' => 'list',
												'caption' => 'List of answers with single choice',
											),											
										),
					'length'	=> '',
					'is_required'=> true,
					'is_system' => true,
				),

			   );		
			   
		$field_type = isset($questionnaire_field_info['type']) ? $questionnaire_field_info['type'] : null;
		$extra_details_info = SJB_ScreeningQuestionnairesFieldDetails::getDetailsByFieldType($field_type);
		return $details_info = array_merge($common_details_info, $extra_details_info);
		
	}

	public static function getDetailsByFieldType($field_type)
	{
		return SJB_TypesManager::getExtraDetailsByFieldType($field_type);
	}
}