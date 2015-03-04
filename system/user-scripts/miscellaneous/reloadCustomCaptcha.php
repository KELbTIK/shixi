<?php
class SJB_Miscellaneous_ReloadCustomCaptcha extends SJB_Function
{
	public function execute()
	{
		echo SJB_Array::get(CaptchaPlugin::getCaptchaProperties(array()), 'captchaView');
	}
}
