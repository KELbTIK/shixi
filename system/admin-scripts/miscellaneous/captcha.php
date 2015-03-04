<?php
class SJB_Admin_Miscellaneous_Captcha extends SJB_Miscellaneous_Captcha
{
	public function execute()
	{
		include '/kcaptcha/index.php';
	}
}
