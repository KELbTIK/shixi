<h1>[[Sub-user]] [[Registration]]</h1>
{include file='field_errors.tpl'}
<p>[[Fields marked with an asterisk (]]<font color="red">*</font>[[) are mandatory]]</p>
<div id="sub-accounts">
	<form method="post" action="{$GLOBALS.site_url}/sub-accounts/new/" enctype="multipart/form-data" >
		<input type="hidden" name="action_name" value="new" />
		{foreach from=$form_fields item=form_field}
			<fieldset>
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id}</div>
			</fieldset>
		{/foreach}
		<fieldset>
			<div class="inputName">[[Permissions]]:</div>
			<div class="inputReq">&nbsp;</div>
			<div class="inputField">
				<ul class="sub-user-permissions">
					<li><input type="checkbox" {if $acl->isAllowed('subuser_add_listings', $user_info.sid)} checked="checked"{/if} name="subuser_add_listings" value="allow" />[[Add new listings]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_listings', $user_info.sid)} checked="checked"{/if} name="subuser_manage_listings" value="allow" />[[Manage listings and applications of other sub users]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_subscription', $user_info.sid)} checked="checked"{/if} name="subuser_manage_subscription" value="allow" />[[View and update subscription]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_use_screening_questionnaires', $user_info.sid)} checked="checked"{/if} name="subuser_use_screening_questionnaires" value="allow" />Manage Questionnaries</li>
				</ul>
			</div>
		</fieldset>
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputReq">&nbsp;</div>
			<div class="inputField"><input type="hidden" name="user_group_id" value="{$user_group_info.id}" /> <input type="submit" value="[[Register]]" /></div>
		</fieldset>
	</form>
</div>