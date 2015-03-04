<?php


class SJB_FormCollection
{
	var $forms = array();

	function SJB_FormCollection($object_collection)
	{
		foreach ($object_collection as $object) {
			$this->forms[$object->getSID()] = new SJB_Form($object);
		}
	}

	function registerTags(&$tp)
	{
		$tp->unregisterPlugin('function', "display");
		$tp->unregisterPlugin('function', "input");
		$tp->unregisterPlugin('function', "search");
		$tp->registerPlugin('function', "display", array(&$this, "tpl_display"));
		$tp->registerPlugin('function', "input", array(&$this, "tpl_input"));
		$tp->registerPlugin('function', "search", array(&$this, "tpl_search"));
		foreach ($this->forms as $index => $form) {
			$this->forms[$index]->registerTemplateProcessor($tp);
		}
	}

	function tpl_display($params, $smatry)
	{
		if (isset($params['object_sid'])) {
			$object_sid = $params['object_sid'];
		}
		elseif (isset($params['object_id'])) {
			$object_sid = $params['object_id'];
		}
		return $this->forms[$object_sid]->tpl_display($params, $smatry);
	}

    function tpl_input($params)
	{
        $object_sid = $params['object_sid'];
		return $this->forms[$object_sid]->tpl_input($params);
	}

	function tpl_search($params)
	{
        $object_sid = $params['object_sid'];
		return $this->forms[$object_sid]->tpl_search($params);
	}

	function makeDisabled($property_id)
	{
		foreach ($this->forms as $form) {
			$form->makeDisabled($property_id);
		}
	}

	function makeNotRequired($property_id)
	{
		foreach ($this->forms as $form) {
			$form->makeDisabled($property_id);
		}
	}

	function isDataValid(&$form_errors)
	{
		foreach ($this->forms as $form) {
			$errors = array();
			$form->isDataValid($errors);
			$form_errors = array_merge($form_errors, $errors);
		}
	}
}


