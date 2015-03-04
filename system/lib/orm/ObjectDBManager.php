<?php

class SJB_ObjectDBManager
{
	/**
	 * @param $db_table_name
	 * @param SJB_Object $object
	 * @param bool $sid
	 * @param array $listingSidsForCopy
	 * @return bool
	 */
	public static function saveObject($db_table_name, SJB_Object $object, $sid = false, $listingSidsForCopy = array())
	{
		$object_sid = $object->getSID();
		if (is_null($object_sid)) {
			if ($sid) {
				if (!SJB_DB::query("INSERT INTO ?w (sid) VALUES($sid)", $db_table_name))
					return false;
				else 	
					$object_sid = $sid;
			}
		 	elseif (!$sid && !$object_sid = SJB_DB::query("INSERT INTO ?w() VALUES()", $db_table_name))
				return false;
			$object->setSID($object_sid);
		}
		
		if (!empty($listingSidsForCopy)) {
			SJB_ListingManager::copyFilesAndPicturesFromListing($listingSidsForCopy['filesFrom'], $object_sid, $listingSidsForCopy['picturesFrom']);
		}
		
		$object_details = $object->getDetails();
		$object_properties = $object_details->getProperties();
		$complexFields = array();
		foreach ($object_properties as $object_property) {
			if (!$object_property->saveIntoBD())
				continue;

			if ($object_property->isComplex()) {
				$complexProperties  = $object_property->type->complex->getProperties();
				$propertyId         = $object_property->getID();
				$complexFields[$propertyId] = array();
				if ($complexProperties) {
					foreach ($complexProperties as $complexProperty) {
						$complexProperty->setObjectSID($object_property->object_sid);
						$fieldValues = $complexProperty->getValue();
						if (!empty($fieldValues) && is_array($fieldValues)) {
							foreach ($fieldValues as $complexEnum => $value) {
								$complexProperty->setValue($value);
								$complexProperty->setComplexEnum($complexEnum);
								$complexProperty->setComplexParent($propertyId);
								$propertySqlValue   = $complexProperty->getSQLValue();
								$complexPropertyID  = $complexProperty->getID();
								$complexParameter   = $complexProperty->getAddParameter();
								if ($complexParameter == '') {
									$complexFields[$propertyId][$complexPropertyID][$complexEnum] = $propertySqlValue == 'NULL' ? NULL : $propertySqlValue;
								} else {
									$complexFields[$propertyId][$complexPropertyID][$complexEnum] = array(
										'add_parameter' => $complexParameter,
										'value'         => $propertySqlValue == 'NULL' ? NULL : $propertySqlValue
									);
								}
							}
							$complexProperty->setValue($fieldValues);
						}
					}
				}
			}
			elseif ($object_property->isParent()){
				$childProperties = $object_property->type->child->getProperties();
				$parentID = $object_property->getID();
				$keywords = '';
				if ($childProperties) {
					foreach ($childProperties as $childProperty) {
						$childProperty->setObjectSID($object_property->object_sid);
						$property_id = $parentID."_".$childProperty->getID();
						$property_sql_value = $childProperty->getSQLValue();
						if ($childProperty->getID() == 'State') {
							$displayAS = $childProperty->display_as;
							$displayAS = $displayAS?$displayAS:'state_name';
							$childProperty->type->list_values = SJB_StatesManager::getStatesNamesByCountry(false, true, $displayAS);
						}
						$keywords .= $childProperty->getKeywordValue() . ' ';
						if (empty($property_sql_value) && in_array($childProperty->getType(), array('boolean', 'integer', 'float'))) {
							$property_sql_value = 0;
						}
						SJB_DB::query("UPDATE `?w` SET `?w` = ?s WHERE sid = ?n", $db_table_name, $property_id, $property_sql_value, $object_sid);
					}
				}
				$origValue = $object_property->getValue();
				$object_property->setValue($keywords);
				$property_id = $object_property->getID();
				$property_sql_value = $object_property->getSQLValue();
				$object_property->setValue($origValue);
				SJB_DB::query("UPDATE `?w` SET `?w` = ?s WHERE sid = ?n", $db_table_name, $property_id, $property_sql_value, $object_sid);
			} else {
				$property_id = $object_property->getID();
				$property_sql_value = $object_property->getSQLValue();
				$property_sql_add_parameter = $object_property->getAddParameter();
				if ($object_property->isSystem()) {
					if (empty($property_sql_value) && in_array($object_property->getType(), array('boolean', 'integer', 'float'))) {
						$property_sql_value = 0;
					}
					
					SJB_DB::query("UPDATE `?w` SET `?w` = ?s WHERE sid = ?n", $db_table_name, $property_id, $property_sql_value, $object_sid);
					if (!empty($property_sql_add_parameter)) {
						if (($object_property->getType() == 'monetary') && ($object->getObjectType() != 'field')) {
							SJB_DB::query("UPDATE `?w` SET `?w` = ?w WHERE sid = ?n", $db_table_name, $property_id . '_parameter', $property_sql_add_parameter, $object_sid);
						} else {
							SJB_DB::query("UPDATE `?w` SET `add_parameter` = ?w WHERE sid = ?n", $db_table_name, $property_sql_add_parameter, $object_sid);
						}
					}
				} else {
					if (SJB_DB::table_exists($db_table_name . "_properties")) {
						$property_exists = SJB_DB::queryValue("SELECT COUNT(*) FROM ?w WHERE object_sid = ?n AND id = ?s", $db_table_name . "_properties", $object_sid, $property_id);
						if ($property_exists)
							SJB_DB::query("UPDATE ?w SET value = ?s, add_parameter = ?s WHERE object_sid = ?n AND id = ?s", $db_table_name . "_properties", $property_sql_value, $property_sql_add_parameter, $object_sid, $property_id);
						else
							SJB_DB::query("INSERT INTO ?w(object_sid, id , value, add_parameter) VALUES(?n, ?s, ?s, ?s)", $db_table_name . "_properties", $object_sid, $property_id, $property_sql_value, $property_sql_add_parameter);
					}
				}
			}
		}
		
		if (!empty($complexFields)) {
			SJB_DB::query("UPDATE `?w` SET `?w` = ?s WHERE sid = ?n", $db_table_name, 'complex', serialize($complexFields), $object_sid);
		}
	}
	
