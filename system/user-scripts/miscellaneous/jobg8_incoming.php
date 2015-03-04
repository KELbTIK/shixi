<?php
/***************************************************
 * Integration of JobSource Jobg8 script
 * 
 * This script dispatch event to parse incoming
 * XML document from JobG8
 ***************************************************/
class SJB_Miscellaneous_Jobg8Incoming extends SJB_Function
{
	public function execute()
	{
		ini_set('max_execution_time', 0);
		error_log('jobg8_incoming_error_log.log');
		SJB_System::getTemplateProcessor();
		SJB_Event::dispatch('incomingFromJobG8');
		exit;
	}
}

