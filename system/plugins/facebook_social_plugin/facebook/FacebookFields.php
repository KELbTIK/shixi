<?php

class SJB_FacebookFields
{
	private $oProfile;

	/**
	 * @var SJB_TemplateProcessor
	 */
	private $tp;

	public function  __construct($oProfile)
	{
		$this->oProfile = $oProfile;
		$this->tp = SJB_System::getTemplateProcessor();
	}

	public function __call($name, $arguments)
	{
		$value = '';

		$aParams = explode('_', $name);

		$method = array_shift($aParams);

		if ('get' == $method) {
			$returnType = SJB_Array::get($arguments, 0);
			$fieldType = SJB_Array::get($arguments, 1);
			$listingFieldID = SJB_Array::get($arguments, 2);
			$value = null;

			switch ($returnType) {
				case 'array':
				case 'string':
				case 'int':
					$type = '(' . $returnType . ')';
					break;
				default:
					$type = '(string)';
					break;
			}

			$val = '$this->oProfile->' . implode('->', array_map('strtolower', $aParams));
			$exec = 'return (!empty(' . $val . ')) ? ' . $type . $val . ': \'\';';

			switch ($fieldType) {
				case 'tree':
					$exec = 'return (!empty(' . $val . ')) ? ' . $type . $val . ': \'\';';
					$value = eval($exec);
					if (is_array($value)) {
						if (isset($value[0]))
							$value = $value[0];
					}

					$value = $this->getTreeValues($value, $listingFieldID);
					break;

				case 'array':
					$listingFieldSID = SJB_ListingFieldManager::getListingFieldSIDByID($listingFieldID);
					if (!empty($listingFieldSID)) {
						$exec = 'return (!empty(' . $val . ')) ? ' . $val . ': \'\';';
						$value = eval($exec);
						$value = SJB_ListingFieldManager::getListItemSIDByValue($value, $listingFieldSID);
						$value = eval('return ' . $type . ' $value;');
					}
					break;

				default:
					$value = eval($exec);
					break;
			}
		}

		return $value;
	}

	public function  __get($name)
	{
		return '';
	}

	public static function fillOutListingData_Request(&$request, $aFieldAssoc)
	{
		foreach ($aFieldAssoc as $requestKey => $value)
			$request[$requestKey] = $value;
	}

	public function getTreeValues($sValue, $fieldID)
	{
		$aFieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($fieldID);

		if (!$aFieldInfo)
			return array('tree' => '');

		$aTreeValues = array();
		$tok = strtok($sValue, "\n\r\t");

		while ($tok !== false) {
			array_push($aTreeValues, $tok);
			$tok = strtok("\n\r\t");
		}

		$aTreeValuesSIDs = array();

		foreach ($aTreeValues as $treeCaption) {
			$aTreeItemInfo = SJB_ListingFieldTreeManager::getItemInfoByCaption($aFieldInfo['sid'], $treeCaption);

			if ($aTreeItemInfo['sid'] > 0)
				array_push($aTreeValuesSIDs, $aTreeItemInfo['sid']);
		}

		if (!empty($aTreeValuesSIDs))
			return array('tree' => implode(',', $aTreeValuesSIDs));

		return array('tree' => '');
	}

	/**
	 * @param SJB_Listing $object
	 * @param array $aFieldAssoc
	 * @return void
	 */
	public function fillOutListingData_Object(SJB_Listing &$object, $aFieldAssoc)
	{
		foreach ($aFieldAssoc as $propertyID => $value) {
			// checking if such property exists in listing
			if (!($object->getProperty($propertyID) instanceof SJB_ObjectProperty)) {
				continue;
			}

			if ('tree' == $object->getProperty($propertyID)->getType()) {
				if (!empty($value['tree']))
					$object->setPropertyValue($propertyID, $value['tree']);
			}
			elseif (is_string($value) && strcmp($object->getPropertyValue($propertyID), $value) !== 0) {
				$object->setPropertyValue($propertyID, $value);
			}
			elseif (is_array($value)) {
				foreach ($value as $fieldID => $fieldValue) {
					if ('complex' == $object->getProperty($propertyID)->getType()) {
						if ('date' == $object->getProperty($propertyID)->type->complex->getProperty($fieldID)->getType()) {
							foreach ($fieldValue as &$date)
								$date = !empty($date) ? SJB_I18N::getInstance()->getDate($date) : '';
						}

						$object->getProperty($propertyID)->type->complex->setPropertyValue($fieldID, $fieldValue);

					}
					else {
						$value = $value[0];
						$object->setPropertyValue($propertyID, $value);
					}
				}
			}
		}
	}

	public function get_First_Name()
	{
		return (!empty($this->oProfile->{'fist_name'})) ? $this->oProfile->{'fist_name'} : '';
	}

	public function get_Last_Name()
	{
		return (!empty($this->oProfile->last_name)) ? $this->oProfile->last_name : '';
	}

	public function insertBrs($string)
	{
		$order = array("\r\n", "\n", "\r");
		$replace = '<br />';
		// Processes \r\n's first so they aren't converted twice.
		return str_replace($order, $replace, $string);
	}

	public function get_Summary()
	{
		$value = '';
		if (!empty($this->oProfile->summary)) {
			$this->tp->assign('summary', $this->insertBrs($this->oProfile->summary));
			$value = $this->tp->fetch('../social/summary.tpl');
		}
		return $value;
	}

