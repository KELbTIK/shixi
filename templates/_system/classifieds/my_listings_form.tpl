{capture assign='trListingTypeName'}{if in_array($listingTypeID, array('Job', 'Resume'))}{$listingTypeName}s{else}{$listingTypeName} Listings{/if}{/capture}
<h1>{tr}My {$trListingTypeName}{/tr|escape:'html'}</h1>
{if $listingsInfo.listingsMax === 'unlimited'}
	<div class="alert alert-info">{tr}You can post unlimited number of {$trListingTypeName|strtolower}{/tr|escape:'html'}.</div>
{else}
	<div class="alert alert-info">{tr}You have $listingsInfo.listingsLeft {$trListingTypeName|strtolower} left to post out of $listingsInfo.listingsMax originally available{/tr|escape:'html'}.</div>
{/if}

<form method="post" action="" class="form-horizontal">
	<input type="hidden" name="action" value="search" />
    <div class="form-group has-feedback">
        <label class="inputName col-sm-3">[[ID]]</label>
        <div class="inputField col-sm-8">{search property="id"}</div>
    </div>
    <div class="form-group has-feedback">
        <label class="inputName col-sm-3">[[Activation Date]]</label>
        <div class="inputField col-sm-8">{search property="activation_date"}</div>
    </div>
    <div class="form-group has-feedback">
        <label class="inputName col-sm-3">[[Keywords]]</label>
        <div class="inputField col-sm-8">{search property="keywords"}</div>
    </div>
    <div class="form-group has-feedback">
        <div class="inputField col-sm-8 col-sm-offset-3"><input type="submit" value="[[Filter]]" class="button btn btn-default" /></div>
    </div>
</form>

<script type="text/javascript">
$( function () {ldelim}
	var dFormat = '{$GLOBALS.current_language_data.date_format}';
	{literal}
	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	$("#activation_date_notless, #activation_date_notmore").datepicker({dateFormat: dFormat, yearRange: '-99:+99'});
	{/literal}
{rdelim});
</script>