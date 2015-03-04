<?php

class SJB_Form
{
	var $object_properties	= array();
	/**
	 * @var null|SJB_TemplateProcessor
	 */
	var $template_processor = null;
	var $path_to_templates  = null;
	var $form_fields 		= array();
	/**
	 * @var null|SJB_Object
	 */
	var $object				= null;
	var $errors				= false;
	
	private $useDefaultValues;

	/**
	 * @param null|SJB_Object $object
	 */
	function SJB_Form(SJB_Object $object = null)
	{
		if (!empty($object)) {
			$object_details = $object->getDetails();
			$this->object_properties = $object_details->getProperties();
			$this->object_properties = $this->object_properties?$this->object_properties:array();
			foreach ($this->object_properties as $object_property) {
				$form_field['caption'] 		= $object_property->getCaption();
				$form_field['is_system'] 	= $object_property->isSystem();
				$form_field['id']           = $object_property->getID();
				$form_field['is_required'] 	= $object_property->isRequired();
				$form_field['disabled'] 	= false;
				$form_field['order'] 		= $object_property->getOrder();
				$form_field['comment'] 		= $object_property->getComment();
				$form_field['type'] 		= $object_property->getType();
				$form_field['instructions']	= $object_property->getInstructions();
				$form_field['is_classifieds'] = $object_property->isClassifieds();
				$form_field['use_autocomplete'] = $object_property->isUsingAutocomplete();
				$form_field['display_as'] = $object_property->getDisplayAs();

				$this->form_fields[$object_property->getID()] = $form_field;
			}
			$this->object = $object;
		}

		$this->path_to_templates = '../field_types/';
	}

	/**
	 * @param SJB_TemplateProcessor $template_processor
	 */
	function registerTemplateProcessor(SJB_TemplateProcessor $template_processor)
	{
		$this->template_processor = $template_processor;
	}

	function setUseDefaultValues()
	{
		$this->useDefaultValues = true; 
	}

	/**
	 * @param SJB_TemplateProcessor $tp
	 */
	function registerTags($tp)
	{
		$tp->unregisterPlugin('function', 'input');
		$tp->unregisterPlugin('function', 'display');
		$tp->registerPlugin('function', 'input', array(&$this, 'tpl_input'));
		$tp->registerPlugin('function', 'display', array(&$this, 'tpl_display'));
		$this->registerTemplateProcessor($tp);
	}

	function makeDisabled($property_id)
	{
		$this->form_fields[$property_id]['disabled'] 	 = true;
		$this->form_fields[$property_id]['is_required']  = false;
	}

	function makeNotRequired($property_id) 
	{
		$this->form_fields[$property_id]['is_required'] = false;
	}

	function getFormFieldsInfo()
	{
		return $this->form_fields;
	}

	function isDataValid(&$errors, $addValidParam = false)
	{
		foreach ($this->object_properties as $object_property) {
			$is_valid = $object_property->isValid($addValidParam);

			if ($is_valid !== true) {
				if (is_array($is_valid))
					$errors = array_merge($errors, $is_valid);
				else
					$errors[$object_property->getCaption()] = $is_valid;
			}
		}

		if (!empty($errors)) {
			$this->errors = true;
			return false;
		}
		return true;
	}

	function objectHasProperty($property_name)
	{
		return isset($this->object_properties[$property_name]);
	}

	function getObjectProperty($property_name)
	{
		return $this->object_properties[$property_name];
	}

	function tpl_property($viewType, $params)
	{
		$params['parameters']['viewType'] = $viewType;

		if (!$this->assignTemplateVariables($params)) {
			return;
		}
		
		$complexParent = '';
		if (!empty($params['complexParent'])) {
			$complexParent = $params['complexParent'];
		}
		$parent = '';
		if (!empty($params['parent'])) {
			$parent = $params['parent'];
		}
		$template     = isset($params['template']) ? $params['template'] : $this->getDefaultTemplateByFieldName($params['property'], $complexParent, $parent);
		$templatePath = $this->path_to_templates . $viewType . '/' . $template;
		$html         = $this->template_processor->fetch($templatePath);
		$this->removeAssignedTemplateVariables();
		
		return $html;
	}

