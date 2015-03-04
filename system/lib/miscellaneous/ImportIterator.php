<?php

class SJB_ImportIterator implements Iterator 
{
	private $position = 1;
	private $fileResource = false; 
	private $csvDelimiter = ','; 
	private $readEncoding = 'UTF-8';

	public function __construct() 
	{
		$this->position = 1;
	}

	public function rewind() 
	{
		$this->position = 1;
		rewind($this->fileResource);
		fgets($this->fileResource, 4) == "\xEF\xBB\xBF" ? fseek($this->fileResource, 3) : fseek($this->fileResource, 0);
	}

	public function current() 
	{
		$csv_string = fgetcsv($this->fileResource, 4048, $this->csvDelimiter);
		if ($this->readEncoding != 'UTF-8' && !empty($csv_string)) {
			foreach ($csv_string as $key => $var) {
				$csv_string[$key] = iconv($this->readEncoding, "UTF-8//IGNORE", $var);
			}
		}
		return $csv_string;
	}

	public function key() 
	{
		return $this->position;
	}

	public function next() 
	{
		++$this->position;
	}

	public function valid() 
	{
		return !feof($this->fileResource);
	}

   	public function setFileResource($fileResource)
   	{
		$this->fileResource = $fileResource;
	}

	public function setCsvDelimiter($csvDelimiter)
	{
		$this->csvDelimiter = $csvDelimiter;
	}

	public function setFileReadEncoding($fileReadEncoding)
	{
		$this->readEncoding = $fileReadEncoding;
	}
}


