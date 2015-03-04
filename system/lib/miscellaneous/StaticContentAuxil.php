<?php

class SJB_StaticContentAuxil
{
	public static function isValidNameID($name, $id)
	{
		if (!isset($name) || $name == '') {
			return 'Please enter Static Content Name.';
		}
		if (!isset($id) || $id == '') {
			return 'Please enter Static Content ID.';
		}
		if (!self::isValidID($id)) {
			return 'The ID you have entered is invalid. Please try another ID.';
		}
		return '';
	}
	
	public static function isValidID ($id)
	{
		return preg_match("(^\w+$)", $id);
	}
	
	public static function warning($error_code, $error_message)
	{
		echo "<p class='error'>{$error_message}</p>";
	}
}
