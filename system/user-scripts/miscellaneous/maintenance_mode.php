<?php

class SJB_Miscellaneous_MaintenanceMode extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->display("maintenance_mode.tpl");
	}
}
