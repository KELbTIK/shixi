{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingsType.link}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; [[Edit Listing]]
{/breadcrumbs}
<h1> <img src="{image}/icons/linedpaperminus32.png" border="0" alt="" class="titleicon"/>[[Edit Listing]]</h1>
{foreach from=$errors item=message key=error}
	{if $error eq 'PARAMETERS_MISSED'}
		<p class="error">[[The key parameters are not specified]]</p>
	{elseif $error eq 'WRONG_PARAMETERS_SPECIFIED'}
		<p class="error">[[Wrong parameters specified]]</p>
	{/if}
{foreachelse}
	<br />
	<p class="message">[[File deleted successfully]]</p>
	<p>[[Please wait... You will be redirected to Edit Listing in 5 seconds]].</p>
	<p>[[Or you can Click]] <a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing_id}">[[Here]]</a></p>

		<script type="text/javascript">
			redirectTime = "5000";
			redirectURL = "{$GLOBALS.site_url}/edit-listing/?listing_id={$listing_id}";
			//redirecting back to Edit Listing
			setTimeout("location.href = redirectURL;", redirectTime);
		</script>
{/foreach}