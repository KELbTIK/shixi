{if $listingTypeID == 'Job'}
	{if $acl->isAllowed('bulk_job_import')}
		<p><a href='{$GLOBALS.site_url}/job-import/'><span class="strong">[[Bulk job import from exl/csv file]]</span></a></p>
	{elseif $acl->getPermissionParams('bulk_job_import') == "message"}
		<p><a href='{$GLOBALS.site_url}/job-import/' onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=bulk_job_import', 300, '[[Bulk job import from exl/csv file]]'); return false;"><span class="strong">[[Bulk job import from exl/csv file]]</span></a></p>
	{/if}
{/if}

<p>{if !$listings|@count && empty($search_criteria)}[[You have no listings now]]{/if} [[Click]] <a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingTypeID}">[[here]]</a> [[to add a new listing.]]</p>

{if !$listings|@count}
	{if !empty($search_criteria)}
		<p class="error">[[No listings found.]]</p>
	{/if}
{else}

<script type="text/javascript" language="JavaScript">
	{literal}
	function submit()
	{
		form = document.getElementById("listings_per_page_form");
		form.submit();
	}
	{/literal}
</script>

<!-- PER PAGE / NAVIGATION -->
<br />
<div class="numberPerPage">
	<form id="listings_per_page_form" method="get" action="">
		[[Number of listings per page]]:
		<select name="listings_per_page" onchange="submit()">
			<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
			<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
			<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
			<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
		</select>
		<input type="hidden" name="restore" value="" />
		<input type="hidden" name="page" value="1" />
	</form>
	<br />
	<span class="prevBtn"><img src="{image}prev_btn.png" alt=""/>
	{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
	<span class="navigationItems">
		{if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1">1</a>{/if}
		{if $listing_search.current_page-3 > 1}...{/if}
		{if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}">{$listing_search.current_page-2}</a>{/if}
		{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}">{$listing_search.current_page-1}</a>{/if}
		<span class="strong">{$listing_search.current_page}</span>
		{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}">{$listing_search.current_page+1}</a>{/if}
		{if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}">{$listing_search.current_page+2}</a>{/if}
		{if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
		{if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}">{$listing_search.pages_number}</a>{/if}
	</span>
	<span class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
	<img src="{image}next_btn.png"  alt=""/></span>
	
	
</div>
<!-- END PER PAGE / NAVIGATION -->
<form method="post" action="">
	<div class="actions-with-selected">
		[[Actions with Selected]]:
		<input type="submit" name="action_activate" value="[[Activate]]" class="button" />
		<input type="submit" name="action_deactivate" value="[[Deactivate]]" class="button" />
		<input type="submit" name="action_delete" value="[[Delete]]" class="button" onclick="return confirm('[[Are you sure?]]')" />
	</div>

	<div class="clr"><br/></div>
	<div class="results">
	<table cellspacing="0" id="my-listings-table">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th width="1"><input type="checkbox" id="all_checkboxes_control" /></th>
				<th width="26%">
					[[Sort by]]: <a href="?restore=1&amp;sorting_field=Title&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'Title'}DESC{else}ASC{/if}">[[Title]]</a>
					{if $sorting_field == 'Title'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="[[Up]]" />{else}<img src="{image}b_down_arrow.png" alt="[[Down]]" />{/if}{/if}
				</th>
				<th width="14%">
					{if $hasSubusersWithListings}
						<a href="?restore=1&amp;sorting_field=subuser_sid&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'subuser_sid'}DESC{else}ASC{/if}">[[Listing Owner]]</a>
						{if $sorting_field == 'subuser_sid'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="[[Up]]" />{else}<img src="{image}b_down_arrow.png" alt="[[Down]]" />{/if}{/if}
					{/if}
				</th>
				<th width="10%">
					<a href="?restore=1&amp;sorting_field=id&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'id'}DESC{else}ASC{/if}">{if $GLOBALS.current_user.group.id == 'Employer'}[[Job ID]]{else}[[Resume ID]]{/if}</a>
					{if $sorting_field == 'id'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="[[Up]]" />{else}<img src="{image}b_down_arrow.png" alt="[[Down]]" />{/if}{/if}
				</th>
				<th width="10%">
					{if $property.activation_date.is_sortable}
						<a href="?restore=1&amp;sorting_field=activation_date&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'activation_date'}DESC{else}ASC{/if}">[[Posted]]</a>
						{if $sorting_field == 'activation_date'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
					{else}
						[[Posted]]
					{/if}
				</th>
				<th width="15%">
					{if $property.expiration_date.is_sortable}
						<a href="?restore=1&amp;sorting_field=expiration_date&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'expiration_date'}DESC{else}ASC{/if}">[[Expiration Date]]</a>
						{if $sorting_field == 'expiration_date'}
							{if $sorting_order == 'ASC'}
								<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />
							{/if}
						{/if}
					{else}
						[[Expiration Date]]
					{/if}
				</th>
				<th width="7%">
					<a href="?restore=1&amp;sorting_field=active&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'active'}DESC{else}ASC{/if}">[[Active]]</a>
					{if $sorting_field == 'active'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th width="10%">
					<a href="?restore=1&amp;sorting_field=views&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'views'}DESC{else}ASC{/if}">[[Number of Views]]</a>
					{if $sorting_field == 'views'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th width="12%">
					{if $GLOBALS.current_user.group.id == 'Employer'}
						<a href="?restore=1&amp;sorting_field=applications&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'applications'}DESC{else}ASC{/if}">[[Applications]]</a>
						{if $sorting_field == 'applications'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
					{/if}
				</th>
				<th width="10%">
					{if $waitApprove == 1}
						<a href="?restore=1&amp;sorting_field=status&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'status'}DESC{else}ASC{/if}">[[Approval Status]]</a>
						{if $sorting_field == 'status'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
					{/if}
				</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$listings item=listing name=listings_block}
			{if $listing.type.id eq 'Job'}
				{assign var='link' value='my-job-details'}
			{elseif $listing.type.id eq 'Resume'}
				{assign var='link' value='my-resume-details'}
			{else}
				{assign var='link' value='my-'|cat:{$listing.type.id|lower}|cat:'-details'}
			{/if}
			<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow' advance=false}"{/if}>
				<td> </td>
				<td class="noTdPad"><input type="checkbox" name="listings[{$listing.id}]" value="1" id="checkbox_{$smarty.foreach.listings_block.iteration}" /></td>
				<td><a href="{$GLOBALS.site_url}/{$link}/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html"><span class="strong">{$listing.Title} {if $listing.anonymous == 1}(anonymous){/if}</span></a></td>
				<td>
					{if $hasSubusersWithListings}
						{if $listing.subuser}<span class="longtext-20">{$listing.subuser.username}</span>{else}<span class="longtext-20">{$listing.user.username}</span>{/if}
					{/if}
				</td>
				<td>#&nbsp;{$listing.id}</td>
				<td>[[$listing.activation_date]]</td>
				<td>
					{capture assign="expDate"}[[$listing.expiration_date]]{/capture}
					{if !empty($expDate)}{$expDate}{else}[[Never Expire]]{/if}
				</td>
				<td>&nbsp;&nbsp;{if $listing.active}Yes{else}No{/if}</td>
				<td>{$listing.views}</td>
				<td>
					{if $GLOBALS.current_user.group.id == 'Employer'}
						{if !$apps[$listing.id]}-{else}<a href="{$GLOBALS.site_url}/system/applications/view/?appJobId={$listing.id}">{$apps[$listing.id]|default:"-"}</a>{/if}
					{/if}
				</td>
				<td>
					{if $waitApprove == 1}
						{if $listing.reject_reason != '' && $listing.approveStatus != 'approved'}
							<a title="Reject reason: {$listing.reject_reason}">[[$listing.approveStatus]]</a> | <a href="?action_sendToApprove=1&amp;listings[{$listing.id}]=1">Submit for approval</a>
						{else}
							[[$listing.approveStatus]]
						{/if}
					{/if}
				</td>
				<td> </td>
			</tr>
			<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow'}"{/if}>
				<td> </td>
				<td> </td>
				<td colspan="9">
					<ul>
						<li>&nbsp;<a href="{$GLOBALS.site_url}/{$link}/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html">[[View details]]</a> |</li>
						<li>&nbsp;<a href="{$GLOBALS.site_url}/edit-{$listing.type.id|lower}/?listing_id={$listing.id}">[[Edit]]</a> |</li>
						{if $listing.type.id == 'Job'}<li>&nbsp;<a href="{$GLOBALS.site_url}/clone-job/?listing_id={$listing.id}">[[Clone Job]]</a> |</li>{/if}
						<li>&nbsp;
							{if $listing.active}
								<a href="?action_deactivate=1&amp;listings[{$listing.id}]=1">[[Deactivate]]</a> |
							{else}
								{if $listing.complete == 1}<a href="{$GLOBALS.site_url}/pay-for-listing/?listing_id={$listing.id}">[[Activate]]</a> |{/if}
							{/if}
						</li>
						<li>&nbsp;<a href="?action_delete=1&amp;listings[{$listing.id}]=1" onclick="return confirm('[[Are you sure?]]')">[[Delete]]</a></li>
						{if $listing.priceForUpgradeToFeatured && !$listing.featured}
							<li>&nbsp; | <a href="{$GLOBALS.site_url}/make-featured/?listing_id={$listing.id}">[[Upgrade to Featured]]</a></li>
						{/if}
						{if $listing.priceForUpgradeToPriority && !$listing.priority}
							<li>&nbsp; | <a href="{$GLOBALS.site_url}/make-priority/?listing_id={$listing.id}">[[Upgrade to Priority]]</a></li>
						{/if}
					</ul>
				</td>
				<td> </td>
			</tr>
			<tr>
				<td colspan="12" class="separateListing"> </td>
			</tr>
			{/foreach}
	</table>
	</div>
	<div class="clr"><br/></div>
	<input type="submit" name="action_activate" value="[[Activate]]" class="button" />
	<input type="submit" name="action_deactivate" value="[[Deactivate]]" class="button" />
	<input type="submit" name="action_delete" value="[[Delete]]" class="button" onclick="return confirm('[[Are you sure?]]')" />

	<div class="pageNavigation">
		<span class="prevBtn">
		{if $listing_search.current_page-1 > 0}
			<a href="?restore=1&amp;page={$listing_search.current_page-1}">
				<img src="{image}prev_btn.png" alt="[[Previous]]" border="0"/>
				[[Previous]]
			</a>
		{else}
			<img src="{image}prev_btn.png" alt="[[Previous]]"  border="0" /><a>[[Previous]]</a>{/if}
		</span>
		<span class="navigationItems">
			{if $listing_search.current_page-3 > 0}<a href="?restore=1&amp;page=1">1</a>{/if}
			{if $listing_search.current_page-3 > 1}...{/if}
			{if $listing_search.current_page-2 > 0}<a href="?restore=1&amp;page={$listing_search.current_page-2}">{$listing_search.current_page-2}</a>{/if}
			{if $listing_search.current_page-1 > 0}<a href="?restore=1&amp;page={$listing_search.current_page-1}">{$listing_search.current_page-1}</a>{/if}
			<span class="strong">{$listing_search.current_page}</span>
			{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?restore=1&amp;page={$listing_search.current_page+1}">{$listing_search.current_page+1}</a>{/if}
			{if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?restore=1&amp;page={$listing_search.current_page+2}">{$listing_search.current_page+2}</a>{/if}
			{if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
			{if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?restore=1&amp;page={$listing_search.pages_number}">{$listing_search.pages_number}</a>{/if}
		</span>
		<span class="nextBtn">
			{if $listing_search.current_page+1 <= $listing_search.pages_number}
				<a href="?restore=1&amp;page={$listing_search.current_page+1}" >[[Next]]</a> <img src="{image}next_btn.png" alt="[[Next]]"  border="0"/>
			{else}
				<a>[[Next]]</a> <img src="{image}next_btn.png" alt="[[Next]]" border="0"/>
			{/if}
		</span>
	</div>
</form>

<script type="text/javascript" >
var total={$smarty.foreach.listings_block.total};
{literal}
function set_checkbox(param) {
	for (i = 1; i <= total; i++) {
		if (checkbox = document.getElementById('checkbox_' + i))
			checkbox.checked = param;
	}
}
$("#all_checkboxes_control").click(function() {
	set_checkbox(this.checked);
});
{/literal}
</script>
{/if}