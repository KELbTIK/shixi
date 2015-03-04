<?php

class SJB_Payment_PaypalProFillPaymentCard extends SJB_Function
{
	const GATEWAY_ID = 'paypal_pro';
	private $errors = array();
	private $globalErrors = array();
	private $templateProcessor;
	private $formFields;
	/**
	 * @var SJB_Invoice
	 */
	private $invoice;

	public function isAccessible()
	{
		return SJB_UserManager::isUserLoggedIn();
	}

	public function execute()
	{
		if (SJB_Request::isAjax()) {
			die(json_encode($this->getPayPalStateList()));
		}
		$getInvoiceId = SJB_Request::getInt('payment_id', 0, 'GET');
		$this->invoice = SJB_InvoiceManager::getObjectBySID($getInvoiceId);
		if ($this->invoiceValidation($this->invoice)) {
			if ($this->isPayNowButtonPressed()) {
				$this->processPayNowButtonClick();
			} else {
				$this->displayForm();
			}
		}
	}

	/**
	 * @param $invoice
	 * @return bool
	 */
	private function invoiceValidation($invoice)
	{
		if ($invoice instanceof SJB_Invoice) {
			if (SJB_UserManager::getCurrentUserSID() != $invoice->getUserSID()) {
				SJB_FlashMessages::getInstance()->addError('NOT_OWNER');
				return false;
			}
			else if ($invoice->getStatus() == SJB_Invoice::INVOICE_STATUS_PAID) {
				SJB_FlashMessages::getInstance()->addError('INVOICE_ALREADY_PAID');
				return false;
			}
		} else {
			SJB_FlashMessages::getInstance()->addError('NOT_VALID_PAYMENT_ID');
			return false;
		}
		
		return true;
	}

	public function getFormFields()
	{
		if (is_null($this->formFields)) {
			$this->formFields = array();
			$items = $this->invoice->getPropertyValue('items');
			$taxInfo = $this->invoice->getPropertyValue('tax_info');
			if ($this->invoice->isRecurring()) {
				$i = 0;
				$this->formFields['notify_url'] = SJB_System::getSystemSettings('SITE_URL') . '/system/payment/notifications/' . self::GATEWAY_ID . '/';
				if (!empty($items['products'])) {
					foreach ($items['products'] as $key => $product) {
						$productInfo = $this->invoice->getItemValue($key);
						if ($taxInfo) {
							$productInfo['price'] += SJB_TaxesManager::getTaxAmount($productInfo['price'], $taxInfo['tax_rate'], $taxInfo['price_includes_tax']);
						}
						if (!empty($productInfo['recurring'])) {
							$this->formFields['recurring'][$i] = $productInfo;
						} else {
							$this->formFields['products'][$i] = $productInfo;
						}
						$i += 1;
					}
				}
			}
			$this->formFields['address'] = SJB_Request::getVar('address', false);
			$this->formFields['zip'] = SJB_Request::getVar('zip', false);
			$this->formFields['country'] = SJB_Request::getVar('country', false);
			$this->formFields['city'] = SJB_Request::getVar('city', false);
			$this->formFields['state'] = SJB_Request::getVar('state', false);
			$this->formFields['email'] = SJB_Request::getVar('email', false);
			$this->formFields['phone'] = SJB_Request::getVar('phone', false);
			$this->formFields['amount'] = SJB_Request::getVar('amount', false);
			$this->formFields['item_name'] = SJB_Request::getVar('item_name', false);
			$this->formFields['item_number'] = SJB_Request::getVar('item_number', false);
			$this->formFields['card_number'] = SJB_Request::getVar('card_number', false);
			$this->formFields['exp_date_mm'] = SJB_Request::getVar('exp_date_mm', false);
			$this->formFields['exp_date_yy'] = SJB_Request::getVar('exp_date_yy', false);
			$this->formFields['csc_value'] = SJB_Request::getVar('csc_value', false);
			$this->formFields['first_name'] = SJB_Request::getVar('first_name', false);
			$this->formFields['last_name'] = SJB_Request::getVar('last_name', false);
			$this->formFields['currency_code'] = SJB_Payment_PaypalProFillPaymentCard::getCurrencyCode();
		}

		return $this->formFields;
	}


