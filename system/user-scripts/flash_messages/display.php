<?php

class SJB_FlashMessages_Display extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('messagesArray', SJB_FlashMessages::getInstance()->getContentAndRemove());
		$tp->display('flash_errors.tpl');
	}
}