	function assignTemplateVariables($params)
	{
		global $variables_to_assign;
		$variables_to_assign = array();
		if ($this->objectHasProperty($params['property'])) {
			$object_property = $this->getObjectProperty($params['property']);
			$variables_to_assign = $object_property->getPropertyVariablesToAssign();
		}

		if (!empty($params['complexParent'])) {
			$complexParent = $params['complexParent'];
			$object = $this->object_properties[$complexParent]->type->complex;
			$object_properties = $object->getProperties();

			if (isset($object_properties[$params['property']])) {
				$variables_to_assign = $object_properties[$params['property']]->getPropertyVariablesToAssign();
			}

			if (isset($params['complexStep']) && !empty($this->object_properties[$complexParent]->value)) {
				if (is_string($this->object_properties[$complexParent]->value)) {
					$complexValue = unserialize($this->object_properties[$complexParent]->value);
				} else {
					$complexValue = $this->object_properties[$complexParent]->value;
				}

				//exception for monetary type
				if (!isset($variables_to_assign['list_currency'])) {
					$variables_to_assign['value'] = '';
				}
				if (isset($complexValue[$params['property']]) && isset($complexValue[$params['property']][$params['complexStep']])) {
					if ($object_properties[$params['property']]->getType() === 'date' && $object_properties[$params['property']]->type->getConvertToDBDate()) {
						$field = $object_properties[$params['property']];
						$field->type->property_info['value'] = $complexValue[$params['property']][$params['complexStep']];
						if ($field->isValid() !== true) {
							$complexValue[$params['property']][$params['complexStep']] = '';
						}
						$variables_to_assign['value'] = SJB_I18N::getInstance()->getInput('date', $complexValue[$params['property']][$params['complexStep']]);
					}
					elseif ($object_properties[$params['property']]->getType() == 'monetary') {
						if (isset($complexValue[$params['property']][$params['complexStep']]['value'])) {
							$variables_to_assign['value']['value'] = htmlentities($complexValue[$params['property']][$params['complexStep']]['value'], ENT_QUOTES, "UTF-8");
						} else {
							$variables_to_assign['value'] = htmlentities($complexValue[$params['property']][$params['complexStep']], ENT_QUOTES, "UTF-8");
						}
					}
					elseif ($object_properties[$params['property']]->getType() == 'multilist') {
						$value = $complexValue[$params['property']][$params['complexStep']];
						if (!is_array($value) && strpos($value, ',')) {
							$variables_to_assign['value'] = explode(',', $value);
						} else {
							$variables_to_assign['value'] = $value;
						}
					}
					elseif ($object_properties[$params['property']]->getType() !== 'text') {
						$variables_to_assign['value'] = htmlentities($complexValue[$params['property']][$params['complexStep']], ENT_QUOTES, "UTF-8");
					} else {
						$variables_to_assign['value'] = $complexValue[$params['property']][$params['complexStep']];
					}
				}
			}
		}
		if (!empty($params['parent'])) {
			$parent = $params['parent'];
			$object = $this->object_properties[$parent]->type->child;
			$object_properties = $object->getProperties();
			if (isset($object_properties[$params['property']])) {
				$variables_to_assign = $object_properties[$params['property']]->getPropertyVariablesToAssign();
			}
			elseif (strpos($params['property'], '.Code')) {
				$params['property'] = str_replace('.Code', '', $params['property']);
				if (!isset($object_properties[$params['property']])) {
					return false;
				}
				$variables_to_assign = $object_properties[$params['property']]->getPropertyVariablesToAssign();
				if ($params['property'] == 'State') {
					$country = !empty($params['country']) ? $params['country'] : false;
					$variables_to_assign['list_values'] = SJB_StatesManager::getStatesNamesByCountry($country, true, 'state_code');
				}
				$variables_to_assign['displayAS'] = 'Code';
			} else {
				$params['property'] = str_replace('.Name', '', $params['property']);
				if (!isset($object_properties[$params['property']])) {
					return false;
				}
				$variables_to_assign = $object_properties[$params['property']]->getPropertyVariablesToAssign();
				if ($params['property'] == 'State') {
					$country = !empty($params['country']) ? $params['country'] : false;
					$variables_to_assign['list_values'] = SJB_StatesManager::getStatesNamesByCountry($country, true, 'state_name');
				}
				$variables_to_assign['displayAS'] = 'Name';
			}
		}
		if (isset($params['fields'])) {
			$fields = $params['fields'];
			$object_property = $this->getObjectProperty($params['property']);
			$fieldProperties = $object_property->type->child->getProperties();
			foreach ($fieldProperties as $key => $property) {
				if (!array_key_exists($property->getSID(), $fields)) {
					$object_property->type->child->deleteProperty($key);
				}
			}
			foreach ($fields as $key => $field) {
				$object_property->type->child->addProperty($field);
			}
			$variables_to_assign = $object_property->getPropertyVariablesToAssign();
		}

		if (isset($params['complexStep'])) {
			$variables_to_assign['complexStep'] = $params['complexStep'];
		}

		if (isset($params['parameters'])) {
			$variables_to_assign = array_merge($variables_to_assign, array('parameters' => $params['parameters']));
		}
		// made for FormBuilders Complex Fields. so admin could define custom html code
		if (isset($params['customHtml']) && !empty($params['customHtml'])) {
			$variables_to_assign['customHtml'] = trim($params['customHtml']);
		}
		$variables_to_assign = array_merge($variables_to_assign, $this->getVariablesToAssign($params));
		$varToAssignValueIsEmpty = $this->isEmptyVariablesToAssignValue($variables_to_assign);
		if (($this->useDefaultValues || !$this->object->getSID()) && $varToAssignValueIsEmpty && $this->errors === false) {
			if ($variables_to_assign['default_value'] != '') {
				if (is_array($variables_to_assign['default_value'])) {
					$variables_to_assign['default_value']['currency'] = $variables_to_assign['default_value']['add_parameter'];
				}
				$variables_to_assign['value'] = $variables_to_assign['default_value'];
			}
			else if ($variables_to_assign['profile_field_as_dv'] != '') {
				$variables_to_assign['value'] = htmlentities($variables_to_assign['profile_field_as_dv'], ENT_QUOTES, 'UTF-8');
			}
		}

		// заглушка для email - когда в value попадает массив из одного элемента [original]
		if ($variables_to_assign['id'] == 'email') {
			if (is_array($variables_to_assign['value'])) {
				$variables_to_assign['value'] = array_pop($variables_to_assign['value']);
			}
		}

		$variables_to_assign['defaultCountry'] = SJB_Settings::getSettingByName('default_country');

		if (isset($params['searchWithin'])) {
			$variables_to_assign['searchWithin'] = $params['searchWithin'];
		}
        if (!isset($variables_to_assign['displayAS'])) {
            $variables_to_assign['displayAS'] = false;
        }
		if ($variables_to_assign['id'] == 'default_value' && in_array($this->object->getProperty('default_value')->getType(), array('list', 'multilist'))) {
			$variables_to_assign['sort_by_alphabet'] = $this->object->getPropertyValue('sort_by_alphabet');
		}
        foreach ($variables_to_assign as $variable_name => $variable_value) {
            $this->template_processor->assign($variable_name, $variable_value);
        }
		return true;
	}

