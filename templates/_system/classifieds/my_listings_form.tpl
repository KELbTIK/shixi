{capture assign='trListingTypeName'}{if in_array($listingTypeID, array('Job', 'Resume'))}{$listingTypeName}s{else}{$listingTypeName} Listings{/if}{/capture}
<h1>{tr}My {$trListingTypeName}{/tr|escape:'html'}</h1>
{if $listingsInfo.listingsMax === 'unlimited'}
	<p style="margin-top: 4px;">{tr}You can post unlimited number of {$trListingTypeName|strtolower}{/tr|escape:'html'}.</p>
{else}
	<p style="margin-top: 4px;">{tr}You have $listingsInfo.listingsLeft {$trListingTypeName|strtolower} left to post out of $listingsInfo.listingsMax originally available{/tr|escape:'html'}.</p>
{/if}

<form method="post" action="" >
	<input type="hidden" name="action" value="search" />
	<fieldset>
		<div class="inputName">[[ID]]</div>
		<div class="inputField">{search property="id"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[Activation Date]]</div>
		<div class="inputField">{search property="activation_date"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">[[Keywords]]</div>
		<div class="inputField">{search property="keywords"}</div>
	</fieldset>
	<fieldset>
		<div class="inputName">&nbsp;</div>
		<div class="inputField"><input type="submit" value="[[Filter]]" class="button" /></div>
	</fieldset>
</form>

<script type="text/javascript">
$( function () {ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	{literal}
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	$("#activation_date_notless, #activation_date_notmore").datepicker({dateFormat: dFormat, showOn: 'button', yearRange: '-99:+99', buttonImage: '{/literal}{$GLOBALS.site_url}/system/ext/jquery/calendar.gif{literal}', buttonImageOnly: true });
	{/literal}
{rdelim});
</script>