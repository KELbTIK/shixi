<?php

class SJB_CashPayment extends SJB_PaymentGateway
{
	function SJB_CashPayment($gateway_info = array())
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_CashPaymentDetails($gateway_info);
	}

	function getUrl()
	{
		return SJB_System::getSystemSettings('SITE_URL') . '/cash-payment-page/';
	}

	function buildTransactionForm($invoice)
	{
		if (count($invoice->isValid()) == 0) {
			$cash_payment_url = $this->getUrl();
			$form_fields = $this->getFormFields($invoice);
			$properties = $this->details->getProperties();
			$gateway_caption = $properties['caption']->getValue();
			$form_hidden_fields = '';

			foreach ($form_fields as $name => $value) {
				$value = htmlentities($value, ENT_QUOTES, "UTF-8");
				$form_hidden_fields .= "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\" />";
			}

			$gateway['hidden_fields'] = $form_hidden_fields;
			$gateway['url'] = $cash_payment_url;
			$gateway['caption'] = $gateway_caption;

			return $gateway;
		}
		return null;
	}

	function getFormFields($invoice)
	{
		$properties = $this->details->getProperties();
		$id = $properties['id']->getValue();
		$form_fields = array
		(
			'invoice_sid' => $invoice->getSID(),
			'gatewayId' => strtolower($id),
		);
		return $form_fields;
	}


	function getTemplate()
	{
		return strtolower($this->getPropertyValue('id')) . '.tpl';
	}
}

