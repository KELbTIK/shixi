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
		<div class="form-group has-feedback">
			<div class="inputName">[[Alert Name]]</div>
			<div class="inputField"><input class="form-control" type="text" name="search_name" /></div>
		</div>
		<div class="form-group has-feedback">
			<div class="inputName">[[Email frequency]]</div>
			<div class="inputField">
				<select class="form-control" name="email_frequency">
					<option value="daily">[[Daily]]</option>
					<option value="weekly">[[Weekly]]</option>
					<option value="monthly">[[Monthly]]</option>
				</select>
			</div>
		</div>
		<div class="form-group has-feedback">
			<div class="inputName"><input type="hidden" name="alert" value="1" /></div>
			<div class="inputButton"><input type="submit" value="[[Save]]" class="btn btn-success" /></div>
		</div>
	</form>
{else}
	<form method="post" action='{$GLOBALS.site_url}/save-search/' id="saveSearchForm" onsubmit="return saveSearchSubmit()">
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="searchId" value="{$searchId}" />
		<div class="form-group has-feedback">
			<div class="inputName">[[Search Name]]</div>
			<div class="inputField"><input class="form-control" type="text" name="search_name" /></div>
		</div>
		<div class="form-group has-feedback">
			<div class="inputName">&nbsp;</div>
			<div class="inputButton"><input type="submit" value="[[Save]]" class="btn btn-success" /></div>
		</div>
	</form>
{/if}