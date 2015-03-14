{foreach from=$errors key=error item=errmess}

		{if $error eq 'NO_SUCH_USER'}
			<div class="error alert alert-danger">[[Login error]]</div>
		{elseif $error eq 'INVALID_PASSWORD'}
			<div class="error alert alert-danger">[[Login error]]</div>
		{elseif $error eq 'EMPTY_VALUE'}
			<div class="error alert alert-danger">[[Enter Security code]]</div>
		{elseif $error eq 'NOT_VALID'}
			<div class="error alert alert-danger">[[Security code is not valid]]</div>
		{elseif $error eq 'USER_NOT_ACTIVE'}
			<div class="error alert alert-danger">[[Your account is not active]]</div>
		{elseif $error eq 'USER_NOT_APPROVED'}
			<div class="error alert alert-danger">[[Your account is not approved]]</div>
		{elseif $error eq 'BANNED_USER'}
			<div class="error alert alert-danger">[[Your IP address was banned by site administrator. Please contact at $adminEmail for any questions.]]</div>
		{elseif $error eq 'SOCIAL_ACCESS_ERROR'}
			{if !empty($errmess)}
				{if $errmess == 'oAuth Problem: user_refused'}
					<div class="error alert alert-danger">[[Access is refused.]]</div>
				{else}
					{assign var="socialNetwork" value=$errmess}
					<div class="error alert alert-danger">[[The $socialNetwork Plugin is set up incorrectly. Please check this issue with the website Administrator.]]</div>
				{/if}
			{/if}
		{elseif $error eq 'NO_SUCH_USER_GROUP_IN_THE_SYSTEM'}
			<div class="error alert alert-danger">[[Registration form cannot be displayed. There is no such User Group in the system.]]</div>
		{else}
			<div class="error alert alert-danger">[[$error]] [[$errmess]]</div>
		{/if}

{/foreach}