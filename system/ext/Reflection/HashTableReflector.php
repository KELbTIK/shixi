<?php


class HashtableReflector
{
	var $hashtable = array();
	
	function setHashtable($hashtable)
	{
		$this->hashtable = $hashtable;
	}
	
	function get($item_key)
	{
		if(!preg_match("/^\[/", $item_key))
			$item_key = "['".$item_key."']";
		return eval("return isset(\$this->hashtable$item_key) ? \$this->hashtable$item_key : null;");
	}
}

?>