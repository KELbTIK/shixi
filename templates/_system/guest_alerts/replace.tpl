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

<h1>[[Replace the Email Alert]]</h1>
<form action="{$GLOBALS.site_url}/guest-alerts/replace/" method="post" id="saveSearchForm" onsubmit="return saveSearchSubmit()" class="form-horizontal">
    <input type="hidden" name="searchId" value="{$searchId}" />
	{foreach from=$form_fields item="formField"}
		<div style="display:none;">{input property=$formField.id}</div>
	{/foreach}
        <div class="replace-email">
        <div class="error alert alert-warning">
            [[We're already sending an email alert to this address.]]<br/>
                [[If you'd like to replace the email alert, click the replace button, or register to save more email alerts.]]
        </div>
        <div class="inputButton"><input type="button" name="register" value="[[Register]]" class="btn btn-success" onclick="location.href='{$GLOBALS.site_url}/registration/'"/></div>
        <div class="inputButton"><input type="submit" name="replace" value="[[Replace]]" class="btn btn-primary" /></div>
        <div class="clearfix"></div>
    </div>
</form>



