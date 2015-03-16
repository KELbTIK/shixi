<script  type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>

{literal}
<script type="text/javascript">
    function saveSearchSubmit() {
        var saveSearchForm = $("#saveSearchForm");
        var options = {
            target: "#messageBox",
            url:  saveSearchForm.attr("action")
        };
        saveSearchForm.ajaxSubmit(options);
        return false;
    }
</script>
{/literal}

{if !empty($errors)}
	{include file="../users/field_errors.tpl"}
{/if}

<form action="{$GLOBALS.site_url}/guest-alerts/create/" method="post" id="saveSearchForm" onsubmit="return saveSearchSubmit()" class="form-horizontal">
    <input type="hidden" name="searchId" value="{$searchId}" />
    <input type="hidden" name="action" value="save" />
	{foreach from=$form_fields item="formField"}
		<div class="form-group has-feedback">
			<label class="inputName control-label col-sm-3">[[{$formField.caption}]]</label>
			<div class="inputField col-sm-8">{input property=$formField.id}</div>
		</div>
	{/foreach}
	<div class="form-group has-feedback">
		<div class="inputButton col-sm-8 col-sm-offset-3"><input type="submit" name="save" value="[[Save]]" class="btn btn-success" /></div>
	</div>
</form>