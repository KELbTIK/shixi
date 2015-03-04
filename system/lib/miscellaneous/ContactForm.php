<?php

class SJB_ContactForm
{
	var $name;
	var $email;
	var $comments;
	
	var $is_form_submitted;
	var $is_data_valid;
	var $field_errors;
	
	function parseRequestedData($request_data)
	{
		$this->name = isset($request_data['name']) ? $request_data['name'] : "";
		$this->email = isset($request_data['email']) ? $request_data['email'] : "";
		$this->comments = isset($request_data['comments']) ? $request_data['comments'] : "";
		$this->is_form_submitted = (isset($request_data['action']) && ($request_data['action'] == 'send_message'));
	}
	
	function isFormSubmitted()
	{
		return $this->is_form_submitted;
	}
	
	function assignTemplateVariables(&$template_processor)
	{
		$template_processor->assign('name', $this->name);
		$template_processor->assign('email', $this->email);
		$template_processor->assign('comments', $this->comments);
	}
	
	function isDataValid()
	{
		$this->_checkData();
		return $this->is_data_valid;
	}
	
	function getFieldErrors()
	{
		return $this->field_errors;
	}
	
	function sendMessage()
	{
		SJB_AdminNotifications::sendContactFormMessage($this->name, $this->email, $this->comments);
	}
	
	function _checkData()
	{
		if (strlen($this->name) < 2)
			$this->field_errors['NAME'] = 1;
		if (strlen($this->comments) < 2)
			$this->field_errors['COMMENTS'] = 1;
		if (strlen($this->email) < 2 || strpos($this->email,'@') === false )
			$this->field_errors['EMAIL'] = 1;
		$this->is_data_valid = empty($this->field_errors);
	}
}
