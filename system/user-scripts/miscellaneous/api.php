<?php

class SJB_Miscellaneous_Api extends SJB_Function
{
	public function execute()
	{
		/*******************************************************************************
		 * Special dispatch file for API Plugin
		 *
		 * This file catch requests, and start associated method of ApiPlugin
		 ******************************************************************************/

		$tp = SJB_System::getTemplateProcessor();

		SJB_Event::dispatch('incomingApiCommand', $_REQUEST);
	}
}

