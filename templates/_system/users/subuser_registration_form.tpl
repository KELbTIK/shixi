<h1>[[Sub-user]] [[Registration]]</h1>
{include file='field_errors.tpl'}
<div class="alert alert-info">[[Fields marked with an asterisk (]]<span class="small text-danger">*</span>[[) are mandatory]]</div>
<div id="sub-accounts">
	<form method="post" action="{$GLOBALS.site_url}/sub-accounts/new/" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="action_name" value="new" />
		{foreach from=$form_fields item=form_field}
			<div class="form-group has-feedback">
                <label class="inputName control-label col-sm-3">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id}</div>
			</div>
		{/foreach}
		<div class="form-group has-feedback">
			<label class="inputName control-label col-sm-3">[[Permissions]]:</label>
			<div class="inputField col-sm-8">
				<ul class="sub-user-permissions">
					<li><input type="checkbox" {if $acl->isAllowed('subuser_add_listings', $user_info.sid)} checked="checked"{/if} name="subuser_add_listings" value="allow" />[[Add new listings]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_listings', $user_info.sid)} checked="checked"{/if} name="subuser_manage_listings" value="allow" />[[Manage listings and applications of other sub users]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_manage_subscription', $user_info.sid)} checked="checked"{/if} name="subuser_manage_subscription" value="allow" />[[View and update subscription]]</li>
					<li><input type="checkbox" {if $acl->isAllowed('subuser_use_screening_questionnaires', $user_info.sid)} checked="checked"{/if} name="subuser_use_screening_questionnaires" value="allow" />Manage Questionnaries</li>
				</ul>
			</div>
		</div>
		<div class="form-group has-feedback">
			<div class="inputField col-sm-8 col-sm-offset-3"><input type="hidden" name="user_group_id" value="{$user_group_info.id}" /> <input type="submit" class="btn btn-default" value="[[Register]]" /></div>
		</div>
	</form>
</div>