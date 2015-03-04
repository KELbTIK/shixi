var langSettings = {
	thousands_separator : '.',
	decimal_separator : ',',
	decimals : 2,
	currencySignLocation : 0,
	currencySign: '$',
	showCurrencySign: 1,
	rightToLeft: 0
};

function formatNumber(value)
{
	value = value.toString();
	var strAmount = value.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1' + langSettings.thousands_separator);
	strAmount = strAmount.replace(/\.(\d+)$/g, langSettings.decimal_separator + '$1');
	if (langSettings.showCurrencySign) {
		if (langSettings.currencySignLocation == 0) {
			if (langSettings.rightToLeft) {
				return strAmount + langSettings.currencySign;
			}
			return langSettings.currencySign + strAmount;
		}

		if (langSettings.rightToLeft) {
			return langSettings.currencySign + ' ' + strAmount;
		}
		return strAmount + ' ' + langSettings.currencySign;
	}
	return strAmount;
}

function unformatNumber(value)
{
	value = value.toString();
	if (langSettings.thousands_separator) {
		value = value.replace(new RegExp('\\' + langSettings.thousands_separator, 'g'), '');
	}
	if (langSettings.decimal_separator) {
		value = value.replace(new RegExp('\\' + langSettings.decimal_separator, 'g'), '.');
	}
	return parseFloat(value);
}

function roundNumber(value)
{
	var power = Math.pow(10, langSettings.decimals);
	return (Math.round(parseFloat(value) * power) / power).toFixed(langSettings.decimals);
}

function calcTaxAmount(sub_total, tax_rate, price_include_tax)
{
	var tax_amount = 0;
	if (price_include_tax) {
		tax_amount = sub_total - (sub_total / (tax_rate / 100 + 1));
	} else {
		tax_amount = sub_total * tax_rate / 100;
	}
	return roundNumber(tax_amount);
}

