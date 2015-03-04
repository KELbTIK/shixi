<?php

namespace SJB\Db;

use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\ResultSet\Exception;

class ResultSet extends \Zend\Db\ResultSet\ResultSet
{
	public function __construct($returnType = \Zend\Db\ResultSet\ResultSet::TYPE_ARRAY, $arrayObjectPrototype = null)
	{
		\Zend\Db\ResultSet\ResultSet::__construct($returnType, $arrayObjectPrototype);
	}


	public function toArray()
	{
		if ($this->dataSource instanceof Result) {
			return $this->dataSource->getResource()->fetchAll(\PDO::FETCH_ASSOC);
		}
		// todo: реализовать подгрузку mysqli значений
//		if ($this->dataSource instanceof \Zend\Db\Adapter\Driver\Mysqli\Result) {
//			$this->dataSource->getResource()
//		}
		return parent::toArray();
	}
}
