<?php

class SJB_ImportFile
{
	var $file_info 	= null;
	var $data		= null;

	function SJB_ImportFile($file_info)
	{
		$this->file_info = $file_info;
	}

	function getData()
	{
		return $this->data;
	}

	public static function isValidFileExtensionByFormat($fileType, $file)
	{
		$pathInfo = (isset($file['name'])) ? pathinfo($file['name']) : pathinfo($file);
		$fileExtension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
		$fileType = strtolower($fileType);
		switch ($fileExtension) {
			case 'csv':
				return in_array($fileType, array('csv'));
			case 'xls':
			case 'xlsx':
				return in_array($fileType, array('excel', 'xls', 'xlsx'));
		}
		return false;
	}

	/**
	 * After converting from incorrectly selected encoding to UTF-8 all correct symbols disappear.
	 * So if we check for correct alpha-numeric and whitespace characters using preg_match -
	 * we assume the string is in correct encoding for our purpose.
	 * @param $fileInfo
	 * @param $encoding
	 * @return bool
	 */
	public static function isValidFileCharset($fileInfo, $encoding)
	{
		$fileSize  = filesize($fileInfo['tmp_name']);
		$partSize  = 18874368; // 18 MB
		$partCount = ceil($fileSize / $partSize);
		for ($i = 0; $i < $partCount; $i++) {
			$readOffset      = $i * $partSize;
			$filePartContent = file_get_contents($fileInfo['tmp_name'], NULL, NULL, $readOffset, $partSize);
			$encodedContent  = iconv($encoding, "UTF-8//IGNORE", $filePartContent);
			if (!preg_match('/[^0-9\f\n\r\t\v\;\_\-\.a-z\/\(\) ]/im', $encodedContent)) {
				return false;
			}
			unset($filePartContent);
			unset($encodedContent);
		}
		return true;
	}
}
