{foreach from=$errors item=error}
	<div class="error alert alert-danger">
		{if $error eq 'DENIED_SAVE_JOB_SEARCH'}
			[[You're not allowed to open this page]]
		{elseif $error eq 'DENIED_VIEW_SAVED_LISTING'}
			[[You're not allowed to open this page]]
		{/if}
	</div>
{/foreach}
