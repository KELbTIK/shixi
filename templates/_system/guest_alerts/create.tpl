<script language="JavaScript" type="text/javascript" src="{$GLOBALS.site_url}/system/ext/jquery/jquery.form.js"></script>

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

<form action="{$GLOBALS.site_url}/guest-alerts/create/" method="post" id="saveSearchForm" onsubmit="return saveSearchSubmit()">
    <input type="hidden" name="searchId" value="{$searchId}" />
    <input type="hidden" name="action" value="save" />
	{foreach from=$form_fields item="formField"}
		<fieldset>
			<div class="inputName">[[{$formField.caption}]]</div>
			<div class="inputField">{input property=$formField.id}</div>
		</fieldset>
	{/foreach}
	<fieldset>
		<div class="inputName">&nbsp;</div>
		<div class="inputButton"><input type="submit" name="save" value="[[Save]]" class="button" /></div>
	</fieldset>
</form>