	private static function getCurrencyCode()
	{
		$default_currency = SJB_CurrencyManager::getDefaultCurrency();
		$currency_code = $default_currency['currency_code'];
		return $currency_code;
	}

	private function isPayNowButtonPressed()
	{
		return !is_null(SJB_Request::getVar('action'));
	}

	private function processPayNowButtonClick()
	{
		$this->validate();
		if ($this->hasErrors()) {
			$this->displayErrors();
		} else {
			$this->makePayment();
		}
	}

	private function displayForm()
	{
		$this->assignInvoiceInfo();
		$this->assignIcons();
		$this->assignMonthList();
		$this->assignYearList();
		$this->assignCountryList();
		$this->assignCurrentUserCountry();
		$this->assignStateList();
		$this->assignHiddenFields();
		$this->getTemplateProcessor()->display('paypal_pro_fill_payment_card.tpl');
	}

	private function validate()
	{
		$form_fields = $this->getFormFields();
		$errors = array();
		if (empty($form_fields['first_name'])) {
			$errors['USER_NAME_IS_NOT_SET'] = 'User name is not set';
		}
		if (empty($form_fields['last_name'])) {
			$errors['USER_LAST_NAME_IS_NOT_SET'] = 'User last name is not set';
		}
		if (empty($form_fields['card_number'])) {
			$errors['CREDIT_CARD_NUMBER_IS_NOT_SET'] = 'Credit card number is not set';
		}
		if (empty($form_fields['exp_date_mm'])) {
			$errors['EXP_DATE_MM_IS_NOT_SET'] = 'Expiration month is not set';
		}
		if (empty($form_fields['exp_date_yy'])) {
			$errors['EXP_DATE_YY_IS_NOT_SET'] = 'Expiration year is not set';
		}
		if (empty($form_fields['csc_value'])) {
			$errors['CSC_VALUE_IS_NOT_SET'] = 'CSC value is not set';
		}
		if (empty($form_fields['address'])) {
			$errors['ADDRESS_IS_NOT_SET'] = 'Billing address is not set';
		}
		if (empty($form_fields['zip'])) {
			$errors['ZIP_CODE_IS_NOT_SET'] = 'ZIP code is not set';
		}
		if (empty($form_fields['city'])) {
			$errors['CITY_IS_NOT_SET'] = 'City is not set';
		}
		if (empty($form_fields['country'])) {
			$errors['COUNTRY_IS_NOT_SET'] = 'Country is not set';
		}
		if (empty($form_fields['state']) && in_array($this->formFields['country'], array("US", "GB", "AU", "CA"))) {
			$errors['STATE_IS_NOT_SET'] = 'State is not set';
		}

		$this->errors = array_merge($this->errors, $errors);
	}

	private function hasErrors()
	{
		return !empty($this->errors);
	}

	private function displayErrors()
	{
		$this->assignErrors();
		$this->assignFields();
		$this->displayForm();
	}

	private function makePayment()
	{
		$allFormFields = $this->getFormFields();
		$payPalProPayment = SJB_PaymentGatewayManager::getObjectByID(self::GATEWAY_ID, false);
		$payPalProPayment->makePayment($allFormFields);
	}

	private function assignHiddenFields()
	{
		$paymentAndGatewayData = $this->preparePaymentData();
		$hiddenFields = $this->getHiddenFieldsPart($paymentAndGatewayData);
		$this->getTemplateProcessor()->assign('hiddenFields', $hiddenFields);
	}

	private function preparePaymentData()
	{
		$data['amount'] = $this->invoice->GetPropertyValue('total');
		$data['item_name'] = $this->invoice->getProductNames();
		$data['item_number'] = $this->invoice->getID();
		return $data;
	}

	private function getHiddenFieldsPart($data)
	{
		$hidden_fields = 'amount item_name item_number';
		$payment_fields = explode(' ', $hidden_fields);
		$form_hidden_fields = array();
		foreach ($payment_fields as $name) {
			$form_hidden_fields[] = "<input type=\"hidden\" name=\"{$name}\" value=\"{$data[$name]}\" />";
		}
		return join("\r\n", $form_hidden_fields);
	}

