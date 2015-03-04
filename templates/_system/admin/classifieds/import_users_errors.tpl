{foreach from=$errors item=error key=key}
	{if $key == 'registrationDate'}
		<p class="error">
			{foreach from=$error item=value}
				[[{$value}]]
			{/foreach}
		</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}
