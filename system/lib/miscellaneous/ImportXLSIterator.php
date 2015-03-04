<?php

class SJB_ImportXLSIterator implements Iterator 
{
	private $position = 1;
	private $chunkFilter = false; 
	private $objReader = false;
	private $fileName = false;
	private $rowResult = array();

	public function __construct() 
	{
		reset($this->rowResult);
		$this->position = 1;
	}

	public function rewind() 
	{
		reset($this->rowResult);
		$this->position = 1;
	}

	public function current() 
	{
		return current($this->rowResult);
	}

	public function key() 
	{
		return $this->position;
	}

	public function next() 
	{
		next($this->rowResult);
		++$this->position;
	}

	public function valid() 
	{
		$currentItem = current($this->rowResult);
		if (!empty($currentItem)) {
			return true;
		} 
		else {
			$this->chunkFilter->setRows($this->position, 2000); 
			$objPHPExcel = $this->objReader->load($this->fileName);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$highestRow = $objWorksheet->getHighestRow();
			$result = array();
			for ($row = $this->position; $row <= $highestRow; $row++) {
				$rowObj = new PHPExcel_Worksheet_Row($objWorksheet, $row);
				$cellIterator = $rowObj->getCellIterator();
		  		$cellIterator->setIterateOnlyExistingCells(false);
		  		$arrayValues = array(); 
				foreach ($cellIterator as $cell) {
				  	$arrayValues[] = str_replace('_x000D_', '', $cell->getValue());
				}
				$result[$row] = $arrayValues;
			}
			$objPHPExcel->disconnectWorksheets(); 
			unset($objPHPExcel); 
			$this->rowResult = $result;
			return !empty($result);
		}
	}
    
	public function setObjReader($objReader)
	{
		$this->objReader = $objReader;
	}
    
	public function setChunkFilter($chunkFilter)
	{
		$this->chunkFilter = $chunkFilter;
	}
    
	public function setFileName($filename)
	{
		$this->fileName = $filename;
	}
}