	private function assignYearList()
	{
		$yearList = array();
		$year = date('Y');
		for ($i = 0; $i < 10; $i++) {
			$yearList[$i] = $year + $i;
		}
		$this->getTemplateProcessor()->assign('yearList', $yearList);
	}

	private function assignMonthList()
	{
		$monthList = array('01','02','03','04','05','06','07','08','09','10','11','12');
		$this->getTemplateProcessor()->assign('monthList', $monthList);
	}

	private function assignCountryList()
	{
		$CountryList = $this->getPayPalCountryList();
		$this->getTemplateProcessor()->assign('CountryList', $CountryList);
	}

	private function assignStateList()
	{
		$this->getTemplateProcessor()->assign('StateList', $this->getPayPalStateList());
	}

	/**
	 * @return SJB_TemplateProcessor
	 */
	private function getTemplateProcessor()
	{
		if (!isset ($this->templateProcessor)) {
			$this->templateProcessor = SJB_System::getTemplateProcessor();
		}
		return $this->templateProcessor;
	}

	private function assignErrors()
	{
		$this->getTemplateProcessor()->assign('errors', $this->errors);
	}

	/**
	 * <a href="https://cms.paypal.com/us/cgi-bin?cmd=_render-content&content_ID=developer/e_howto_api_ACCountryCodes&bn_r=o">
	 * country list from paypal documentation
	 * </a>
	 * @return array
	 */
	private function getPayPalCountryList()
	{
		return array(
			"ÅLAND ISLANDS" => "AX",
			"ALBANIA" => "AL",
			"ALGERIA" => "DZ",
			"AMERICAN SAMOA" => "AS",
			"ANDORRA" => "AD",
			"ANGUILLA" => "AI",
			"ANTARCTICA" => "AQ",
			"ANTIGUA AND BARBUDA" => "AG",
			"ARGENTINA" => "AR",
			"ARMENIA" => "AM",
			"ARUBA" => "AW",
			"AUSTRALIA" => "AU",
			"AUSTRIA" => "AT",
			"AZERBAIJAN" => "AZ",
			"BAHAMAS" => "BS",
			"BAHRAIN" => "BH",
			"BANGLADESH" => "BD",
			"BARBADOS" => "BB",
			"BELGIUM" => "BE",
			"BELIZE" => "BZ",
			"BENIN" => "BJ",
			"BERMUDA" => "BM",
			"BHUTAN" => "BT",
			"BOSNIA-HERZEGOVINA" => "BA",
			"BOTSWANA" => "BW",
			"BOUVET ISLAND" => "BV",
			"BRAZIL" => "BR",
			"BRITISH INDIAN OCEAN TERRITORY" => "IO",
			"BRUNEI DARUSSALAM" => "BN",
			"BULGARIA" => "BG",
			"BURKINA FASO" => "BF",
			"CANADA" => "CA",
			"CAPE VERDE" => "CV",
			"CAYMAN ISLANDS" => "KY",
			"CENTRAL AFRICAN REPUBLIC" => "CF",
			"CHILE" => "CL",
			"CHINA" => "CN",
			"CHRISTMAS ISLAND " => "CX",
			"COCOS (KEELING) ISLANDS" => "CC",
			"COLOMBIA" => "CO",
			"COOK ISLANDS" => "CK",
			"COSTA RICA" => "CR",
			"CYPRUS" => "CY",
			"CZECH REPUBLIC" => "CZ",
			"DENMARK" => "DK",
			"DJIBOUTI" => "DJ",
			"DOMINICA" => "DM",
			"DOMINICAN REPUBLIC" => "DO",
			"ECUADOR" => "EC",
			"EGYPT" => "EG",
			"EL SALVADOR" => "SV",
			"ESTONIA" => "EE",
			"FALKLAND ISLANDS (MALVINAS)" => "FK",
			"FAROE ISLANDS" => "FO",
			"FIJI" => "FJ",
			"FINLAND" => "FI",
			"FRANCE" => "FR",
			"FRENCH GUIANA" => "GF",
			"FRENCH POLYNESIA" => "PF",
			"FRENCH SOUTHERN TERRITORIES" => "TF",
			"GABON" => "GA",
			"GAMBIA" => "GM",
			"GEORGIA" => "GE",
			"GERMANY" => "DE",
			"GHANA" => "GH",
			"GIBRALTAR" => "GI",
			"GREECE" => "GR",
			"GREENLAND" => "GL",
			"GRENADA" => "GD",
			"GUADELOUPE" => "GP",
			"GUAM" => "GU",
			"GUERNSEY" => "GG",
			"GUYANA" => "GY",
			"HEARD ISLAND AND MCDONALD ISLANDS" => "HM",
			"HOLY SEE (VATICAN CITY STATE)" => "VA",
			"HONDURAS" => "HN",
			"HONG KONG" => "HK",
			"HUNGARY" => "HU",
			"ICELAND" => "IS",
			"INDIA" => "IN",
			"INDONESIA" => "ID",
			"IRELAND" => "IE",
			"ISLE OF MAN" => "IM",
			"ISRAEL" => "IL",
			"ITALY" => "IT",
			"JAMAICA" => "JM",
			"JAPAN" => "JP",
			"JERSEY" => "JE",
			"JORDAN" => "JO",
			"KAZAKHSTAN" => "KZ",
			"KIRIBATI" => "KI",
			"KOREA, REPUBLIC OF" => "KR",
			"KUWAIT" => "KW",
			"KYRGYZSTAN" => "KG",
			"LATVIA" => "LV",
			"LESOTHO" => "LS",
			"LIECHTENSTEIN" => "LI",
			"LITHUANIA" => "LT",
			"LUXEMBOURG" => "LU",
			"MACAO" => "MO",
			"MACEDONIA" => "MK",
			"MADAGASCAR" => "MG",
			"MALAWI" => "MW",
			"MALAYSIA" => "MY",
			"MALTA" => "MT",
			"MARSHALL ISLANDS" => "MH",
			"MARTINIQUE" => "MQ",
			"MAURITANIA" => "MR",
			"MAURITIUS" => "MU",
			"MAYOTTE" => "YT",
			"MEXICO" => "MX",
			"MICRONESIA, FEDERATED STATES OF" => "FM",
			"MOLDOVA, REPUBLIC OF" => "MD",
			"MONACO" => "MC",
			"MONGOLIA" => "MN",
			"MONTENEGRO" => "ME",
			"MONTSERRAT" => "MS",
			"MOROCCO" => "MA",
			"MOZAMBIQUE" => "MZ",
			"NAMIBIA" => "NA",
			"NAURU" => "NR",
			"NEPAL" => "NP",
			"NETHERLANDS" => "NL",
			"NETHERLANDS ANTILLES" => "AN",
			"NEW CALEDONIA" => "NC",
			"NEW ZEALAND" => "NZ",
			"NICARAGUA" => "NI",
			"NIGER" => "NE",
			"NIUE" => "NU",
			"NORFOLK ISLAND" => "NF",
			"NORTHERN MARIANA ISLANDS" => "MP",
			"NORWAY" => "NO",
			"OMAN" => "OM",
			"PALAU" => "PW",
			"PALESTINE" => "PS",
			"PANAMA" => "PA",
			"PARAGUAY" => "PY",
			"PERU" => "PE",
			"PHILIPPINES" => "PH",
			"PITCAIRN" => "PN",
			"POLAND" => "PL",
			"PORTUGAL" => "PT",
			"PUERTO RICO" => "PR",
			"QATAR" => "QA",
			"REUNION" => "RE",
			"ROMANIA" => "RO",
			"RUSSIAN FEDERATION" => "RU",
			"RWANDA" => "RW",
			"SAINT HELENA" => "SH",
			"SAINT KITTS AND NEVIS" => "KN",
			"SAINT LUCIA" => "LC",
			"SAINT PIERRE AND MIQUELON" => "PM",
			"SAINT VINCENT AND THE GRENADINES" => "VC",
			"SAMOA" => "WS",
			"SAN MARINO" => "SM",
			"SAO TOME AND PRINCIPE" => "ST",
			"SAUDI ARABIA" => "SA",
			"SENEGAL" => "SN",
			"SERBIA" => "RS",
			"SEYCHELLES" => "SC",
			"SINGAPORE" => "SG",
			"SLOVAKIA" => "SK",
			"SLOVENIA" => "SI",
			"SOLOMON ISLANDS" => "SB",
			"SOUTH AFRICA" => "ZA",
			"SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS" => "GS",
			"SPAIN" => "ES",
			"SURINAME" => "SR",
			"SVALBARD AND JAN MAYEN" => "SJ",
			"SWAZILAND" => "SZ",
			"SWEDEN" => "SE",
			"SWITZERLAND" => "CH",
			"TAIWAN, PROVINCE OF CHINA" => "TW",
			"TANZANIA, UNITED REPUBLIC OF " => "TZ",
			"THAILAND" => "TH",
			"TIMOR-LESTE" => "TL",
			"TOGO" => "TG",
			"TOKELAU" => "TK",
			"TONGA" => "TO",
			"TRINIDAD AND TOBAGO" => "TT",
			"TUNISIA" => "TN",
			"TURKEY" => "TR",
			"TURKMENISTAN" => "TM",
			"TURKS AND CAICOS ISLANDS" => "TC",
			"TUVALU" => "TV",
			"UGANDA" => "UG",
			"UKRAINE" => "UA",
			"UNITED ARAB EMIRATES" => "AE",
			"UNITED KINGDOM" => "GB",
			"UNITED STATES" => "US",
			"UNITED STATES MINOR OUTLYING ISLANDS" => "UM",
			"URUGUAY" => "UY",
			"UZBEKISTAN" => "UZ",
			"VANUATU" => "VU",
			"VENEZUELA" => "VE",
			"VIET NAM" => "VN",
			"VIRGIN ISLANDS, BRITISH" => "VG",
			"VIRGIN ISLANDS, U.S." => "VI",
			"WALLIS AND FUTUNA" => "WF",
			"WESTERN SAHARA" => "EH",
			"ZAMBIA" => "ZM",);
	}

