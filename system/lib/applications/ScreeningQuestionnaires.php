<?php

class SJB_ScreeningQuestionnaires extends SJB_Object
{
	public $sid;
	
	function SJB_ScreeningQuestionnaires($info, $sid = 0)
	{
		$this->db_table_name = 'screening_questionnaires';
		$this->details = new SJB_ScreeningQuestionnairesDetails($info);
	}
	
	public static function getList($user_sid)
	{
		return SJB_DB::query('SELECT * FROM screening_questionnaires WHERE `user_sid` =?n ORDER BY `name`', $user_sid);
	}
	
	public static function getListSIDsAndCaptions($user_sid)
	{
		$list = self::getList($user_sid);
		$result = array();
		foreach ($list as $val) {
			$result[] = array('id' 		=> $val['sid'],
							  'caption'	=> $val['name']);
		}
		return $result;
	}
	
	public static function save($info)
	{
		SJB_ObjectDBManager::saveObject('screening_questionnaires', $info);
		if ($info->sid)
			return true;
	}
	
	public static function getInfoBySID($sid)
	{
		return SJB_ObjectDBManager::getObjectInfo('screening_questionnaires', $sid);
	}
	
	public static function deleteQuestionnaireBySID($sid)
	{
		SJB_ObjectDBManager::deleteObjectInfoFromDB('screening_questionnaires', $sid);
		SJB_ScreeningQuestionnairesFieldManager::deleteQuestionsByParentSID($sid);
		SJB_DB::query("UPDATE `listings` SET `screening_questionnaire`='' WHERE `screening_questionnaire`=?n", $sid);
	}
	
	public static function isUserOwnerQuestionnaire($userSid, $sid)
	{
		return SJB_DB::query('SELECT `sid` FROM `screening_questionnaires` WHERE `sid`=?n AND `user_sid`=?n', $sid, $userSid);
	}
}

class SJB_ScreeningQuestionnairesDetails extends SJB_ObjectDetails
{
	function SJB_ScreeningQuestionnairesDetails($info)
	{
		$details_info = SJB_ScreeningQuestionnairesDetails::getDetails($info);
		foreach ($details_info as $detail_info) {
			if (isset($info[$detail_info['id']])) {
				$detail_info['value'] = $info[$detail_info['id']];
			} else 
				$detail_info['value'] = isset($detail_info['value']) ? $detail_info['value'] : '';
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		return array(
				array
				(
					'id'		=> 'name',
					'caption'	=> 'Questionnaire Name', 
					'type'		=> 'string',
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'passing_score',
					'caption'	=> 'Passing Score', 
					'type'		=> 'list',
					'list_values'	=> array(
							array(
							'id'		=> 'not_acceptable',
							'caption'	=> 'Not acceptable - 0',
							),
							array(
							'id'		=> 'acceptable',
							'caption'	=> 'Acceptable - 1',
							),
							array(
							'id'		=> 'good',
							'caption'	=> 'Good - 2',
							),
							array(
							'id'		=> 'very_good',
							'caption'	=> 'Very Good - 3',
							),
							array(
							'id'		=> 'excellent',
							'caption'	=> 'Excellent - 4',
							),
						),
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'send_auto_reply_more',
					'caption'	=> 'equal or more than passing score', 
					'type'		=> 'boolean',
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'email_text_more',
					'caption'	=> 'Email text to candidates whose score is equal or more than passing score', 
					'type'		=> 'text',
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'send_auto_reply_less',
					'caption'	=> 'less than passing score', 
					'type'		=> 'boolean',
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'email_text_less',
					'caption'	=> 'Email text to candidates whose score is less than passing score', 
					'type'		=> 'text',
					'length'	=> '20',
                    'table_name'=> 'screening_questionnaires',
					'is_required'=> false,
					'is_system'	=> true,
				),
		 );		
	}
}
