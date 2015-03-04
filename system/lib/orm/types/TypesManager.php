<?php

class SJB_TypesManager
{
	public static function getExtraDetailsByFieldType($field_type)
	{
		switch ($field_type) {
			
			case 'email':
				return SJB_EmailType::getFieldExtraDetails();
				
			case 'list':
				return SJB_ListType::getFieldExtraDetails();
				
			case 'multilist':
				return SJB_MultiListType::getFieldExtraDetails();
	
			case 'string':
				return SJB_StringType::getFieldExtraDetails();
	
			case 'text':
				return SJB_TextType::getFieldExtraDetails();
	
			case 'integer':
				return SJB_IntegerType::getFieldExtraDetails();
	
			case 'float':
				return SJB_FloatType::getFieldExtraDetails();

			case 'file':
				return SJB_UploadFileType::getFieldExtraDetails();

			case 'geo':
				return SJB_GeoType::getFieldExtraDetails();

			case 'video':
				return SJB_UploadVideoFileType::getFieldExtraDetails();
	
			case 'pictures':
				return SJB_PicturesType::getFieldExtraDetails();
	
			case 'tree':
				return SJB_TreeType::getFieldExtraDetails();
	
			case 'picture':
				return SJB_PictureType::getFieldExtraDetails();
			
			case 'logo':
				return SJB_LogoType::getFieldExtraDetails();
				
			case 'captcha':
				return SJB_CaptchaType::getFieldExtraDetails();
				
			case 'youtube':
				return SJB_YouTubeType::getFieldExtraDetails();		
					
			case 'monetary':
				return SJB_MonetaryType::getFieldExtraDetails();	

			case 'location':
				return SJB_LocationType::getFieldExtraDetails();
			break;
				
			default:
				return array();
		}
	}
}

