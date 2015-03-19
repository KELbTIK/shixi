<input type="text" class="input-date displayDate form-control {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}" value="{tr type="date"}{if $mysql_date && !$complexField}{$mysql_date|escape:'html'}{else}{$value|escape:'html'}{/if}{/tr}" /><i class="fa fa-calendar-o  form-control-feedback"></i>

<script type="text/javascript">

var dFormat = '{$GLOBALS.current_language_data.date_format}';
{literal}
dFormat = dFormat.replace('%m', "mm");
dFormat = dFormat.replace('%d', "dd");
dFormat = dFormat.replace('%Y', "yy");

$(document).ready(function() {
    {/literal}$(".input-date").datepicker({literal}{ 
		dateFormat: dFormat,
		changeMonth: true,
		changeYear: true,
		minDate: new Date(1940, 1 - 1, 1),
		maxDate: '+10y',
		yearRange: '-99:+99'
	});
});
{/literal}
</script>