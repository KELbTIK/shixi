{foreach from=$errors item=error}
	{if $error eq 'DENIED_SAVE_JOB_SEARCH'}
		<p class="error">
		[[You're not allowed to open this page]]
		</p>
	{elseif $error eq 'DENIED_VIEW_SAVED_LISTING'}
		<p class="error">
		[[You're not allowed to open this page]]
		</p>
	{/if}
{/foreach}
