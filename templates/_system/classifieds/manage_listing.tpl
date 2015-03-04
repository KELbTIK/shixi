<h1>[[Manage Listing]]</h1>
{if $waitApprove == 1}<p>[[Your {$listing.type.id|strtolower} posting is successfully created and waiting for approval.]]</p>{/if}
{if $errors == null}
{if $listing.type.id eq "Job"}
	{assign var='link' value='my-job-details'}
{elseif $listing.type.id eq 'Resume'}
	{assign var='link' value='my-resume-details'}
{else}
	{assign var='link' value='my-'|cat:{$listing.type.id|lower}|cat:'-details'}
{/if}
	<p><a href="{$GLOBALS.site_url}/{$link}/{$listing.id}/"> [[Preview Listing]]</a></p>
	<p><a href="{$GLOBALS.site_url}/edit-{$listing.type.id|lower}/?listing_id={$listing.id}"> [[Edit Listing]]</a></p>
	{if $listing.priceForUpgradeToFeatured && !$listing.featured}
		<p><a href="{$GLOBALS.site_url}/make-featured/?listing_id={$listing.id}"> [[Upgrade to Featured]] </a></p>
	{/if}
	{if $listing.priceForUpgradeToPriority && !$listing.priority}
		<p><a href="{$GLOBALS.site_url}/make-priority/?listing_id={$listing.id}"> [[Upgrade to Priority]] </a></p>
	{/if}
	{if $listing.type.id eq "Job"}
		<p><a href="{$GLOBALS.site_url}/clone-job/?listing_id={$listing.id}">[[Clone Job]]</a></p>
		{module name="social_media" function="social_posting" listing_id=$listing.id}
	{/if}
{else}
	{foreach from=$errors key=error item=error_message}
		{if $error == 'PARAMETERS_MISSED'}
			<p class="error">[[The key parameters are not specified]]</p>
		{elseif $error == 'WRONG_PARAMETERS_SPECIFIED'}
			<p class="error">[[Wrong parameters are specified]]</p>
		{elseif $error == 'NOT_OWNER'}
			<p class="error">[[You are not owner of this listing]]</p>
		{elseif $error == 'NOT_LOGGED_IN'}
			{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"}
			<p class="error">[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register]]</a></p>
			<br/><br/>
			{module name="users" function="login"}
		{/if}
	{/foreach}
{/if}