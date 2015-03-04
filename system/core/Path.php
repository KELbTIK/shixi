<?php

class SJB_Path
{
	public static function combine()
	{
		$combainer = new PathCombiner(DIRECTORY_SEPARATOR);
		$args = func_get_args();
		return $combainer->vcombine($args);
	}
	
	public static function combineURL()
	{
		$combainer = new PathCombiner('/');
		$args = func_get_args();
		return $combainer->vcombine($args);
	}
}

class PathCombiner
{
	function PathCombiner($separator)
	{
		$this->separator = $separator;
	}

	function combine()
	{
		$args = func_get_args();
		return $this->vcombine($args);
	}

	function vcombine($args)
	{
		$args = $this->_remove_slahes_at_start_exclude_first_arg($args);
		$args = $this->_remove_slahes_at_end($args);
		$args = $this->_filter_empty_elements($args);
		return join($this->separator, $args);
	}
	
	function _remove_slahes_at_start_exclude_first_arg($args)
	{
		$first_element = array_shift($args);
		$args = array_map(array($this, '_remove_slah_at_start'), $args);
		array_unshift($args, $first_element);
		return $args;
	}
	
	function _remove_slahes_at_end($args)
	{
		return array_map(array($this, '_remove_slah_at_end'), $args);
	}
	
	function _filter_empty_elements($args)
	{
		return array_filter($args, array($this, '_not_empty'));
	}

	function _not_empty($value)
	{
		return !empty($value);
	}
	
	function _remove_slah_at_end($value)
	{
		return preg_replace("/(".'\\'.$this->separator."*)$/u", '', $value);
	}

	function _remove_slah_at_start($value)
	{
		return preg_replace("/^(".'\\'.$this->separator."*)/u", '', $value);
	}
}

