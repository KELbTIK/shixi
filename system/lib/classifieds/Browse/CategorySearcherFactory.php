<?php

class SJB_CategorySearcherFactory
{
	function SJB_CategorySearcherFactory()
	{
		$this->map = Array(
			'tree' => 'create_CategorySearcher_Tree',
			'list' => 'create_CategorySearcher_List',
			'multilist' => 'create_CategorySearcher_Multilist',
			'integer' => 'create_CategorySearcher_Value',
			'string' => 'create_CategorySearcher_Value',
		);
	}
	
	function getCategorySearcher($field)
	{
		$type = $field['type'];
		if ($this->_existsCategorySearcher($type)) {
 			$searcher = $this->_create_CategorySearcher($type, $field);
 			return $searcher;
		}
		die("CategorySearcher for the '" . $type . "' type does not exist");
	}
	
	function _existsCategorySearcher($type)
	{
		return isset($this->map[$type]);
	}
	
	function _create_CategorySearcher($type, $field)
	{
		$staticMethodName = $this->map[$type];
	 	$s = "\$obj_ref = SJB_ObjectMother::" . $staticMethodName . "(\$field);";
	 	eval($s);
		return $obj_ref;
	}
}
