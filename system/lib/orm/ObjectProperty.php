<?php

class SJB_ObjectProperty
{
	var $value;
	var $type;
	var $order;

	var $sid;
	var $id;
	var $caption;
	var $comment;
	var $is_classifieds;
	var $is_using_autocomplete;
	var $display_as;

	var $is_system;
	var $is_required;
	var $save_into_db;

	var $object_sid;

	function SJB_ObjectProperty($property_info)
	{
		$this->id = $property_info['id'];
		$this->caption = $property_info['caption'];

		if (isset($property_info['value']) && is_string($property_info['value']))	
			$property_info['value'] = trim($property_info['value']);

		$this->sid 			= isset($property_info['sid']) 	   	   ? $property_info['sid'] 			: null;
		$this->value 		= isset($property_info['value']) 	   ? $property_info['value'] 		: null;
		$this->is_system 	= isset($property_info['is_system'])   ? $property_info['is_system'] 	: false;
		$this->is_required 	= isset($property_info['is_required']) ? $property_info['is_required'] 	: false;
		$this->save_into_db = isset($property_info['save_into_db'])? $property_info['save_into_db']	: true;
		$this->order 		= isset($property_info['order'])	   ? $property_info['order']		: null;
		$this->comment 		= isset($property_info['comment'])	   ? $property_info['comment']		: null;
		$this->is_classifieds = isset($property_info['is_classifieds']) ? $property_info['is_classifieds'] : false;
		$this->is_using_autocomplete = isset($property_info['use_autocomplete']) ? $property_info['use_autocomplete'] : false;
		$this->display_as   = isset($property_info['display_as']) ? $property_info['display_as'] : false;

		switch($property_info['type']) {

			case 'list':
				$this->type = new SJB_ListType($property_info);
				break;
			case 'multilist':
				$this->type = new SJB_MultiListType($property_info);
				break;
			case 'string':
				$this->type = new SJB_StringType($property_info);
				break;
			case 'text':
				$this->type = new SJB_TextType($property_info);
				break;
			case 'integer':
			case 'int':
				$this->type = new SJB_IntegerType($property_info);
				break;
			case 'float':
				$this->type = new SJB_FloatType($property_info);
				break;
			case 'boolean':
				$this->type = new SJB_BooleanType($property_info);
				break;
			case 'geo':
				$this->type = new SJB_GeoType($property_info);
				break;
			case 'file':
				$this->type = new SJB_UploadFileType($property_info);
				break;
			case 'video':
				$this->type = new SJB_UploadVideoFileType($property_info);
				break;
			case 'pictures':
				$this->type = new SJB_PicturesType($property_info);
				break;
			case 'tree':
				$this->type = new SJB_TreeType($property_info);
				break;
			case 'password':
				$this->type = new SJB_PasswordType($property_info);
				break;
			case 'password_cur':
				$this->type = new SJB_PasswordCurType($property_info);
				break;
			case 'unique_string':
				$this->type = new SJB_UniqueStringType($property_info);
				break;
			case 'id_string':
				$this->type = new SJB_IdStringType($property_info);
				break;
			case 'date':
				$this->type = new SJB_DateType($property_info);
				break;
			case 'picture':
				$this->type = new SJB_PictureType($property_info);
				break;
			case 'logo':
				$this->type = new SJB_LogoType($property_info);
				break;
			case 'captcha':
				$this->type = new SJB_CaptchaType($property_info);
				break;
			case 'email':
				$this->type = new SJB_EmailType($property_info);
				break;
			case 'unique_email':
				$this->type = new SJB_UniqueEmailType($property_info);
			break;
			case 'youtube':
				$this->type = new SJB_YouTubeType($property_info);				
			break;
			case 'monetary':
				$this->type = new SJB_MonetaryType($property_info);				
			break;	
			case 'complex':
				$this->type = new SJB_ComplexType($property_info);		
			break;
			case 'complexfile':
				$this->type = new SJB_ComplexFileType($property_info);		
			break;
			case 'location':
				$this->type = new SJB_LocationType($property_info);
			break;
			case 'id':
				$this->type = new SJB_IdType($property_info);
			break;
		}
	}

	function getPropertyVariablesToAssign() { return $this->type->getPropertyVariablesToAssign(); }

    function getSavableValue() 				{ return $this->type->getSavableValue(); }

	function setObjectSID($sid)
	{
		$this->type->setObjectSID($sid);
		$this->object_sid = $sid;
	}
	
	function setKeywordValue($value)
	{
		$this->type->setKeywordValue($value);
	}

	function isValid($addValidParam = false)
	{
		if (!$this->type->isEmpty()) {
			return $this->type->isValid($addValidParam);
		}
		if ($this->is_required) {
			return 'EMPTY_VALUE';
		}
		return true;
	}

	function isSearchValueValid()
	{
        if (!is_array($this->value)) $value = trim($this->value);
       	else						 $value = trim((string)($this->value)); // ZAPLATKA!!!!!!!!!

        if (empty($value)) {
        	return false;
		}
		return $this->type->isValid();
	}
	
	function isComplex() 
	{
		return $this->type->isComplex();
	}
	
	function isParent() 
	{
		return $this->type->isParent();
	}

    function getID() 		{ return $this->id; }
    function getSID() 		{ return $this->sid; }
	function getCaption() 	{ return $this->caption; }
	function getComment() 	{ return $this->comment; }
	function isRequired()	{ return $this->is_required; }
	function isSystem() 	{ return $this->is_system; }
	function saveIntoBD() 	{ return $this->save_into_db; }
	function getOrder() 	{ return $this->order; }
	function isClassifieds() 	{ return $this->is_classifieds; }
	function isUsingAutocomplete() 	{ return $this->is_using_autocomplete; }
	function getDisplayAs() { return $this->display_as; }

	function setValue($value)
	{
		$this->value = $value;
		$this->type->setValue($value);
	}
	
	function setID($id)
	{
		$this->id = $id;
		$this->type->setID($id);
	}
	
	function getValue() 			{ return $this->type->getValue(); }
	function getSQLValue($context = null) { return $this->type->getSQLValue($context); }
	function getAddParameter($context = null) { return $this->type->getAddParameter($context); }
	function getKeywordValue() 		{ return $this->type->getKeywordValue(); }
	function getKeywordValueForAutocomplete() 		{ return $this->type->getKeywordValueForAutocomplete(); }
	
	function getDisplayValue() 		{ return $this->type->getDisplayValue(); }

	public function getObjectType()
	{
		return $this->type;
	}

	function getType()				{ return $this->type->getType(); }
	function getInstructions()				{ return $this->type->getInstructions(); }
	function getSQLType()			{ return $this->type->getSQLType(); }

	function getDefaultTemplate() 	{ return $this->type->getDefaultTemplate(); }
	function setDefaultTemplate( $template ) 	{ return $this->type->setDefaultTemplate( $template ); }

	function makeRequired() 		{ $this->is_required = true;  $this->type->makeRequired(); }
	function makeNotRequired() 		{ $this->is_required = false; $this->type->makeNotRequired(); }

	function setSaveFlag() 			{ $this->save_into_db = true; }
	function setDontSaveFlag() 		{ $this->save_into_db = false; }
	function setComplexParent($parent = null) 	{ $this->type->setComplexParent($parent); }
	function setComplexEnum($value) { $this->type->setComplexEnum($value); }
}

