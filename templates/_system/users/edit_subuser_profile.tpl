<h1>[[Sub-user Profile]]</h1>
{include file='field_errors.tpl'}
{if $form_is_submitted && !$errors}
	<div class="message alert alert-success">[[You have successfully changed sub-user profile info!]]</div>
{/if}
<div id="sub-accounts">
	<form method="post" action="{$GLOBALS.site_url}/sub-accounts/edit/" class="form-horizontal">
		<input type="hidden" name="action_name" value="edit" />
		<input type="hidden" name="user_id" value="{$user_info.sid}" />
		{foreach from=$form_fields item=form_field}
			<div class="form-group has-feedback">
				<label class="inputName control-label col-sm-3">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id}</div>
			</div>
		{/foreach}
		{if !$GLOBALS.current_user.subuser}
            <div class="form-group has-feedback">
                <label class="inputName control-label col-sm-3">[[Permissions]]:</label>
				<div class="inputField col-sm-8">
					<ul class="sub-user-permissions">
						<li><input type="checkbox" {if $acl->isAllowed('subuser_add_listings', $user_info.sid)} checked="checked"{/if} name="subuser_add_listings" value="allow" />[[Add new listings]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_listings', $user_info.sid)} checked="checked"{/if} name="subuser_manage_listings" value="allow" />[[Manage listings and applications of other sub users]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_subscription', $user_info.sid)} checked="checked"{/if} name="subuser_manage_subscription" value="allow" />[[View and update subscription]]</li>
						<li><input type="checkbox" {if $acl->isAllowed('subuser_use_screening_questionnaires', $user_info.sid)} checked="checked"{/if} name="subuser_use_screening_questionnaires" value="allow" />[[Manage Screening questionnaires]]</li>
					</ul>
				</div>
			</div>
		{/if}
        <div class="form-group has-feedback">
			<div class="inputField col-sm-8 col-sm-offset-3"><input type="submit" value="[[Save]]" class="button btn btn-success" /></div>
		</div>
	</form>
</div>