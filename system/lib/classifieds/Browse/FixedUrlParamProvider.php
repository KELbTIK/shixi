<?php

class SJB_FixedUrlParamProvider
{
	public static function getParams(array $parameters)
	{
		if (!isset($parameters['passed_parameters_via_uri'])) {
			return array();
		}
		$splitedParts = explode("/", $parameters['passed_parameters_via_uri']);
		$parts = array();
		foreach ($splitedParts as $part) {
			if (!in_array($part, array("", "/"))) {
				$parts[] = urldecode($part);
			}
		}
		return $parts;
	}
}
