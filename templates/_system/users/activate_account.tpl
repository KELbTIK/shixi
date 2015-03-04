{if $errors && !$activated}
	{foreach from=$errors item="error_message" key="error"}
		<p class="error">
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
		</p>
	{/foreach}
{else}
	{if $activated}
		<p class="message">
			{if $approvalStatus == 'Pending'}
				[[Registration process is successfully completed and your account is waiting for approval by Administrator.]]
			{elseif $approvalStatus == 'Rejected'}
				[[Your account was rejected by Administrator and therefore can not be activated.]]
			{else}
				[[Your account was successfully activated. Thank you. {if $isLoggedIn == 0}Please <a href="{$GLOBALS.user_site_url|cat:"/login/"}">login</a>.{/if}]]
			{/if}
		</p>
	{else}
		<p class="error">[[Cannot activate account. Please contact administrator.]]</p>
	{/if}
{/if}