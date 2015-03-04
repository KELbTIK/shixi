<?php

class SJB_Miscellaneous_ContactForm extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$contact_form = SJB_ObjectMother::createContactForm();
		$contact_form->parseRequestedData($_REQUEST);

		if ($contact_form->isFormSubmitted()) {
			$errors = !$contact_form->isDataValid() ? $contact_form->getFieldErrors() : array();
			SJB_Captcha::getInstance($tp, $_REQUEST)->isValid($errors);
			if (!$errors) {
				$contact_form->sendMessage();
				$tp->assign('message_sent', true);
			} else {
				$tp->assign('field_errors', $errors);
			}
		}

		$contact_form->assignTemplateVariables($tp);
		$tp->display('contact_form.tpl');

	}
}

