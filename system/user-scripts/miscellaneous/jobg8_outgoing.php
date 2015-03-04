<?php
/***************************************************
 * Integration of JobSource Jobg8 script
 * 
 * This script dispatch event to generate outgoing
 * XML document for JobG8
 ***************************************************/

class SJB_Miscellaneous_Jobg8Outgoing extends SJB_Function
{
	public function execute()
	{
		ini_set('max_execution_time', 0);
		error_log('jobg8_outgoing_error_log.log');
		SJB_System::getTemplateProcessor();
		SJB_Event::dispatch('sendJobsToJobG8');
		exit;
	}
}