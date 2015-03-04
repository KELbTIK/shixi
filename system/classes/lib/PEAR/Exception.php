<?php

class SJB_PEAR_Exception extends PEAR_Exception
{
	public function __construct($message, $p2 = null, $p3 = null)
	{
		parent::__construct($message, $p2, $p3);
		throw $this;
	}
}