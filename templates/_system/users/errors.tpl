{foreach from=$errors key=error item=errmess}
	<p class="error">
		{if $error eq 'NO_SUCH_USER'}
			[[Login error]]
		{elseif $error eq 'INVALID_PASSWORD'}
			[[Login error]]
		{elseif $error eq 'EMPTY_VALUE'}
			[[Enter Security code]]
		{elseif $error eq 'NOT_VALID'}
			[[Security code is not valid]]
		{elseif $error eq 'USER_NOT_ACTIVE'}
			[[Your account is not active]]
		{elseif $error eq 'USER_NOT_APPROVED'}
			[[Your account is not approved]]
		{elseif $error eq 'BANNED_USER'}
			[[Your IP address was banned by site administrator. Please contact at $adminEmail for any questions.]]
		{elseif $error eq 'SOCIAL_ACCESS_ERROR'}
			{if !empty($errmess)}
				{if $errmess == 'oAuth Problem: user_refused'}
					[[Access is refused.]]
				{else}
					{assign var="socialNetwork" value=$errmess}
					[[The $socialNetwork Plugin is set up incorrectly. Please check this issue with the website Administrator.]]
				{/if}
			{/if}
		{elseif $error eq 'NO_SUCH_USER_GROUP_IN_THE_SYSTEM'}
			[[Registration form cannot be displayed. There is no such User Group in the system.]]
		{else}
			[[$error]] [[$errmess]]
		{/if}
	</p>
{/foreach}