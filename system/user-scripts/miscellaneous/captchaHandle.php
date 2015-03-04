<?php

class SJB_Miscellaneous_CaptchaHandle extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		SJB_Captcha::getInstance($tp)->display();
	}
}
