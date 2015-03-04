<?php

interface SJB_TypeValidator
{
	public static function isValid($type);
}

class SJB_IdValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (preg_match("/[^_\w\d]/u", $type->property_info['value']))
			return 'NOT_VALID_ID_VALUE';
		return true;
	}
}

class SJB_UserFieldIdValidator extends SJB_IdValidator
{
	public static function isValid($type)
	{
		$value = SJB_Array::get($type->property_info, 'value');
		$reservedIDs = array(
			'sendmail',
			'username',
			'email',
			'password',
		);
		return in_array(strtolower($value), $reservedIDs) ? 'RESERVED_ID_VALUE' : parent::isValid($type);
	}
}

class SJB_OneCharValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (!preg_match("/[a-zA-Z]{1,}/", $type->property_info['value'])) 
			return 'NOT_STRING_ID_VALUE';
		return true;
	}
}

class SJB_IdWithSpaceValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (preg_match("/[^_,-\p{L}\d\s]/u", $type->property_info['value'])) {
			return 'NOT_VALID_ID_VALUE';
		}
		return true;
	}
}

class SJB_UniqueSystemValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (!empty($type->property_info['addValidParam'])){
			$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n  AND `{$type->property_info['addValidParam']['field']}` = '{$type->property_info['addValidParam']['value']}'",
				$type->property_info['table_name'], $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		}
		else
			$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n ", $type->property_info['table_name'], $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		if ($count) 
			return 'NOT_UNIQUE_VALUE';
		
		return true;
	}
}

class SJB_UniqueStateSystemValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n  AND `{$type->property_info['addValidParam']['field']}` = '{$type->property_info['addValidParam']['value']}'",
				$type->property_info['table_name'], $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		
		if ($count) 
			return 'NOT_UNIQUE_VALUE';
		
		return true;
	}
}

class SJB_UniqueSystemPagesValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n  AND `{$type->property_info['addValidParam']['field']}` = '{$type->property_info['addValidParam']['value']}'",
				$type->property_info['table_name'], $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		if ($count) 
			return 'NOT_UNIQUE_VALUE';
		
		return true;
	}
}

class SJB_UniqueSystemComplexValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n ", $type->property_info['table_name'], $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		if (!$count) {
			$table_name = str_replace('_complex_', '_', $type->property_info['table_name']);
			$count = SJB_DB::queryValue("SELECT count(*) FROM ?w WHERE ?w = ?s AND sid <> ?n ", $table_name, $type->property_info['id'], $type->property_info['value'], $type->object_sid);
		}
		if ($count) 
			return 'NOT_UNIQUE_VALUE';
		
		return true;
	}
}

class SJB_UniqueSystemUserProfileFieldsValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		$user_group_sid = SJB_Request::getVar('user_group_sid');
		$count = SJB_DB::queryValue("SELECT COUNT(*) FROM ?w WHERE user_group_sid = ?n AND `?w` = ?s AND sid <> ?n", $type->property_info['table_name'], $user_group_sid, SJB_DB::quote($type->property_info['id']), $type->property_info['value'], $type->object_sid);
		if ($count) 
			return 'NOT_UNIQUE_VALUE';
		
		return true;
	}
}

class SJB_PlusValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if ($type->property_info['value'] < 0) {
			return 'NOT_PLUS_VALUE';
		}
		return true;
	}	
}

class SJB_GoogleAnalyticsIdValidator implements SJB_TypeValidator
{
	public static function isValid($fieldValue)
	{
		if (!preg_match("/^(UA-){1}([0-9]){6,}(-)([0-9]){1,2}$/", $fieldValue))
			return 'The code you have entered is invalid. Please try again.';
		return true;
	}
}

class SJB_UserRegistrationDateValidator implements SJB_TypeValidator
{
	public static function isValid($value)
	{
		if (!preg_match("/\d{4}\-\d{1,2}\-\d{1,2} \d{1,2}\:\d{1,2}\:\d{1,2}/", $value)) {
			return 'Warning: The following users have wrong registration date format. Registration date for them was automatically set up for current date:';
		}

		return true;
	}
}

class SJB_StringWithoutTagsValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (strcmp(strip_tags($type->property_info['value']), $type->property_info['value']) === 0)
			return true;
		return 'NOT_VALID_STRING_VALUE';
	}
}

class SJB_CurrencyCodeValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (mb_strlen($type->property_info['value']) > 3) {
			return 'NOT_VALID_ID_VALUE';
		}

		return true;
	}
}

class SJB_UniqueSystemListingFieldsValidator implements SJB_TypeValidator
{
	public static function isValid($type)
	{
		if (!SJB_ListingFieldManager::getListingFieldSIDByID($type->property_info['value'])) {
			if (SJB_DB::query('SHOW COLUMNS FROM `listings` WHERE `Field` = ?s', $type->property_info['value'])) {
				return 'NOT_UNIQUE_VALUE';
			}
		}
		return true;
	}
}
