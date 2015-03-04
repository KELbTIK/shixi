<?php

require_once('I18N/IntFormatter.php');
require_once('I18N/FloatFormatter.php');
require_once('I18N/DateFormatter.php');
require_once('I18N/NullFormatter.php');

class I18NFormatterFactory
{
	var $context;
	var $formatters;
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	
	function doesFormatterExist($type)
	{
		if (is_null($this->formatters)) {
			$this->createFormatters();
		}
		return isset($this->formatters[$type]);
	}

	function getIntFormatter()
	{
		return $this->getFormatter('integer');
	}
	
	function getFloatFormatter()
	{
		return $this->getFormatter('float');
	}
	
	/**
	 * @return SJB_DateFormatter
	 */
	function getDateFormatter()
	{
		return $this->getFormatter('date');
	}
	
	function getNullFormatter()
	{
		if (!isset($this->nullFormatter)) {
			$this->nullFormatter = new SJB_NullFormatter();
		}
		return $this->nullFormatter;
	}
	
	function getFormatter($type)
	{
		if (is_null($this->formatters)) {
			$this->createFormatters();
		}
		return $this->formatters[$type];
	}
	
	function createFormatters()
	{
		$this->formatters['integer'] = $this->createIntFormatter();
		$this->formatters['float'] = $this->createFloatFormatter();
		$this->formatters['date'] = $this->createDateFormatter();
	}	
	
	function createIntFormatter()
	{
		$thousands_separator = $this->context->getThousandsSeparator();
		if (is_null($thousands_separator)) {
			SJB_Logger::error('THOUSANDS SEPARATOR NOT SET');
			$formatter = $this->getNullFormatter();
		}
		else {
			$formatter = new SJB_IntFormatter();
			$formatter->setThousandsSeparator($thousands_separator);
		}
		return $formatter;
	}

	
	function createFloatFormatter()
	{
		$thousands_separator = $this->context->getThousandsSeparator();
		$decimals = $this->context->getDecimals();
		$decimal_point = $this->context->getDecimalPoint();
		
		$errors = array();
		
		if (is_null($thousands_separator)) {
			$errors[] = 'THOUSANDS SEPAPARATOR IS NULL';
		}
		
		if (is_null($decimals)) {
			$errors[] = 'DECIMALS IS NULL';
		}
		else {
			if (!is_numeric($decimals) || !is_int((int)$decimals)) {
				$errors[] = 'DECIMALS IS NOT NUMERIC';
			}
		}
		
		if (empty($decimal_point)) {
			$decimals = 0;
		}
		
		if (count($errors) == 0) {
			$formatter = new SJB_FloatFormatter();
			$formatter->setThousandsSeparator($thousands_separator);
			$formatter->setDecimals($decimals);
			$formatter->setDecimalPoint($decimal_point);
		} 
		else {
			$formatter = $this->getNullFormatter();
			foreach($errors as $error) {
				SJB_Logger::error($error);
			}
		}
		
		return $formatter;
	}
	
	function createDateFormatter()
	{
		$date_format = $this->context->getDateFormat();
		if (empty($date_format)) {
			SJB_Logger::error('FORMAT IS EMPTY');
			$formatter = $this->getNullFormatter();
		}
		else {
			$formatter = new SJB_DateFormatter();
			$formatter->setDateFormat($date_format);
		}
		return $formatter;
	}
}
