<h1>[[Sub-user Profile]]</h1>
{include file='field_errors.tpl'}
{if $form_is_submitted && !$errors}
	<p class="message">[[You have successfully changed sub-user profile info!]]</p>
{/if}
<div id="sub-accounts">
	<form method="post" action="{$GLOBALS.site_url}/sub-accounts/edit/">
		<input type="hidden" name="action_name" value="edit" />
		<input type="hidden" name="user_id" value="{$user_info.sid}" />
		{foreach from=$form_fields item=form_field}
			<fieldset>
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id}</div>
			</fieldset>
		{/foreach}
		{if !$GLOBALS.current_user.subuser}
			<fieldset>
				<div class="inputName">[[Permissions]]:</div>
				<div class="inputReq">&nbsp;</div>
				<div class="inputField">
					<ul class="sub-user-permissions">
						<li><input type="checkbox" {if $acl->isAllowed('subuser_add_listings', $user_info.sid)} checked="checked"{/if} name="subuser_add_listings" value="allow" />[[Add new listings]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_listings', $user_info.sid)} checked="checked"{/if} name="subuser_manage_listings" value="allow" />[[Manage listings and applications of other sub users]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_subscription', $user_info.sid)} checked="checked"{/if} name="subuser_manage_subscription" value="allow" />[[View and update subscription]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_use_screening_questionnaires', $user_info.sid)} checked="checked"{/if} name="subuser_use_screening_questionnaires" value="allow" />[[Manage Screening questionnaires]]</li>
					</ul>
				</div>
			</fieldset>
		{/if}
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputReq">&nbsp;</div>
			<div class="inputField"><input type="submit" value="[[Save]]" class="button" /></div>
		</fieldset>
	</form>
</div>