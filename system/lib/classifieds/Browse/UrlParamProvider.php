<?php

class SJB_UrlParamProvider
{
	public static function getParams()
	{
		if (!isset($_REQUEST['passed_parameters_via_uri']))
			return Array();
		$uri_part = $_REQUEST['passed_parameters_via_uri'];
		$uri_part = preg_replace("/\/*\?.*$/u", "", $uri_part);
		$uri_part = preg_replace("/\/*$/u", "", $uri_part);
		$uri_part = preg_replace("/^\/+/u", "", $uri_part);
		$uri_part = preg_replace("/\/+/u", "/", $uri_part);
		$parts = array_map('urldecode', explode("/", $uri_part));
		return $parts[0] ? $parts : array();
	}
}
