{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; [[Add a New User Group]]{/breadcrumbs}
{assign var="switchColumns" value=false}
<h1><img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon" />[[Add User Group]]</h1>
{include file="field_errors.tpl"}

<fieldset>
	<legend>[[Add a New User Group]]</legend>
	<form id="editUserForm" method="post" onsubmit="disableSubmitButton('submitAdd');">
		<input type="hidden" name="action" value="add">
		{include file="user_group_form_fields.tpl"}
		<div class="clr"><br/></div>
		<div class="floatRight"><input type="submit" value="[[Add]]" class="grayButton" id="submitAdd" /></div>
	</form>
</fieldset>