	public function get_EducationsArr($fields = array())
	{
		$aEducations = array();

		foreach ($fields as $socialField => $sjbField) {
			$aEducations[$sjbField] = array();
		}

		if (!empty($this->oProfile->education)) {
			/**
			 * FIX: work experience array begins from index #1
			 * @see SJB_ComplexType::getPropertyVariablesToAssign()
			 */
			$fieldIndex = 1;

			foreach ($this->oProfile->education as $education) {

				foreach ($fields as $socialField => $sjbField) {
					$newValues = '';
					switch ($socialField) {
						case 'year':
							$newValues = ((!empty($education[$socialField])) ? SJB_I18N::getInstance()->getDate($education[$socialField]['name'] . '-09') : '');
							break;
						case 'concentration':
							if (!empty($education[$socialField])) {
								$aConcentrations = array();
								foreach ($education[$socialField] as $concentration)
									array_push($aConcentrations, $concentration['name']);
								$newValues = implode(',', $aConcentrations);
							}
							else {
								$newValues = '';
							}
							break;
						default:
							$newValues = ((!empty($education[$socialField])) ? (string)$education[$socialField]['name'] : '');
							break;
					}

					$aEducations[$sjbField][$fieldIndex] = $newValues;
				}

				$fieldIndex++;
			}
		}

		return $aEducations;
	}

    /**
     * retrieves profile education display part for sjb textarea field
     * uses template "/social/educations.tpl" for appearance
     * @return bool|mixed|string
     */
    public function get_Educations()
	{
		$value = '';

		if (!empty($this->oProfile->education)) {
            $value = $this->retrieveProfileEducationValue();
		}

		return $value;
	}

    /**
     * @return bool|mixed|string
     */
    protected function retrieveProfileEducationValue()
    {
        $aEducation = array();
        $value      = '';

        foreach ($this->oProfile->education as $education) {
            array_push($aEducation, $this->getEducationFieldsInfo($education));
        }

        if (!empty($aEducation))
            $value = $this->getEducationDisplayPart($aEducation);

        return $value;
    }

    /**
     * @param $aEducation
     * @return bool|mixed|string
     */
    protected function getEducationDisplayPart($aEducation)
    {
        $this->tp->assign('educations', $aEducation);
        $value = $this->tp->fetch('../social/educations.tpl');
        return $value;
    }

    /**
     * @param $education
     * @return array
     */
    protected function getEducationFieldsInfo($education)
    {
        return array(
            'school' => SJB_Array::getPath($education, 'school/name'),
            'year' => SJB_Array::getPath($education, 'year/name'),
            'concentration' => SJB_Array::get($education, 'concentration'),
            'type' => SJB_Array::get($education, 'type'),
            'classes' => SJB_Array::get($education, 'classes'),
        );
    }

    /**
	 * get werbal Month ( January, February ) by month number
	 *
	 * @param mixed $month
	 * @return string
	 */
	private function getMonth($month, $type = 'F')
	{
		$month = (int)$month;
		return date($type, mktime(null, null, null, $month));
	}

    /**
     * retrieves profile education display part for sjb textarea field
     * uses template "/social/experience.tpl" for appearance
     * @return bool|mixed|string
     */
    public function get_Positions()
	{
		$value = '';

		if (!empty($this->oProfile->work)) {
            $value = $this->retrieveProfileWorkValue();
		}

		return $value;
	}

    /**
     * @return bool|mixed|string
     */
    protected function retrieveProfileWorkValue()
    {
        $aPositions = array();
        $value      = '';

        foreach ($this->oProfile->work as $work) {
            array_push($aPositions, $this->getWorkFieldsInfo($work));
        }

        if (!empty($aPositions))
            $value = $this->getWorkDisplayPart($aPositions);

        return $value;
    }

    /**
     * @param array $aPositions
     * @return bool|mixed|string
     */
    protected function getWorkDisplayPart($aPositions)
    {
        $this->tp->assign('positions', $aPositions);
        $value = $this->tp->fetch('../social/experience.tpl');
        return $value;
    }

    protected function getWorkFieldsInfo($position)
    {
        return array(
            'employer'      => SJB_Array::getPath($position, 'employer/name'),
            'position'      => SJB_Array::getPath($position, 'position/name'),
            'location'      => SJB_Array::getPath($position, 'location/name'),
            'start_date'    => SJB_Array::get($position, 'start_date'),
            'end_date'      => SJB_Array::get($position, 'end_date'),
            'description'   => SJB_Array::get($position, 'description'),
        );
    }

	public function get_PositionsArr($fields = array())
	{
		$aWorkExperience = array();

		foreach ($fields as $socialField => $sjbField)
			$aWorkExperience[$sjbField] = array();

		if (!empty($this->oProfile->work)) {
			/**
			 * FIX: work experience array begins from index #1
			 * @see SJB_ComplexType::getPropertyVariablesToAssign()
			 */
			$fieldIndex = 1;

			foreach ($this->oProfile->work as $position) {
				foreach ($fields as $socialField => $sjbField) {
					$newValues = '';
					switch ($socialField) {
						case 'start_date':
						case 'end_date':
							$date = ((!empty($position[$socialField])) ? $position[$socialField] : '');
							if ($date) {
								$newValues = SJB_I18N::getInstance()->getDate($date);
							}
							break;
						case 'description':
							$newValues = ((!empty($position[$socialField])) ? (string)$position[$socialField] : '');
							break;
						default:
							$newValues = ((!empty($position[$socialField])) ? (string)$position[$socialField]['name'] : '');
							break;
					}

					$aWorkExperience[$sjbField][$fieldIndex] = $newValues;
				}

				$fieldIndex++;
			}
		}

		return $aWorkExperience;
	}

}
