<script type="text/javascript" language="JavaScript">
{literal}
function submitForm(id) {
	lpp = document.getElementById("companies_per_page" + id);
	location.href = '?{/literal}searchId={$searchId|escape:'url'}{literal}&action=search&page=1&companies_per_page=' + lpp.value;
}
</script>
{/literal}
<div class="SearchResultsCompany">
	{if $ERRORS}
		{include file="error.tpl"}
	{else}
		{if $tmp_listing.user.CompanyName eq '' }
			<h1>[[Company Search Results]]</h1>
		{/if}
	<!-- RESULTS / PER PAGE / NAVIGATION -->
	<div class="topNavBarLeft"></div>
	<div class="topNavBar">
		<div class="numberResults">[[Results:]] {$usersCount} {if $usersCount == 1}[[Company]]{else}[[Companies]]{/if}</div>
		<div class="numberPerPage">
			<form class="companies_per_page_form tableSRNavPerPage" method="get" action="">
				<input type="hidden" name="searchId" value="{$searchId}" />
				[[Number of companies per page]]:
				<select id="companies_per_page1" name="companies_per_page1" onchange="submitForm(1); return false;">
					<option value="10" {if $companies_per_page == 10}selected="selected"{/if}>10</option>
					<option value="20" {if $companies_per_page == 20}selected="selected"{/if}>20</option>
					<option value="50" {if $companies_per_page == 50}selected="selected"{/if}>50</option>
					<option value="100" {if $companies_per_page == 100}selected="selected"{/if}>100</option>
				</select>
			</form>
		</div>
		<div class="pageNavigation">
			<span class="prevBtn"><img src="{image}prev_btn.png" alt="[[Previous]]"/>
			{if $current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-1}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
			<span class="navigationItems">
				{if $current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1">1</a>{/if}
				{if $current_page-3 > 1}...{/if}
				{if $current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-2}">{$current_page-2}</a>{/if}
				{if $current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-1}">{$current_page-1}</a>{/if}
				<span class="strong">{$current_page}</span>
				{if $current_page+1 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+1}">{$current_page+1}</a>{/if}
				{if $current_page+2 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+2}">{$current_page+2}</a>{/if}
				{if $current_page+3 < $pages_number}...{/if}
				{if $current_page+3 < $pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$pages_number}">{$pages_number}</a>{/if}
			</span>
			<span class="nextBtn">{if $current_page+1 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+1}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
			<img src="{image}next_btn.png" alt="[[Next]]"/></span>
		</div>
	</div>
	<div class="topNavBarRight"></div>
	<div class="clr"><br/></div>
	<!-- END RESULTS / PER PAGE / NAVIGATION -->

	{if $found_users_sids}
	<table cellspacing="0">
		<thead>
			<tr>
				<th class="tableLeft"> </th>
				<th width="10%">&nbsp;</th>
				<th>
					<a href="?searchId={$searchId}&amp;sorting_field=CompanyName&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'CompanyName'}DESC{else}ASC{/if}">[[Company Name]]</a>
					{if $sorting_field == 'CompanyName'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th>
					<a href="?searchId={$searchId}&amp;sorting_field[0]=Location_City&amp;sorting_field[1]=Location_State&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field[0] == 'Location_City'}DESC{else}ASC{/if}">[[Location]]</a>
					{if $sorting_field.0 == 'Location_City'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th style="text-align: right !important;">
					<a href="?searchId={$searchId}&amp;sorting_field=number_of_jobs&amp;sorting_order={if $sorting_order == 'ASC' && $sorting_field == 'number_of_jobs'}DESC{else}ASC{/if}">[[No of jobs]]</a>
					{if $sorting_field == 'number_of_jobs'}{if $sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
				</th>
				<th class="tableRight"> </th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$found_users_sids item=user_sid name=users_block}
				<tr class="{cycle values = 'evenrow,oddrow' advance=true}">
					{display property='username' object_sid=$user_sid assign='username'}
					{display property='CompanyName' object_sid=$user_sid assign='companyNameAlias'}
					{display property='State.Code' object_sid=$user_sid parent=Location assign='State'}
					{display property='City' object_sid=$user_sid parent=Location assign='City'}
					<td class="compLogo" colspan="2">
						<div class="text-center">
							<a href="{$GLOBALS.site_url}/company/{$user_sid}/{$companyNameAlias|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{display property='Logo' object_sid=$user_sid}</a>
						</div>
					</td>
					<td>
						<span class="strong">
							<a href="{$GLOBALS.site_url}/company/{$user_sid}/{$companyNameAlias|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{display property='CompanyName' object_sid=$user_sid}</a>
						</span>
					</td>
					<td>{$City}{if $City && $State}, {/if}{$State}</td>
					<td align="right">{display property='countListings' object_sid=$user_sid}</td>
					<td></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	
	<!-- RESULTS / PER PAGE / NAVIGATION -->
	<div class="clr"><br/></div>
	<div class="topNavBarLeft"></div>
	<div class="topNavBar">
		<div class="numberResults">[[Results:]] {$usersCount} {if $usersCount == 1}[[Company]]{else}[[Companies]]{/if}</div>
		<div class="numberPerPage">
			<form class="companies_per_page_form tableSRNavPerPage" method="get" action="">
				<input type="hidden" name="searchId" value="{$searchId}" />
				[[Number of companies per page]]:
				<select id="companies_per_page2" name="companies_per_page2" onchange="submitForm(2); return false;">
				<option value="10" {if $companies_per_page == 10}selected="selected"{/if}>10</option>
				<option value="20" {if $companies_per_page == 20}selected="selected"{/if}>20</option>
				<option value="50" {if $companies_per_page == 50}selected="selected"{/if}>50</option>
				<option value="100" {if $companies_per_page == 100}selected="selected"{/if}>100</option>
			</select>
			</form>
		</div>
		<div class="pageNavigation">
			<span class="prevBtn"><img src="{image}prev_btn.png" alt="[[Previous]]"/>
		    {if $current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-1}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
			<span class="navigationItems">
				{if $current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1">1</a>{/if}
				{if $current_page-3 > 1}...{/if}
				{if $current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-2}">{$current_page-2}</a>{/if}
				{if $current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page-1}">{$current_page-1}</a>{/if}
				<span class="strong">{$current_page}</span>
				{if $current_page+1 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+1}">{$current_page+1}</a>{/if}
				{if $current_page+2 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+2}">{$current_page+2}</a>{/if}
				{if $current_page+3 < $pages_number}...{/if}
				{if $current_page+3 < $pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$pages_number}">{$pages_number}</a>{/if}
			</span>
			<span class="nextBtn">{if $current_page+1 <= $pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$current_page+1}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
			<img src="{image}next_btn.png" alt="[[Next]]"/></span>
		</div>
	</div>
	<div class="topNavBarRight"></div>
	<!-- END RESULTS / PER PAGE / NAVIGATION -->
	{/if}
	{/if}
</div>
