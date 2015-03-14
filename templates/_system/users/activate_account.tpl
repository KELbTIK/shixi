{if $errors && !$activated}
	{foreach from=$errors item="error_message" key="error"}
		<div class="error alert alert-danger">
			{if $error == "PARAMETERS_MISSED"}
				[[The key parameters are not specified]]
			{elseif $error == "USER_NOT_FOUND"}
				[[No such user found]]
			{elseif $error == "INVALID_ACTIVATION_KEY"}
				[[Wrong activation key is specified]]
			{elseif $error == "CANNOT_ACTIVATE"}
				[[Cannot activate account. Please contact administrator.]]
			{else}
				[[$error]] [[$error_message]]
			{/if}
		</div>
	{/foreach}
{else}
	{if $activated}
		<div class="message alert alert-info">
			{if $approvalStatus == 'Pending'}
				[[Registration process is successfully completed and your account is waiting for approval by Administrator.]]
			{elseif $approvalStatus == 'Rejected'}
				[[Your account was rejected by Administrator and therefore can not be activated.]]
			{else}
				[[Your account was successfully activated. Thank you. {if $isLoggedIn == 0}Please <a href="{$GLOBALS.user_site_url|cat:"/login/"}">login</a>.{/if}]]
			{/if}
		</div>
	{else}
		<div class="error alert alert-danger"> [[Cannot activate account. Please contact administrator.]]</div>
	{/if}
{/if}