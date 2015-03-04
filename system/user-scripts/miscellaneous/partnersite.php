<?php
class SJB_Miscellaneous_Partnersite extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action');
		$api = SJB_Request::getVar('api', false);
		$request = $_REQUEST;
		unset($request['action']);
		switch ($action) {
			case 'header':
				$test = $tp->fetch("header.tpl");
				echo $test;
				exit();
				break;
			case 'simplyHired':
				SJB_Statistics::addStatistics('partneringSites');
				break;
			default:
				$isIPhone = false;
				if (class_exists('MobilePlugin')) {
					$isIPhone = MobilePlugin::isPhone();
				}
				$url = SJB_Request::getVar('url');
				$url = $url ? base64_decode($url) : '';
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) === SJB_Settings::getValue('mobile_url')
					|| (SJB_Settings::getValue('detect_iphone') && $isIPhone)
				) {
					$url = str_replace('viewjob', 'm/viewjob', $url);
				}
				SJB_Statistics::addStatistics('partneringSites');
				if ($api && $api == 'indeed') {
					SJB_HelperFunctions::redirect($url);
				}

				$tp->assign('url', $url);
				$tp->display("partnersite.tpl");
				break;
		}
	}
}

