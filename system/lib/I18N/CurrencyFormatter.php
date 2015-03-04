<?php

class SJB_CurrencyFormatter
{
	private $lang = array();
	private $sign = '';

	public function __construct()
	{
		$i18n = SJB_I18N::getInstance();
		$this->lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$this->sign = SJB_Settings::getValue('transaction_currency');
	}

	/**
	 * Positions currency sign relative to currency amount
	 * depending on current language configuration
	 *
	 * @param  array  $params Currency amount and sign
	 * @return string Ready currency string
	 */
	public function currencyFormat($params)
	{
		$sign   = isset($params['sign']) ? $params['sign'] : $this->sign;
		$amount = isset($params['amount']) ? $params['amount'] : 0;

		//Formatting output
		if ($this->lang['currencySignLocation'] == 0) {
			if ($this->lang['rightToLeft']) {
				return $amount . $sign;
			}
			return $sign . $amount;
		}
		if ($this->lang['rightToLeft']) {
			return $sign . '&nbsp;' . $amount;
		}
		return $amount . '&nbsp;' . $sign;
	}
}
