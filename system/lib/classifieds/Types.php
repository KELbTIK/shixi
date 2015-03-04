<?php

class SJB_Types
{
	public static function display($object_info, $display_type)
	{
		switch ($object_info['type']) {
			case 'string': 	$data_object = new MetaString($object_info);	break;
			case 'float':	$data_object = new MetaFloat($object_info);		break;
			case 'integer':	$data_object = new MetaInteger($object_info);	break;
			case 'text':	$data_object = new MetaText($object_info);		break;
			case 'boolean':	$data_object = new MetaBoolean($object_info);	break;
			case 'list':	$data_object = new MetaList($object_info);		break;
		}
		if (isset($object_info['read_only']) && $object_info['read_only']) {
			$display_type = 'display';
		}
		return $data_object->display($display_type);
	}
}