	public static function getObjectInfo($dbTableName, $objectSid)
	{
		$objectInfo = SJB_DB::query("SELECT * FROM ?w WHERE sid = ?n", $dbTableName, $objectSid);
		$objectInfo = array_pop($objectInfo);
		if (empty($objectInfo)) {
			return null;
		}
		
		foreach ($objectInfo as $key => $val) {
			$locationFields = array("{$key}_Country", "{$key}_State", "{$key}_City", "{$key}_Address", "{$key}_ZipCode");
			if (array_key_exists("{$key}_Country", $objectInfo) && array_key_exists("{$key}_State", $objectInfo) && array_key_exists("{$key}_City", $objectInfo)) {
				$objectInfo[$key] = array();
				foreach ($locationFields as $field) {
					if (array_key_exists($field, $objectInfo)) {
						$objectInfo[$key][str_replace("{$key}_", "", $field)] = $objectInfo[$field];
					}
				}
			}
			if (array_key_exists("{$key}_parameter", $objectInfo)) {
				$value = $objectInfo[$key];
				$addParameter = $objectInfo["{$key}_parameter"];
				unset($objectInfo[$key]);
				$objectInfo[$key]["add_parameter"] = $addParameter;
				$objectInfo[$key]["value"] = $value;
			}
		}
		
		if ($dbTableName == 'listings' && $objectInfo['complex'] != '') {
			$complexFields = unserialize($objectInfo['complex']);
			$objectInfo = array_merge($complexFields, $objectInfo);
		}
		
		$objectInfoProperties = self::executeObjectProperties($dbTableName, $objectSid);
		
		return array_merge($objectInfoProperties, $objectInfo);
	}
	
	private static function executeObjectProperties($dbTableName, $objectSid)
	{
		$objectInfoProperties = array();
		if (SJB_DB::table_exists($dbTableName . "_properties")) {
			$objectProperties = SJB_DB::query("SELECT * FROM ?w WHERE object_sid = ?n", $dbTableName . "_properties", $objectSid);
			
			foreach ($objectProperties as $objectProperty) {
				if (isset($objectProperty['add_parameter']) && $objectProperty['add_parameter'] != '') {
					$objectInfoProperties[$objectProperty['id']]['add_parameter'] = $objectProperty['add_parameter'];
					$objectInfoProperties[$objectProperty['id']]['value']         = $objectProperty['value'];
				} else {
					$objectInfoProperties[$objectProperty['id']] = $objectProperty['value'];
				}
			}
		}
		
		return $objectInfoProperties;
	}
	
	public static function getObjectsInfoByType($db_table_name)
	{
		$objects_info = SJB_DB::query('SELECT * FROM ?w', $db_table_name);
		foreach ($objects_info as $i => $object_info)
			$objects_info[$i] = SJB_ObjectDBManager::getObjectInfo($db_table_name, $object_info['sid']);
		return $objects_info;
	}

	public static function deleteObjectInfoFromDB($db_table_name, $object_sid)
	{
		if (SJB_DB::table_exists($db_table_name . '_properties')) {
            if (SJB_DB::query('DELETE FROM ?w WHERE object_sid = ?n', $db_table_name . '_properties', $object_sid))
				return SJB_DB::query('DELETE FROM ?w WHERE sid = ?n', $db_table_name, $object_sid);
			return false;
		}
		return SJB_DB::query('DELETE FROM ?w WHERE sid = ?n', $db_table_name, $object_sid);
	}