	private function getPayPalStateList()
	{
		$country = $this->formFields['country'] ?: SJB_Request::getVar('countryCode');
		$states = null;
		switch ($country) {
			case 'US':
				$states = array(
					'AA' => 'AA',
					'AE' => 'AE',
					'AK' => 'AK',
					'AL' => 'AL',
					'AP' => 'AP',
					'AR' => 'AR',
					'AS' => 'AS',
					'AZ' => 'AZ',
					'CA' => 'CA',
					'CO' => 'CO',
					'CT' => 'CT',
					'DC' => 'DC',
					'DE' => 'DE',
					'FL' => 'FL',
					'FM' => 'FM',
					'GA' => 'GA',
					'GU' => 'GU',
					'HI' => 'HI',
					'IA' => 'IA',
					'ID' => 'ID',
					'IL' => 'IL',
					'IN' => 'IN',
					'KS' => 'KS',
					'KY' => 'KY',
					'LA' => 'LA',
					'MA' => 'MA',
					'MD' => 'MD',
					'ME' => 'ME',
					'MH' => 'MH',
					'MI' => 'MI',
					'MN' => 'MN',
					'MO' => 'MO',
					'MP' => 'MP',
					'MS' => 'MS',
					'MT' => 'MT',
					'NC' => 'NC',
					'ND' => 'ND',
					'NE' => 'NE',
					'NH' => 'NH',
					'NJ' => 'NJ',
					'NM' => 'NM',
					'NV' => 'NV',
					'NY' => 'NY',
					'OH' => 'OH',
					'OK' => 'OK',
					'OR' => 'OR',
					'PA' => 'PA',
					'PR' => 'PR',
					'PW' => 'PW',
					'RI' => 'RI',
					'SC' => 'SC',
					'SD' => 'SD',
					'TN' => 'TN',
					'TX' => 'TX',
					'UT' => 'UT',
					'VA' => 'VA',
					'VI' => 'VI',
					'VT' => 'VT',
					'WA' => 'WA',
					'WI' => 'WI',
					'WV' => 'WV',
					'WY' => 'WY'
				);
				break;
			case 'GB':
				$states = array(
					"England" => array(
						"Avon" => "Avon",
						"Bedfordshire" => "Bedfordshire",
						"Berkshire" => "Berkshire",
						"Bristol" => "Bristol",
						"Buckinghamshire" => "Buckinghamshire",
						"Cambridgeshire" => "Cambridgeshire",
						"Cheshire" => "Cheshire",
						"Cleveland" => "Cleveland",
						"Cornwall" => "Cornwall",
						"Cumbria" => "Cumbria",
						"Derbyshire" => "Derbyshire",
						"Devon" => "Devon",
						"Dorset" => "Dorset",
						"Durham" => "Durham",
						"East Riding of Yorkshire" => "East Riding of Yorkshire",
						"East Sussex" => "East Sussex",
						"Essex" => "Essex",
						"Gloucestershire" => "Gloucestershire",
						"Greater Manchester" => "Greater Manchester",
						"Hampshire" => "Hampshire",
						"Herefordshire" => "Herefordshire",
						"Hertfordshire" => "Hertfordshire",
						"Humberside" => "Humberside",
						"Isle of Wight" => "Isle of Wight",
						"Isles of Scilly" => "Isles of Scilly",
						"Kent" => "Kent",
						"Lancashire" => "Lancashire",
						"Leicestershire" => "Leicestershire",
						"Lincolnshire" => "Lincolnshire",
						"London" => "London",
						"Merseyside" => "Merseyside",
						"Middlesex" => "Middlesex",
						"Norfolk" => "Norfolk",
						"North Yorkshire" => "North Yorkshire",
						"North East Lincolnshire" => "North East Lincolnshire",
						"Northamptonshire" => "Northamptonshire",
						"Northumberland" => "Northumberland",
						"Nottinghamshire" => "Nottinghamshire",
						"Oxfordshire" => "Oxfordshire",
						"Rutland" => "Rutland",
						"Shropshire" => "Shropshire",
						"Somerset" => "Somerset",
						"South Yorkshire" => "South Yorkshire",
						"Staffordshire" => "Staffordshire",
						"Suffolk" => "Suffolk",
						"Surrey" => "Surrey",
						"Tyne and Wear" => "Tyne and Wear",
						"Warwickshire" => "Warwickshire",
						"West Midlands" => "West Midlands",
						"West Sussex" => "West Sussex",
						"West Yorkshire" => "West Yorkshire",
						"Wiltshire" => "Wiltshire",
						"Worcestershire" => "Worcestershire",
					),
					"Northern Ireland" => array(
						"Antrim" => "Antrim",
						"Armagh" => "Armagh",
						"Down" => "Down",
						"Fermanagh" => "Fermanagh",
						"Londonderry" => "Londonderry",
						"Tyrone" => "Tyrone",
					),
					"Scotland" => array(
						"Aberdeen City" => "Aberdeen City",
						"Aberdeenshire" => "Aberdeenshire",
						"Angus" => "Angus",
						"Argyll and Bute" => "Argyll and Bute",
						"Banffshire" => "Banffshire",
						"Borders" => "Borders",
						"Clackmannan" => "Clackmannan",
						"Dumfries and Galloway" => "Dumfries and Galloway",
						"East Ayrshire" => "East Ayrshire",
						"East Dunbartonshire" => "East Dunbartonshire",
						"East Lothian" => "East Lothian",
						"East Renfrewshire" => "East Renfrewshire",
						"Edinburgh City" => "Edinburgh City",
						"Falkirk" => "Falkirk",
						"Fife" => "Fife",
						"Glasgow" => "Glasgow",
						"Highland" => "Highland",
						"Inverclyde" => "Inverclyde",
						"Midlothian" => "Midlothian",
						"Moray" => "Moray",
						"North Ayrshire" => "North Ayrshire",
						"North Lanarkshire" => "North Lanarkshire",
						"Orkney" => "Orkney",
						"Perthshire and Kinross" => "Perthshire and Kinross",
						"Renfrewshire" => "Renfrewshire",
						"Roxburghshire" => "Roxburghshire",
						"Shetland" => "Shetland",
						"South Ayrshire" => "South Ayrshire",
						"South Lanarkshire" => "South Lanarkshire",
						"Stirling" => "Stirling",
						"West Dunbartonshire" => "West Dunbartonshire",
						"West Lothian" => "West Lothian",
						"Western Isles" => "Western Isles",
					),
					"Unitary Authorities of Wales" => array(
						"Blaenau Gwent" => "Blaenau Gwent",
						"Bridgend" => "Bridgend",
						"Caerphilly" => "Caerphilly",
						"Cardiff" => "Cardiff",
						"Carmarthenshire" => "Carmarthenshire",
						"Ceredigion" => "Ceredigion",
						"Conwy" => "Conwy",
						"Denbighshire" => "Denbighshire",
						"Flintshire" => "Flintshire",
						"Gwynedd" => "Gwynedd",
						"Isle of Anglesey" => "Isle of Anglesey",
						"Merthyr Tydfil" => "Merthyr Tydfil",
						"Monmouthshire" => "Monmouthshire",
						"Neath Port Talbot" => "Neath Port Talbot",
						"Newport" => "Newport",
						"Pembrokeshire" => "Pembrokeshire",
						"Powys" => "Powys",
						"Rhondda Cynon Taff" => "Rhondda Cynon Taff",
						"Swansea" => "Swansea",
						"Torfaen" => "Torfaen",
						"The Vale of Glamorgan" => "The Vale of Glamorgan",
						"Wrexham" => "Wrexham",
					),
					"UK Offshore Dependencies" => array(
						"Channel Islands" => "Channel Islands",
						"Isle of Man" => "Isle of Man"
					)
				);
				break;
			case 'AU':
				$states =  array(
					'ACT' => 'Australian Capital Territory',
					'NCW' => 'New South Wales',
					'NT' => 'Northern Territory',
					'QLD' => 'Queensland',
					'SA' => 'South Australia',
					'TAS' => 'Tasmania',
					'VIC' => 'Victoria',
					'WA' => 'Western Australia'
				);
				break;
			case 'CA':
				$states =  array(
					"Alberta" => "Alberta",
					"British Columbia" => "British Columbia",
					"Manitoba" => "Manitoba",
					"New Brunswick" => "New Brunswick",
					"Newfoundland" => "Newfoundland",
					"Nova Scotia" => "Nova Scotia",
					"Nunavut" => "Nunavut",
					"Northwest Territories" => "Northwest Territories",
					"Ontario" => "Ontario",
					"Prince Edward Island" => "Prince Edward Island",
					"Quebec" => "Quebec",
					"Saskatchewan" => "Saskatchewan",
					"Yukon" => "Yukon"
				);
		}
		return $states;
	}

