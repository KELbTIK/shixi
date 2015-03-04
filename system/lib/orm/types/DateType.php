<?php

class SJB_DateType extends SJB_Type
{
    private $convertToDBDate = false;

    public function setConvertToDBDate($convert = false)
    {
        $this->convertToDBDate = $convert;
    }

    public function getConvertToDBDate()
    {
        return $this->convertToDBDate;
    }

	function SJB_DateType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->sql_type 		= 'DATE';
		$this->default_template = 'date.tpl';
	}

    function getPropertyVariablesToAssign()
    {
        $values = parent::getPropertyVariablesToAssign();
        if ($this->convertToDBDate && !is_array($this->property_info['value'])) {
            $values['mysql_date'] = SJB_I18N::getInstance()->getInput('date', $this->property_info['value']);
        }
        return $values;
    }

	function isValid()
	{
		$this->setConvertToDBDate(true);
		$value = $this->property_info['value'];
		if (!SJB_I18N::getInstance()->isValidDate($value)) {
			if (!$this->getComplexParent()) {
				$this->property_info['value'] = '';
			}
			return 'WRONG_DATE_FORMAT';
		}
		return true;		
	}

	function getSQLValue($context = null)
	{
		if (empty($this->property_info['value'])) {
			return null;
		}
		$time = '';
		if (strtolower(get_class($context)) === 'sjb_moreequalcriterion') {
			$time = "00:00:00";
		} else {
			if(strtolower(get_class($context)) === 'sjb_lessequalcriterion') {
				$time = "23:59:59";
			}
		}
		$date = SJB_I18N::getInstance()->getInput('date', $this->property_info['value']);
		$this->setConvertToDBDate(true);
		return "{$date} {$time}";
	}

    function getKeywordValue()
    {
		return SJB_I18N::getInstance()->getInput('date', $this->property_info['value']);
	}

	function getSQLFieldType()
	{
		return "DATETIME NULL";
	}
}