	public static function deleteObject($db_table_name, $object_sid)
	{
		return SJB_ObjectDBManager::deleteObjectInfoFromDB($db_table_name, $object_sid);
	}

	public static function saveField($db_table_name, $field_table_name, $object, $oldFieldID = false)
	{
		$fieldID = $object->getPropertyValue('id');
		$type = $object->getPropertyValue('type');
		if ($type != 'complex' && $fieldID != 'ApplicationSettings') {
			$issetField = SJB_DB::query("SHOW COLUMNS FROM `?w` WHERE `Field` like binary ?s", $db_table_name, $fieldID);
			if (!$issetField) {
				$fieldInfo = self::getObjectInfo($field_table_name, $object->getSID());
				$newFieldObject = new SJB_ObjectProperty( $fieldInfo );
				$fieldType = $newFieldObject->type->getSQLFieldType();
				if ($oldFieldID) {
					if (!empty($fieldInfo['parent_sid'])) {
						$parentInfo = self::getObjectInfo($field_table_name, $fieldInfo['parent_sid']);
						if (!empty($parentInfo['id']))
							SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` ?w", $db_table_name, $parentInfo['id'].'_'.$oldFieldID, $parentInfo['id'].'_'.$fieldID, $fieldType);
					}
					else
						SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` ?w", $db_table_name, $oldFieldID, $fieldID, $fieldType);
				}
				else {
					SJB_DB::query("ALTER TABLE `?w` ADD `?w` ?w", $db_table_name, $fieldID, $fieldType);
					if ($type == 'monetary') {
						SJB_DB::query("ALTER TABLE `?w` ADD `?w` ?w", $db_table_name, $fieldID . "_parameter", $fieldType);
					}
					if ($fieldType != 'TEXT NULL' && $fieldType != 'LONGTEXT NULL')
						SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID);
				}
			}
		}
	}


	public static function saveLocationField($db_table_name, $field_table_name, $object, $oldFieldID = false)
	{
		$fieldID = $object->getPropertyValue('id');
		$issetField = SJB_DB::query("SHOW COLUMNS FROM `?w` WHERE `Field` like binary ?s", $db_table_name, $fieldID);
		if (!$issetField) {
			$fieldInfo = self::getObjectInfo($field_table_name, $object->getSID());
			$newFieldObject = new SJB_ObjectProperty( $fieldInfo );
			$fieldType = $newFieldObject->type->getSQLFieldType();
			if ($oldFieldID) {
				SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` ?w", $db_table_name, $oldFieldID, $fieldID, $fieldType);
				
				SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` VARCHAR(255) NULL", $db_table_name, $oldFieldID."_Country", $fieldID."_Country");
				SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` VARCHAR(255) NULL", $db_table_name, $oldFieldID."_State", $fieldID."_State");
				SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` VARCHAR(255) NULL", $db_table_name, $oldFieldID."_City", $fieldID."_City");
				SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` VARCHAR(255) NULL", $db_table_name, $oldFieldID."_ZipCode", $fieldID."_ZipCode");
				if ($db_table_name == 'users') {
					SJB_DB::query("ALTER TABLE `?w` CHANGE `?w` `?w` VARCHAR(255) NULL", $db_table_name, $oldFieldID."_Address", $fieldID."_Address");
				}
			}
			else {
				SJB_DB::query("ALTER TABLE `?w` ADD `?w` ?w", $db_table_name, $fieldID, $fieldType);
				SJB_DB::query("ALTER TABLE `?w` ADD FULLTEXT ( `?w` ) ", $db_table_name, $fieldID);
				
				SJB_DB::query("ALTER TABLE `?w` ADD `?w` VARCHAR(255) NULL", $db_table_name, $fieldID."_Country");
				SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID."_Country");
				
				SJB_DB::query("ALTER TABLE `?w` ADD `?w` VARCHAR(255) NULL", $db_table_name, $fieldID."_State");
				SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID."_State");
				
				SJB_DB::query("ALTER TABLE `?w` ADD `?w` VARCHAR(255) NULL", $db_table_name, $fieldID."_City");
				SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID."_City");
				
				SJB_DB::query("ALTER TABLE `?w` ADD `?w` VARCHAR(255) NULL", $db_table_name, $fieldID."_ZipCode");
				SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID."_ZipCode");
				
				if ($db_table_name == 'users') {
					SJB_DB::query("ALTER TABLE `?w` ADD `?w` VARCHAR(255) NULL", $db_table_name, $fieldID."_Address");
					SJB_DB::query("ALTER TABLE `?w` ADD INDEX ( `?w` ) ", $db_table_name, $fieldID."_Address");
				}
			}
		}
	}

	public static function deleteField($db_table_name, $field_name)
	{
		return SJB_DB::query("ALTER TABLE `?w` DROP `?w`", $db_table_name, $field_name);
	}
}

