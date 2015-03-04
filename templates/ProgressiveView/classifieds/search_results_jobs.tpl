{if $GLOBALS.user_page_uri == '/company/'}
	{assign var='refineSearch' value=false}
{/if}
<script type="text/javascript" language="JavaScript">
	$.ui.dialog.prototype.options.bgiframe = true;
	function submitForm(id) {
		lpp = document.getElementById("listings_per_page" + id);
		location.href = '?searchId={$searchId|escape:'url'}&action=search&page=1&listings_per_page=' + lpp.value + '&view={$view|escape:'url'}';
	}
	function sortBy(id) {
		lpp = document.getElementById("sort-by-select" + id);
		title = $("#sort-by-select").find(":selected").val();
		if(title == "Location_City") {
			location.href = '?searchId={$searchId}&action=search&sorting_field[0]=Location_City&sorting_field[1]=Location_State&sorting_order=ASC&page={$listing_search.current_page}';
		}
		else {
			location.href = '?searchId={$searchId}&action=search&sorting_field='+title+'&sorting_order=ASC&page={$listing_search.current_page}';
		}
	}

	function SaveAd(noteId, url){
		$.get(url, function(data){
			$("#"+noteId).html(data);
		});
	}
	
</script>

{if $ERRORS}
	{include file="error.tpl"}
{else}
	{if !empty($errors)}
		{foreach from=$errors key='error' item='error_msg'}
			{if $error == 'SIMPLY_HIRED_XML_READ_FAILED'}
				<p class="error">[[Failed to read XML from url]] - {$error_msg}</p>
			{/if}
		{/foreach}
	{/if}
	<div {if $refineSearch || $view == 'map'}class="results {if $GLOBALS.user_page_uri == "/company/" ||  $view == 'map' }company-fix{/if}"{else}class="noRefine"{/if}>

	<div id="topResults" {if $GLOBALS.user_page_uri == '/search-results-jobs/'}class="refine-fix"{/if}>
		<div class="headerBgBlock">

			{if $is_company_profile_page}
				{assign var=tmp_listing value=$listings|@current}
				{if $userInfo.CompanyName ne '' }
					{assign var="companyName" value=$userInfo.CompanyName}
					<!-- This page of company profile, with list of vacancy -->
					{include file="company_profile.tpl"}
					<div class="Results" id="compProfileInfo">{tr}Jobs by $companyName{/tr|escape:'html'}</div><span></span>
				{/if}
			{/if}

			<!-- TOP QUICK LINKS -->
			{if $userInfo.CompanyName eq '' }
				<div class="topResultsLinks">
					<ul>
						<li class="modifySearchIco"><a href="{$GLOBALS.site_url}/find-jobs/?searchId={$searchId}"> [[Modify search]]</a></li>
						{if $listing_type_id != ''}
							{if $acl->isAllowed('save_searches')}
								<li class="saveSearchIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/save-search/?searchId={$searchId}', 400, '[[Save this Search]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a></li>
							{elseif $acl->getPermissionParams('save_searches') == "message"}
								<li class="saveSearchIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 400, '[[Save this Search]]'); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a></li>
							{/if}
							{if $GLOBALS.current_user.logged_in}
								{if $acl->isAllowed('use_job_alerts')}
									<li class="saveSearchIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/save-search/?searchId={$searchId}&alert=1', 400, '[[Save Job Alert]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"
										   href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save Job Alert]]</a>
									</li>
								{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
									<li class="saveSearchIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 400, '[[Save Job Alert]]'); return false;"
										   href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save Job Alert]]</a>
									</li>
								{/if}
							{else} {* GUEST ALERTS >>> *}
								{if $acl->isAllowed('use_job_alerts')}
									<li class="saveSearchIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}', 400, '[[Save Job Alert]]', true, false); return false;"
										   href="{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}">[[Save Job Alert]]</a>
									</li>
								{elseif $acl->getPermissionParams('use_job_alerts') == "message"}
									<li class="saveSearchIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts', 400, '[[Save Job Alert]]', true, false); return false;"
										   href="{$GLOBALS.site_url}/access-denied/?permission=use_job_alerts">[[Save Job Alert]]</a>
									</li>
								{/if} {* <<< GUEST ALERTS *}
							{/if}
						{/if}
						{if $GLOBALS.current_user.logged_in}
							{if $acl->isAllowed('save_job')}
								<li class="savedIco">
									<a href="{$GLOBALS.site_url}/saved-jobs/">[[Saved Jobs]]</a>
								</li>
							{elseif $acl->getPermissionParams('save_job') == "message"}
								<li class="savedIco">
									<a href="{$GLOBALS.site_url}/saved-jobs/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 440, '[[Saved jobs]]'); return false;">[[Saved Jobs]]</a>
								</li>
							{/if}
						{else}
							<li class="savedIco">
								<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id=job', 440, '[[Saved jobs]]'); return false;" href="{$GLOBALS.site_url}/saved-listings/">[[Saved Jobs]]</a>
							</li>
						{/if}
						{if $GLOBALS.current_user.logged_in}
							{if $acl->isAllowed('save_searches')}
								<li class="savedIco">
									<a href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
								</li>
							{elseif $acl->getPermissionParams('save_searches') == "message"}
								<li class="savedIco">
									<a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 460, '[[Saved searches]]'); return false;">[[Saved searches]]</a>
								</li>
							{/if}
						{else}
							<li class="savedIco">
								<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-searches/', 460, '[[Saved searches]]'); return false;" href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
							</li>
						{/if}
					</ul>
				</div>
			{/if}
			<!-- END TOP QUICK LINKS -->
		</div>
	</div>
	<div class="results-paging">
		<div class="head">
			<h1>
				{assign var="listings_number" value=$listing_search.listings_number}
				{$listings_number} {if $listings_number == 1}[[Job]]{else}[[jobs]]{/if}
			</h1>
			{if $view_on_map}
				<div id="googleMap-links">
					<a href="{$GLOBALS.site_url}{if $url == '/search-results-jobs/'}{$url}?searchId={$searchId}&amp;action=search&amp;{else}{$url}?{/if}page=1&amp;view=list&amp;show_brief_or_detailed=brief" id="showBriefOrDetailed" {if $view == 'list' && $show_brief_or_detailed == 'brief'}onclick="return false;" class="listLink-active"{/if}>[[Show Brief]]</a> &nbsp;
					<a href="{$GLOBALS.site_url}{if $url == '/search-results-jobs/'}{$url}?searchId={$searchId}&amp;action=search&amp;{else}{$url}?{/if}page=1&amp;view=list&amp;show_brief_or_detailed=detailed" id="listView-icon" {if $view == 'list' && $show_brief_or_detailed == 'detailed'}onclick="return false;" class="listLink-active"{/if}>[[List View]]</a> &nbsp;
					<a href="{$GLOBALS.site_url}{if $url == '/search-results-jobs/'}{$url}?searchId={$searchId}&amp;action=search&amp;{else}{$url}?{/if}page=1&amp;view=map" id="mapView-icon" {if $view == 'map'}onclick="return false;" class="listLink-active"{/if}>[[Map View]]</a>
				</div>
				<div class="clr"></div>
			{/if}
		</div>

		<!-- TOP RESULTS - PER PAGE - PAGE NAVIGATION -->

		<div class="topNavBar">
			<div class="numberResults">
				{assign var="listings_number" value=$listing_search.listings_number}
				{if $search_criteria.ZipCode.value.location and $search_criteria.ZipCode.value.radius}
					{assign var="radius" value=$search_criteria.ZipCode.value.radius}
					{capture name=radius_search_unit}
						[[$GLOBALS.radius_search_unit]]
					{/capture}
					{assign var="radius_search_unit" value=$smarty.capture.radius_search_unit|escape:"html"}
					{assign var="location" value=$search_criteria.ZipCode.value.location|escape:"html"}
					[[within $radius $radius_search_unit of $location]]
				{/if}
				{if $view == 'list'}
					<div class="sorting"><span>[[Order by]]</span>
						<form id="sort-by" method="get" action="">
							<select id="sort-by-select" name="sort-by-select" onchange="sortBy(1); return false;">
								<option value="activation_date" {if $listing_search.sorting_field == 'activation_date'}selected="selected"{/if}>[[Date]]</option>
								<option value="Location_City" {if $listing_search.sorting_field.0 == 'Location_City'}selected="selected"{/if}>[[Location]]</option>
								<option value="CompanyName" {if $listing_search.sorting_field == 'CompanyName'}selected="selected"{/if}>[[Company Name]]</option>
								<option value="Title" {if $listing_search.sorting_field == 'Title'}selected="selected"{/if}>[[Title]]</option>
							</select>
						</form>
					</div>
				{/if}
			</div>
			<div class="numberPerPage select-box">
				<form id="listings_per_page_form" method="get" action="" class="tableSRNavPerPage">
					<select id="listings_per_page2" name="listings_per_page2" onchange="submitForm(2); return false;">
						<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
						<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
						<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
						<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
					</select>
				</form>
				<span>[[Number of jobs per page]]</span>
			</div>
			<div class="clr"></div>
		</div>
		<!-- END TOP RESULTS - PER PAGE - PAGE NAVIGATION -->
	</div>

	<!-- START REFINE SEARCH -->
	{if $refineSearch || $view == 'map'}
		<div id="refineResults-block">
			<div id="blockBg">
				<div id="blockTop"></div>
				<div id="blockInner">
					{if $view == 'map' && $listings}
						<table cellpadding="0" cellspacing="0" width="100%" id="refineResults">
							<thead>
							<tr>
								<th class="tableLeft"> </th>
								<th>[[Job Search Results]]:</th>
								<th class="tableRight"> </th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td colspan="3">
									<div id="googleMap-searchResults">
										{foreach from=$listings item=listing name=listings}
											{if empty($index)}{assign var=index value=0}{/if}
											<div class="mapListings-results {cycle values = 'evenrow,oddrow' advance=true}">
												<div {if $listing.latitude && $listing.longitude}class="listingsWithLocation" onmouseover="javascript: google.maps.event.trigger(markersArray[{$index}], 'click');" onmouseout="javascript: infoWindows[{$index}].close();"{else}class="listingsWithoutLocation" title="No Location"{/if}>
													<a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}" target="_blank"><span class="strong">{$listing.Title}</span></a><br />
													<span class="strong">Company:</span>
													<a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{$listing.user.CompanyName|escape:'html'}</a>
													<br />
													{locationFormat location=$listing.Location format="short"}
												</div>
											</div>
											{if $listing.latitude && $listing.longitude}{assign var=index value=$index+1}{/if}
										{/foreach}
									</div>
									<div class="clr"></div>
									<div id="googleMap-pagging">
										<span class="prevBtn">
											<img src="{image}prev_btn.png" alt=""/>
											{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}
										</span>
										<span class="navigationItems">
											{if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1&amp;view={$view}">1</a>{/if}
											{if $listing_search.current_page-3 > 1}...{/if}
											{if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}&amp;view={$view}">{$listing_search.current_page-2}</a>{/if}
											{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">{$listing_search.current_page-1}</a>{/if}
											<span class="strong">{$listing_search.current_page}</span>
											{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">{$listing_search.current_page+1}</a>{/if}
											{if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}&amp;view={$view}">{$listing_search.current_page+2}</a>{/if}
											{if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
											{if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}&amp;view={$view}">{$listing_search.pages_number}</a>{/if}
										</span>
										<span class="nextBtn">
											{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
											<img src="{image}next_btn.png" alt=""/>
										</span>
									</div>
								</td>
							</tr>
							</tbody>
						</table>
						<div class="clr"><br/></div>
					{/if}
					{if $refineSearch}
						<div id="ajaxRefineSearch">
							{include file="search_results_refine_block.tpl"}
						</div>
					{/if}
				</div>
			</div>
		</div>
	{/if}
	<!-- END REFINE SEARCH -->

	<!-- LISTINGS TABLE -->
	<div id="listingsResults">
		{if $listings}
			{if $view == 'map'}
				{include file='google_map_results.tpl'}
			{else}
				<table cellspacing="0">
					<tbody class="searchResultsJobs">
					<!-- Job Info Start -->
					{include file="search_results_jobs_listings.tpl"}
					<!-- END Job Info Start -->
					</tbody>
				</table>
				<div class="pageNavigation">
					<span class="prevBtn">
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">&#171;&nbsp;[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
					<span class="navigationItems">
						{if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1&amp;view={$view}">1</a>{/if}
						{if $listing_search.current_page-3 > 1}...{/if}
						{if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}&amp;view={$view}">{$listing_search.current_page-2}</a>{/if}
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">{$listing_search.current_page-1}</a>{/if}
						<span class="strong">{$listing_search.current_page}</span>
						{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">{$listing_search.current_page+1}</a>{/if}
						{if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}&amp;view={$view}">{$listing_search.current_page+2}</a>{/if}
						{if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
						{if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}&amp;view={$view}">{$listing_search.pages_number}</a>{/if}
					</span>
					<span class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]&nbsp;&#187;</a>{else}<a>[[Next]]</a>{/if}
					</span>
				</div>
			{/if}
		{else}
			<div id="noListingsFounds"><p class="information">[[There are no postings meeting the criteria you specified]]</p></div>
			<table cellspacing="0" id="listingsTableResults">
				<tbody>
				<!-- Job Info From Listings Providers Here -->
				<tr id="no_listings_found">
					<td colspan="6" style="display:none;"><p class="information">[[There are no postings meeting the criteria you specified]]</p></td>
				</tr>
				{if !$is_company_profile_page}
					<!-- preloader row here -->
					<tr id="ajax_preloader_listings_results">
						<td colspan="6" style="text-align:center;">&nbsp;<img src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" /></td>
					</tr>
				{/if}
				</tbody>
			</table>
		{/if}
	</div>
	<!-- END LISTINGS TABLE -->

	<!-- END BOTTOM RESULTS - PER PAGE - PAGE NAVIGATION -->
	</div>
{/if}

<script type="text/javascript" language="JavaScript">
	{if $keywordsHighlight}
		$(".searchResultsJobs").highlight({$keywordsHighlight});
	{/if}

	{if !$is_company_profile_page}
		function requestToListingsProviders(page, provider) {
			page = typeof page !== 'undefined' ? page : '{$listing_search.current_page}';
			provider = typeof provider !== 'undefined' ? provider : '';

			var preloaderSelector = '#ajax_preloader_listings_results';
			if (provider != '') {
				preloaderSelector =  '#' + provider + '_ajax_preloader_listings_results';
			}
			$(preloaderSelector).show();

			// request to listings providers
			var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
			var ajaxParams = {
				'action' : 'request_for_listings',
				'listing_type[equal]' : 'Job',
				'searchId' : '{$searchId}',
				'provider' : provider,
				'page' : page
			};

			$.get(ajaxUrl, ajaxParams, function(data) {
				if (data.length > 0) {
					$("#no_listings_found").hide();
					dataText = data.replace(/\s/g, '');
					if (dataText == '') {
						$("#listingsTableResults").hide();
					}

					{if !$listings}
						$(".topNavBar").hide();
						$(".topNavBarRight").hide();
						$(".topNavBarLeft").hide();
						$("#noListingsFounds").show();
					{/if}

				}
				$(preloaderSelector).hide();
				if (provider == 'indeed') {
					$('tr.indeedBlock').remove();
				}
				$('#listingsResults > table > tbody').append(data);
			});
		}
		function showRefineSearch() {
			var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
			var ajaxParams = {
				'action' : 'get_refine_search_block',
				'listing_type[equal]' : 'Job',
				'searchId' : '{$searchId}',
				'view' : '{$view}',
				'showRefineFields' : ({$listing_search.listings_number} > 0)
			};

			$.get(ajaxUrl, ajaxParams, function(data) {
				if (data.length > 0) {
					dataText = data.replace(/\s/g, '');
					$('#refineResults-block div#blockBg div#blockInner div#ajaxRefineSearch').html(data);
				}
			});
		}
		// make request to listings providers after page loads
		$(function() {
			requestToListingsProviders();
			showRefineSearch();
		});

	{/if}
	$("#sort-by-select, #listings_per_page2").selectbox();
</script>