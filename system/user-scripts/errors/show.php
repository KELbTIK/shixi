<?php

class SJB_Errors_Show extends SJB_Function
{
	public function execute()
	{
		$errorInstance = SJB_Request::getVar('errors');

		$tp = SJB_System::getTemplateProcessor();

		if ($errorInstance instanceof SJB_Error && $errorInstance->counter != 0) {

			$errors = $errorInstance->getErrors();

			$tp->assign('errors', $errors);

			$tp->display('errors.tpl');
		}
	}
}