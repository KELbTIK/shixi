<?php

class SJB_Miscellaneous_BroadbeanIncoming extends SJB_Function
{
	public function execute()
	{
		//***************************************************
		//  Integration of Bulk Upload script (incoming data from Broadbean to SJB)
		//***************************************************

		/***************************************************
		 * Integration of Broadbean
		 *
		 * This script dispatch event to parse incoming
		 * XML document from Broadbean
		 ***************************************************/
		ini_set('max_execution_time', 0);
		SJB_System::getTemplateProcessor();
		SJB_Event::dispatch('incomingFromBroadbean');
		exit;
	}
}
