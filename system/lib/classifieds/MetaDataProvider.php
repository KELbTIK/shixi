<?php

class SJB_MetaDataProvider
{
	public static function getMetaData($metadata)
	{
		$meta_data = array();
		foreach($metadata as $key => $value) {
			if (is_array(current($value))) {
				$meta_data[$key] = SJB_MetaDataProvider::getMetaData($value);
			}
			else {
			    $meta_data[$key] = SJB_MetaDataProvider::_get_meta_data_item($value);   
			}
		}
		
		return $meta_data;
	}

	public static function getFormFieldsMetadata($form_fields, $domain = 'Frontend')
	{
		$meta_data = array();
		foreach($form_fields as $key => $value) {
			$meta_data[$key]["caption"]["domain"] = $domain;
		}
		return $meta_data;
	}
	
	public static function getPaymentMetaData($domain)
	{
		$meta_data = array(
			'name'  => array('domain' => $domain),
			'price' => array('type' => 'float'),
		);		
		
		return $meta_data;
	}

	public static function _get_meta_data_item($meta_data)
	{
		if (isset($meta_data['type'])) {
    		switch($meta_data['type']) {
    			case 'integer':
    				return array('type' => 'int');
    			case 'date':
    				return array('type' => 'date');
    			case 'boolean':
    				return array('type' => 'boolean');
    			case 'geo':
    				return array('type' => 'geo');
    			case 'string':
    				return array('type' => 'string');
    			case 'video':
    				return array('type' => 'video');
    			case 'text':
    				return array('type' => 'text');
    			case 'pictures':
    				return array('type' => 'pictures');
    		}
		}
		
		return array();
	}

	public static function getBrowsingMetaData()
	{
		// Meta data for browsing realization lies in the BrowseManager
	}
}
  
