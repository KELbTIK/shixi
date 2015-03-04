<?php

class SJB_ObjectManager
{
	public static function saveObject($db_table_name, &$object)
	{
		return SJB_ObjectDBManager::saveObject($db_table_name, $object);
	}

	public static function getObjectInfoBySID($db_table_name, $object_sid)
	{
		return SJB_ObjectDBManager::getObjectInfo($db_table_name, $object_sid);
	}

	public static function getObjectInfo($object)
	{
		$object_info = array();
		$properties = $object->details->getProperties();

		foreach ($properties as $property)
			$object_info['user_defined'][$property->getID()] = $property->getValue();

		$system_object_info = SJB_ObjectManager::getSystemObjectInfo($object);

		$object_info['system'] 			= $system_object_info;
		$object_info['system']['id'] 	= $object->getID();

		return $object_info;
	}
	
	public static function getObjectMetaData($object) 
 	{ 
 		$object_meta = array(); 
 		$properties  = $object->details->getProperties(); 

 		foreach ($properties as $property)  { 
			$object_meta[$property->getID()]['type'] = $property->getType(); 
			$object_meta[$property->getID()]['propertyID'] = $property->getID(); 
			$object_meta[$property->getID()]['propertySID'] = $property->getSID(); 
 		} 
 		 
 		return $object_meta; 
	}

	public static function deleteObject($db_table_name, $object_sid)
	{
		return SJB_ObjectDBManager::deleteObject($db_table_name, $object_sid);
	}

	public static function getSystemObjectInfo($object)
	{
		$object_system_info = SJB_DB::query("SELECT * FROM `?w` WHERE `sid` = ?n", $object->db_table_name, $object->getSID());

		if (!empty($object_system_info)) {
			return array_pop($object_system_info);
		}
		else {
	        $system_properties = SJB_DB::query("SHOW COLUMNS FROM `?w`", $object->db_table_name);
			foreach ($system_properties as $property) {
				$object_system_info[$property['Field']] = null;
			}
		}

		return $object_system_info;
	}

	public static function getPropertyValueByObjectSID($db_table_name, $sid, $property_name)
	{
		return SJB_DB::queryValue("SELECT `value` FROM `?w`
							WHERE `id`=?s AND `object_sid`=?s",
							$db_table_name.'_properties', $property_name, $sid);
	}

	public static function getSystemPropertyValueByObjectSID($db_table_name, $sid, $property_name)
	{
		return SJB_DB::queryValue("SELECT `?w` FROM `?w`
							WHERE `sid`=?s",
							$property_name, $db_table_name, $sid);
	}

	public static function propertyNameIsAccepted($property_name)
	{
		return ($property_name != 'db_table_name') &&
			   (strpos('details', $property_name) === false);
	}
}

