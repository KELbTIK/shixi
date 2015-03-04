function htmlentities(value)
{
	if (value) {
		return jQuery('<span />').text(value).html();
	}
	
	return '';
}

function is_array(variable)
{
	return (variable instanceof Array);
}

function empty(variable)
{
	return (variable === "" || variable === 0 || variable === "0" || variable === null || variable === false || (is_array(variable) && variable.length === 0));
}

function in_array(value, array)
{
	for (var i = 0; i < array.length; i++ ) {
		if (value == array[i]) {
			return true;
		}
	}
	
	return false;
}

function array_key_exists(key, array)
{
	if (!array || (array.constructor !== Array && array.constructor !== Object)) {
		return false;
	}
	
	return array[key] !== undefined;
}