	private function removeAssignedTemplateVariables()
	{
		global $variables_to_assign;
		$this->template_processor->clearAssign(array_keys($variables_to_assign));
	}

	protected function isEmptyVariablesToAssignValue($variables_to_assign)
	{
		$objectProperty	  = $this->getObjectProperty($variables_to_assign['id']);
		$isMonetaryType   = is_a($objectProperty, 'SJB_ObjectProperty') && $objectProperty->getType() === 'monetary';
		$varToAssignValue = SJB_Array::get($variables_to_assign, 'value');

		if ($isMonetaryType) {
			$varToAssignValueIsEmpty = empty($varToAssignValue['value']) && empty($varToAssignValue['currency']);
		}
		else {
			$varToAssignValueIsEmpty = ($varToAssignValue == '');
		}
		return $varToAssignValueIsEmpty;
	}

	function getVariablesToAssign($params)
	{
		if ($this->objectHasProperty($params['property'])) {
			$object_property = $this->getObjectProperty($params['property']);
			return $object_property->getPropertyVariablesToAssign();
		}

		return array();
	}

	function tpl_input($params)
	{
		$oldObject = false;
		if (!empty($params['object'])) {
			$oldObject = $this->object;
			$this->SJB_Form($params['object']);
		}
		if ($this->form_fields[$params['property']]['disabled']) {
			$result = $this->tpl_property('display', $params);
		} else {
			$result = $this->tpl_property('input', $params);
		}
		if ($oldObject !== false) {
			$this->SJB_Form($oldObject);
		}
		return $result;
	}

	function tpl_search($params)
	{
		$oldObject = false;
		if (!empty($params['object'])) {
			$oldObject = $this->object;
			$this->SJB_Form($params['object']);
		}
		$this->template_processor->filterThenAssign('templateParams', $params);
		if ($this->form_fields[$params['property']]['disabled']) {
			$result = $this->tpl_property('display', $params);
		} else {
			$result = $this->tpl_property('search', $params);
		}
		if ($oldObject !== false) {
			$this->SJB_Form($oldObject);
		}
		return $result;
	}

	function tpl_display($params, $smatry)
	{
		$oldObject = false;
		if (!empty($params['object'])) {
			$oldObject = $this->object;
			$this->SJB_Form($params['object']);
		}
		if (isset($params['assign'])) {
			$smatry->assign($params['assign'], trim($this->tpl_property('display', $params)));
			$result = '';
    	}
		else {
			$result = trim($this->tpl_property('display', $params));
		}
		if ($oldObject !== false) {
			$this->SJB_Form($oldObject);
		}

		return $result;
	}

	function getDefaultTemplateByFieldName($property_name, $complexParent = '', $parent = '')
	{
		if ($this->objectHasProperty($property_name)) {
			return $this->object_properties[$property_name]->getDefaultTemplate();
		}
		if (!empty($complexParent)) {
			$object = $this->object_properties[$complexParent]->type->complex;
			$object_properties = $object->getProperties();
			if (isset($object_properties[$property_name])) {
				return $object_properties[$property_name]->getDefaultTemplate();
			}
		}
		if (!empty($parent)) {
			$object = $this->object_properties[$parent]->type->child;
			$object_properties = $object->getProperties();
			$property_name = str_replace('.Name', '', $property_name);
			$property_name = str_replace('.Code', '', $property_name);
			if (isset($object_properties[$property_name])) {
				return $object_properties[$property_name]->getDefaultTemplate();
			}
		}
		return 'string.tpl';
	}

	/**
	 *
	 * @param string $property_name
	 * @param string $newTemplate
	 */
	function setDefaultTemplateByFieldName($property_name, $newTemplate)
	{
		if ($this->objectHasProperty($property_name)) {
			$this->object_properties[$property_name]->setDefaultTemplate($newTemplate);
		}
	}
}

