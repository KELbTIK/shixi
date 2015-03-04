<?php

class SJB_ImportFileCSV extends SJB_ImportFile
{
	protected $csv_delimiter;

	public function SJB_ImportFileCSV($file_info, $csv_delimiter = ';')
	{
		ini_set("auto_detect_line_endings", true);
		$this->data = array();
		parent::SJB_ImportFile($file_info);
		
		switch ($csv_delimiter) {
			case 'comma':
			case ',':
				$this->csv_delimiter = ",";
				break;
			
			case 'tab':
				$this->csv_delimiter = "\t";
				break;
				
			default:
				$this->csv_delimiter = ';';
				break;
		}
	}

	public function parse($readEncoding = 'UTF-8')
	{
        if ( !$file_resource = fopen($this->file_info['tmp_name'], "r") ) {
			$this->errors["CANNOT_OPEN_FILE"] = $this->file_info['tmp_name'];
			return false;
		}

		$this->data = new SJB_ImportIterator;
		$this->data->setFileResource($file_resource);
		$this->data->setFileReadEncoding($readEncoding);
		$this->data->setCsvDelimiter($this->csv_delimiter);
	}
}

