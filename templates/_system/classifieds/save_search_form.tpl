<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>
{literal}
<script type="text/javascript">
	function saveSearchSubmit() {
		var options = {
				  target: "#messageBox",
				  url:  $("#saveSearchForm").attr("action")
				};
		$("#saveSearchForm").ajaxSubmit(options);
		return false;
	}
</script>
{/literal}
{if $is_alert}
	<form method="post" action='{$GLOBALS.site_url}/save-search/' id="saveSearchForm" onsubmit="return saveSearchSubmit()">
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="searchId" value="{$searchId}" />
		<fieldset>
			<div class="inputName">[[Alert Name]]</div>
			<div class="inputField"><input type="text" name="search_name" /></div>
		</fieldset>
		<fieldset>
			<div class="inputName">[[Email frequency]]</div>
			<div class="inputField">
				<select name="email_frequency">
					<option value="daily">[[Daily]]</option>
					<option value="weekly">[[Weekly]]</option>
					<option value="monthly">[[Monthly]]</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<div class="inputName"><input type="hidden" name="alert" value="1" /></div>
			<div class="inputButton"><input type="submit" value="[[Save]]" class="button" /></div>
		</fieldset>
	</form>
{else}
	<form method="post" action='{$GLOBALS.site_url}/save-search/' id="saveSearchForm" onsubmit="return saveSearchSubmit()">
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="searchId" value="{$searchId}" />
		<fieldset>
			<div class="inputName">[[Search Name]]</div>
			<div class="inputField"><input type="text" name="search_name" /></div>
		</fieldset>
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputButton"><input type="submit" value="[[Save]]" class="button" /></div>
		</fieldset>
	</form>
{/if}