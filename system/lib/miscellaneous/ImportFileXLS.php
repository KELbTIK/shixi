<?php

class SJB_ImportFileXLS extends SJB_ImportFile
{
	function parse()
	{
		$objReader = PHPExcel_IOFactory::createReaderForFile($this->file_info['tmp_name']);
		$chunkFilter = new SJB_ChunkReadFilter(); 
		$objReader->setReadFilter($chunkFilter); 
		$objReader->setReadDataOnly(true);
		$this->data = new SJB_ImportXLSIterator;
		$this->data->setObjReader($objReader);
		$this->data->setChunkFilter($chunkFilter);
		$this->data->setFileName($this->file_info['tmp_name']);
	}
}

class SJB_ChunkReadFilter implements PHPExcel_Reader_IReadFilter 
{
    private $_startRow = 0;
    private $_endRow = 0;

    public function setRows($startRow, $chunkSize) 
    { 
        $this->_startRow    = $startRow; 
        $this->_endRow      = $startRow + $chunkSize;
    } 

    public function readCell($column, $row, $worksheetName = '') 
    {
        if ($row >= $this->_startRow && $row < $this->_endRow) { 
           return true;
        }
        return false;
    } 
}