	private function assignFields()
	{
		$formFields = $this->getFormFields();
		$this->getTemplateProcessor()->assign('formFields', $formFields);
	}

	private function assignIcons()
	{
		$payPalPro = SJB_PaymentGatewayManager::getObjectByID(self::GATEWAY_ID, false);
		$countrySID = $payPalPro->getPropertyValue('country');
		$country = SJB_CountriesManager::getCountryInfoBySID($countrySID);
		$creditCards = array();
		if ($country) {
			switch ($country['country_code']) {
				case 'US':
					$creditCards = array(
						"visa",
						"mastercard",
						"discovery",
						"amex"
					);
					break;
				case 'CA':
					$creditCards = array(
						"visa",
						"mastercard"
					);
					break;
				case 'UK':
					$creditCards = array(
						"visa",
						"mastercard",
						"maestro"
					);
					break;
			}
			$this->getTemplateProcessor()->assign('creditCards', $creditCards);
		}
	}

	private function assignCurrentUserCountry()
	{
		$user = SJB_UserManager::getCurrentUser();
		if ($user) {
			$locationValue = $user->getPropertyValue('Location');
			$country = SJB_Array::get($locationValue, 'Country');
			if ($country) {
				$countryInfo = SJB_CountriesManager::getCountryInfoBySID($country);
				$this->getTemplateProcessor()->assign('curUserCountryInfo', $countryInfo);
			}
		}
	}
	
	private function assignInvoiceInfo()
	{
		$invoiceInfo['invoiceNumber'] = $this->invoice->getSID();
		$invoiceInfo['description'] = $this->invoice->getProductNames();
		$invoiceInfo['totalPrice'] = $this->invoice->getPropertyValue('total');
		$invoiceInfo['currencyCode'] = SJB_Payment_PaypalProFillPaymentCard::getCurrencyCode();
		$this->getTemplateProcessor()->assign('invoiceInfo', $invoiceInfo);
	}
}
