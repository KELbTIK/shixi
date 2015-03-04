<?php


class FactoryReflector
{
	function setFactory($factory)
	{
		$this->factory = $factory;
	}
	
	function create($name, $args = array())
	{
		$arguments = array();
		for ($i = 0; $i < count($args); $i++)
		{
			$arguments[] = '$args['.$i.']';
		}
		eval('$object = $this->factory->create' . $name . '(' . join(",", $arguments) . ');');
		return $object;
	}
}

?>