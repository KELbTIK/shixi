<?php

class SJB_WrappedFunctions
{

	public static function file_exists($filename)
	{
		return file_exists($filename);
	}

	public static function unlink($filename)
	{
		return unlink($filename);
	}

	public static function redirect($url)
	{
		SJB_HelperFunctions::redirect($url);
	}
	
	public static function ini_set($varname, $newvalue)
	{
		return ini_set($varname, $newvalue);
	}
	
	public static function session_start()
	{
		session_start();
	}
	
	public static function is_uploaded_file($filename)
	{
		return is_uploaded_file($filename);
	}
	
	public static function move_uploaded_file($filename, $destination)
	{
		return move_uploaded_file($filename, $destination);
	}
	
	public static function header($header)
	{
		header($header);
	}

	public static function basename($path)
	{
		return basename($path);
	}

	public static function readfile($filename)
	{
		return readfile($filename);
	}
}
