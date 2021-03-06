{if $GLOBALS.user_page_uri == '/company/'}
	{assign var='refineSearch' value=false}
{/if}
<div id="no-padding">
	<script type="text/javascript" language="JavaScript">
	
	$.ui.dialog.prototype.options.bgiframe = true;
	function submitForm(id) {
		lpp = document.getElementById("listings_per_page" + id);
		location.href = '?searchId={$searchId|escape:'url'}&action=search&page=1&listings_per_page=' + lpp.value + '&view={$view|escape:'url'}';
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
		<div {if $refineSearch || $view == 'map'}class="results"{else}class="noRefine"{/if}>

			<div id="topResults">
				<div class="headerBgBlock">

					{if $is_company_profile_page}
						{assign var=tmp_listing value=$listings|@current}
						{if $userInfo.CompanyName ne '' }
							{assign var="companyName" value=$userInfo.CompanyName}
							<!-- This page of company profile, with list of vacancy -->
							{include file="company_profile.tpl"}
							<div class="Results" id="compProfileInfo">{tr}Jobs by $companyName{/tr|escape:'html'}</div><span></span>
						{/if}
					{else}
						<div class="Results">[[Job Search Results]]</div><span>&nbsp;</span>
					{/if}

					<!-- TOP QUICK LINKS -->
					{if $userInfo.CompanyName eq '' }
						<div class="topResultsLinks">
							<ul>
								<li class="modifySearchIco"><a href="{$GLOBALS.site_url}/find-jobs/?searchId={$searchId}"> [[Modify search]]</a></li>
								{if $listing_type_id != ''}
									{if $acl->isAllowed('save_searches')}
										<li class="saveSearchIco">
											<a onclick="popUpWindow('{$GLOBALS.site_url}/save-search/?searchId={$searchId}', 400, '[[Save this Search]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a>
										</li>
									{elseif $acl->getPermissionParams('save_searches') == "message"}
										<li class="saveSearchIco">
											<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 400, '[[Save this Search]]'); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a>
										</li>
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
										{/if}
									{/if} {* <<< GUEST ALERTS *}
								{/if}
								{if $GLOBALS.current_user.logged_in}
									{if $acl->isAllowed('save_job')}
										<li class="savedIco">
											<a href="{$GLOBALS.site_url}/saved-jobs/">[[Saved jobs]]</a>
										</li>
									{elseif $acl->getPermissionParams('save_job') == "message"}
										<li class="savedIco">
											<a href="{$GLOBALS.site_url}/saved-jobs/"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 400, '[[Saved jobs]]'); return false;">[[Saved jobs]]</a>
										</li>
									{/if}
								{else}
									<li class="savedIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id=job', 400, '[[Saved jobs]]'); return false;" href="{$GLOBALS.site_url}/saved-listings/">[[Saved jobs]]</a>
									</li>
								{/if}
								{if $GLOBALS.current_user.logged_in}
									{if $acl->isAllowed('save_searches')}
										<li class="savedIco">
											<a href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
										</li>
									{elseif $acl->getPermissionParams('save_searches') == "message"}
										<li class="savedIco">
											<a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 400, '[[Saved searches]]'); return false;">[[Saved searches]]</a>
										</li>
									{/if}
								{else}
									<li class="savedIco">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-searches/', 400, '[[Saved searches]]'); return false;" href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
									</li>
								{/if}
							</ul>
						</div>
					{/if}
					<!-- END TOP QUICK LINKS -->
				</div>

				{if $view_on_map}
					<div id="googleMap-links">
						<a href="{$GLOBALS.site_url}{if $url == '/search-results-jobs/'}{$url}?searchId={$searchId}&amp;action=search&amp;{else}{$url}?{/if}page=1&amp;view=list" id="listView-icon" {if $view == 'list'}onclick="return false;" class="listLink-active"{/if}>[[List View]]</a> &nbsp;
						<a href="{$GLOBALS.site_url}{if $url == '/search-results-jobs/'}{$url}?searchId={$searchId}&amp;action=search&amp;{else}{$url}?{/if}page=1&amp;view=map" id="mapView-icon" {if $view == 'map'}onclick="return false;" class="listLink-active"{/if}>[[Map View]]</a>
					</div>
					<div class="clr"></div>
				{/if}

				<!-- TOP RESULTS - PER PAGE - PAGE NAVIGATION -->
				<div class="topNavBarLeft"></div>
				<div class="topNavBar">
					<div class="numberResults">
						{assign var="listings_number" value=$listing_search.listings_number}
						[[Results:]] {$listings_number} {if $listings_number == 1}[[Job]]{else}[[jobs]]{/if}
						{if $search_criteria.ZipCode.value.location and $search_criteria.ZipCode.value.radius}
							{assign var="radius" value=$search_criteria.ZipCode.value.radius}
							{capture name=radius_search_unit}
								[[$GLOBALS.radius_search_unit]]
							{/capture}
							{assign var="radius_search_unit" value=$smarty.capture.radius_search_unit|escape:"html"}
							{assign var="location" value=$search_criteria.ZipCode.value.location|escape:"html"}
							[[within $radius $radius_search_unit of $location]]
						{/if}
					</div>
					<div class="numberPerPage">
						<form method="get" action="" class="listings_per_page_form tableSRNavPerPage">
						[[Number of jobs per page]]:
							<select id="listings_per_page1" name="listings_per_page1" onchange="submitForm(1); return false;">
							<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
							<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
							<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
							<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
						</select>
						</form>
					</div>
					<div class="pageNavigation">
						<span class="prevBtn"><img src="{image}prev_btn.png" alt="[[Previous]]"/>
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
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
						<span class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
						<img src="{image}next_btn.png" alt="[[Next]]"/></span>
					</div>
				</div>
				<div class="topNavBarRight"></div>
				<!-- END TOP RESULTS - PER PAGE - PAGE NAVIGATION -->
			</div>

			<!-- START REFINE SEARCH -->
			{if $refineSearch || $view == 'map'}
				<div id="refineResults">
					<div id="blockBg">
						<div id="blockTop"></div>
						<div id="blockInner">
							{if $view == 'map' && $listings}
								<table cellpadding="0" cellspacing="0" width="100%" id="refineResults">
									<thead>
										<tr>
											<th class="tableLeft"> </th>
											<th>[[Jobs Search Results]]:</th>
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
																<a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"|escape:"url"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}" target="_blank"><span class="strong">{$listing.Title}</span></a><br />
																<span class="strong">[[Company]]:</span>
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
							<thead>
								<tr>
									<th class="tableLeft"> </th>
									<th width="50%">
										<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=Title&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'Title'}DESC{else}ASC{/if}&amp;page={$listing_search.current_page}">[[Title]]</a>
										{if $is_show_brief_or_detailed}
											<a href="?{if $searchId}searchId={$searchId}&amp;{/if}{if $params|strpos:"searchId" !== false}{$params|regex_replace:"/searchId=$searchId&amp;/":""|regex_replace:"/&amp;show_brief_or_detailed=$show_brief_or_detailed/":""}{else}{$params}{/if}&amp;show_brief_or_detailed={if $show_brief_or_detailed == 'brief'}detailed{else}brief{/if}" id="showBriefOrDetailed">({if $show_brief_or_detailed == 'brief'}[[show detailed]]{else}[[Show Brief]]{/if})</a>
										{/if}
										{if $listing_search.sorting_field == 'Title'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
									</th>
									<th width="20%">
										{if $userInfo.CompanyName eq '' }
											<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=CompanyName&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'CompanyName'}DESC{else}ASC{/if}&amp;page={$listing_search.current_page}">[[Company]]</a>
											{if $listing_search.sorting_field == 'CompanyName'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
										{/if}
									</th>
									<th width="20%">
										<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field[0]=Location_City&amp;sorting_field[1]=Location_State&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field.0 == 'Location_City'}DESC{else}ASC{/if}&amp;page={$listing_search.current_page}">[[Location]]</a>
										{if $listing_search.sorting_field.0 == 'Location_City'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
									</th>
									<th width="10%">
										<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=activation_date&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'activation_date'}DESC{else}ASC{/if}&amp;page={$listing_search.current_page}">[[Posted]]</a>
										{if $listing_search.sorting_field == 'activation_date'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
									</th>
									<th class="tableRight"> </th>
								</tr>
							</thead>
							<tbody class="searchResultsJobs">
								<!-- Job Info Start -->
								{include file="search_results_jobs_listings.tpl"}
								<!-- END Job Info Start -->
							</tbody>
						</table>
					{/if}
				{else}
					<div id="noListingsFounds"><p class="information">[[There are no postings meeting the criteria you specified]]</p></div>
					<table cellspacing="0" id="listingsTableResults">
						<thead>
							<tr>
								<th class="tableLeft"> </th>
								<th width="50%">[[Title]]</th>
								<th width="20%">[[Company]]</th>
								<th width="20%">[[Location]]</th>
								<th width="10%">[[Posted]]</th>
								<th class="tableRight"> </th>
							</tr>
						</thead>
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


			<!-- BOTTOM RESULTS - PER PAGE - PAGE NAVIGATION -->
			<div id="endResults">
				<div class="topResultsLinks">
					<div class="topNavBarLeft"></div>
					<div class="topNavBar">
						<div class="numberResults">
							{assign var="listings_number" value=$listing_search.listings_number}
							[[Results:]] {$listings_number} {if $listings_number == 1}[[Job]]{else}[[jobs]]{/if}
							{if $search_criteria.ZipCode.value.location and $search_criteria.ZipCode.value.radius}
								{assign var="radius" value=$search_criteria.ZipCode.value.radius}
								{capture name=radius_search_unit}
									[[$GLOBALS.radius_search_unit]]
								{/capture}
								{assign var="radius_search_unit" value=$smarty.capture.radius_search_unit|escape:"html"}
								{assign var="location" value=$search_criteria.ZipCode.value.location|escape:"html"}
								[[within $radius $radius_search_unit of $location]]
							{/if}
						</div>
						<div class="numberPerPage">
							<form method="get" action="" class="listings_per_page_form tableSRNavPerPage">
								[[Number of jobs per page]]:
								<select id="listings_per_page2" name="listings_per_page2" onchange="submitForm(2); return false;">
									<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
									<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
									<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
									<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
								</select>
							</form>
						</div>
						{if !$view == 'map'}
							<div class="pageNavigation">
								<span class="prevBtn"><img src="{image}prev_btn.png" alt=""/>
								{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</span>
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
								<span class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}
								<img src="{image}next_btn.png" alt=""/></span>
							</div>
						{/if}

					</div>
					<div class="topNavBarRight"></div>
				</div>
			</div>
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
						$('#refineResults div#blockBg div#blockInner div#ajaxRefineSearch').html(data);
					}
				});
			}
			// make request to listings providers after page loads
			$(function() {
				requestToListingsProviders();
				showRefineSearch();
			});
		{/if}
	</script>
</div>
