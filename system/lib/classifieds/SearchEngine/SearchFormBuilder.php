<?php

define('SEARCH_TAG_NAME', 'search');

class SJB_SearchFormBuilder extends SJB_Form
{
	private $criteria = array();

	function setCriteria($criteria_data)
	{
		$this->criteria = $criteria_data;
	}

	function getCriteria()
	{
		return $this->criteria;
	}

	/**
	 * @param SJB_TemplateProcessor $tp
	 */
	function registerTags($tp)
	{
		$this->template_processor = $tp;
		$tp->registerPlugin('function', "search", array($this, "tpl_search"));
	}
	
	function getVariablesToAssign($params)
	{
		$value = array();
		if (!empty($params['parent'])) {
			$params['property'] = $params['parent']."_".$params['property'];
		}
		
		if (!empty($params['fields'])) {
			foreach ($params['fields'] as $field) {
				$criteria = $this->getCriteriaByFieldName($params['property']."_".$field['id']);
				if (!empty($criteria)) {
					foreach ($criteria as $criterion) {
						$value[$field['id']] = $criterion->getValue();
					}
				}
			}
		}
		
		$criteria = $this->getCriteriaByFieldName($params['property']);
		if (!empty($criteria)) {
			$value = array();
			
			foreach ($criteria as $criterion) {
				$criterion_type = $criterion->getType();
				$field_value 	= $criterion->getFieldValue();
				
				if (!empty($field_value)) {
					$value[$criterion_type] = SJB_HelperFunctions::getClearVariablesToAssign($field_value);
				} else {
					if ($criterion->type == 'tree') {
						$criterionProperty = $criterion->getProperty();
						if (!$criterionProperty->type->displayAsSelectBoxes) {
							$treeHelper = new SJB_TreeHelper('');
							$value      = $treeHelper->createTreeObjects($criterion->value);
						} else {
							$value = SJB_HelperFunctions::getClearVariablesToAssign($criterion->value);
						}
					} else {
						$value = $value + SJB_HelperFunctions::getClearVariablesToAssign($criterion->getValue());
					}
				}
			}
		} else {
			$value = SJB_HelperFunctions::getClearVariablesToAssign($value);
		}
		
		$res = array(
			'id'    => $params['property'],
			'value' => $value
		);
		
		if (isset($params['type']) && $params['type'] == 'bool' && $params['property'] !== 'Title') {
			$params2 = $params;
			$params2['property'] = 'Title';
			$titleProp = $this->getVariablesToAssign($params2);
			if (!empty($titleProp) && !empty($titleProp['value'])) {
				$res['title'] = true;
				$res['value'] = $titleProp['value'];
			}
		}
		
		if (!empty($params['complexParent'])) {
			$res['id'] = $params['complexParent'] . ":" . $res['id'];
		}
		return $res;
	}

	function getDefaultTemplateByFieldName($property_name, $complexParent = '', $parent = '')
	{
		$template_name = 'string.tpl';
		if (SJB_ListingManager::propertyIsCommon($property_name)) { // is common property
			$property = SJB_ListingManager::getPropertyByPropertyName($property_name);
			$template_name = $property->getDefaultTemplate();
		}
		elseif (isset($this->object_properties[$property_name])) { // is object property
			$property = $this->object_properties[$property_name];
			$template_name = $property->getDefaultTemplate();
		}
		elseif (!empty($complexParent)) {
			$object = $this->object_properties[$complexParent]->type->complex;
			$object_properties = $object->getProperties();
			if (isset($object_properties[$property_name]))
				$template_name = $object_properties[$property_name]->getDefaultTemplate();
		}
		if (!empty($parent)) {
			$object = $this->object_properties[$parent]->type->child;
			$object_properties = $object->getProperties();
			if (isset($object_properties[$property_name])) {
				return $object_properties[$property_name]->getDefaultTemplate();
			}
		}

		return $template_name;
	}

	function getCriteriaByFieldName($property_name)
	{
		foreach ($this->criteria as $criteria) {
			foreach ($criteria as $criteria_property_name => $property_criteria) {
				if ($criteria_property_name == $property_name) {
					return $property_criteria;
				}
			}
		}

		return array();
	}

	public static function extractCriteriaFromRequestData($request_data, $object = null)
	{
		$criteria = array(	'system' => array(),
							'common' => array(),);
	
		foreach($request_data as $property_name => $criteria_data) {
			if (is_array($criteria_data)) {
				foreach($criteria_data as $criterion_type => $criterion_value) {
					$criterion = SJB_SearchCriterion::getCriterionByType($criterion_type);

					if (!is_null($criterion)) {
						if (!empty($object)) {
							$object_details    = $object->getDetails();
							$object_properties = $object_details->getProperties();
						}

						$property = isset($object_properties[$property_name]) ? $object_properties[$property_name] : null;
						if (empty($property) && !empty($object)) {
							foreach ($object_properties as $objectProperty) {
								if ($objectProperty->isComplex()) {
									$object_properties = $objectProperty->type->complex->getProperties();
									$propertyNameWithoutParent = str_replace($objectProperty->getID() . ':', '', $property_name);
									if (isset($object_properties[$propertyNameWithoutParent])) {
										$property = $objectProperty;
										break;
									}
								}
							}
						}
						
						$property_is_system = SJB_ListingManager::propertyIsSystem($property_name) || (!empty($property) && $property->isSystem());
						
						//*** integer, float, date  i18n transformation
						if (empty($property)) {
							$property = SJB_ListingManager::getPropertyByPropertyName($property_name);
						}
						
						if (!empty($property) && preg_match("/integer|float|date/i", $property->getType())) {
							$property->setValue($criterion_value);
							
							if ($property->isValid()) {
								$criterion_value = $property->getValue();
							}
						}
						//*** ----------------------------------------
						
						$criterion->setProperty($property);
						$criterion->setPropertyName($property_name);
						$criterion->setValue($criterion_value);

						if ($property_is_system) {
							$criteria['system'][$property_name][] = $criterion;
						}
						else {
							$criteria['common'][$property_name][] = $criterion;
						}
					}
				}
			}
		}

		return $criteria;
	}
}
