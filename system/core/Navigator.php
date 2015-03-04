<?php

class SJB_Navigator
{
	public static function getURI()
	{
		$site_url = parse_url(SJB_System::getSystemSettings('SITE_URL'));
		$request_uri = parse_url($_SERVER['REQUEST_URI']);

		if (isset($site_url['path'])) {
			$return = substr($request_uri['path'], strlen($site_url['path']));
			return ($return) ? $return : '/';
		}

		return $request_uri['path'];
	}
	
	public static function getURIThis()
	{
		return $_SERVER['REQUEST_URI'];
	}

	public static function isRequestedUnderLegalURI()
	{
		$site_url = parse_url(SJB_System::getSystemSettings('SITE_URL'));
		$request_uri = parse_url($_SERVER['REQUEST_URI']);
		$isUnderOurHost = $site_url['host'] === $_SERVER['HTTP_HOST'];
		$isInOurPath = isset($site_url['path']) ? strpos($request_uri['path'], $site_url['path']) === 0 : true;
		return $isUnderOurHost && $isInOurPath;
	}
}