<?php
/**
 * This file contains compatibility global functions aliases, to prevent some
 * PHP system errors.
 */

if (!function_exists('strptime')) {
// strptime() function for windows systems. Get from http://forum.academ.org/index.php?showtopic=187558
	function strptime ( $strdate, $format ) {
		$plop = array( 's'=>'tm_sec', 'i'=>'tm_min', 'H'=>'tm_hour',
		'd'=>'tm_mday', 'm'=>'tm_mon', 'Y'=>'tm_year');

		$regexp = preg_quote($format, '/');
		$regexp = str_replace(
			array('%d','%m','%Y','%H','%i','%s'),
			array('(\d{2})','(\d{2})','(\d{4})','(\d{2})','(\d{2})','(\d{2})'),
			$regexp);
		if (preg_match('/^' . $regexp.'$/', $strdate,$m)) {
			$result = array('tm_sec'=>0,'tm_min'=>0,'tm_hour'=>0,'tm_mday'=>0,'tm_mon'=>0,'tm_year'=>0,'tm_wday'=>0,'tm_yday'=>0,'unparsed'=>'');
			preg_match_all('/%(\w)/',$format,$patt);
			foreach($patt[1] as $k=>$v) {
				if(!isset($plop[$v])) {
					continue;
				}
				$result[$plop[$v]] = intval($m[$k+1]);
				if($plop[$v] == 'tm_mon') {
					$result[$plop[$v]] -= 1;
				}
			}
			$result['tm_year'] -= 1900;
			return $result;
		}
		else {
			return false;
		}
	}
}

if (!isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['QUERY_STRING'] = '';
}
