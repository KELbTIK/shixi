<h1>[[My Invoices]]</h1>
<form method="get" name="search_form" class="invoice-form" action="">
	<fieldset>
		<div class="inputName">[[Invoice]]&nbsp;&#35;</div>
		<div class="inputField">{search property="sid"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[From]]</div>
		<div class="inputField">{search property="date"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[Status]]</div>
		<div class="inputField">{search property="status"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">&nbsp;<input type="hidden" name="action" value="search" /></div>
		<div class="inputField"><input type="submit" value="Search" class="grayButton" /></div>
	</fieldset>
</form>
<script type="text/javascript">
	$(function () {ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	{literal}
		dFormat = dFormat.replace('%m', "mm");
		dFormat = dFormat.replace('%d', "dd");
		dFormat = dFormat.replace('%Y', "yy");

		$("#date_notless, #date_notmore").datepicker({dateFormat: dFormat, showOn: 'button', yearRange: '-99:+99', buttonImage: '{/literal}{$GLOBALS.site_url}/system/ext/jquery/calendar.gif{literal}', buttonImageOnly: true });

	{/literal}
	{rdelim});
</script>
