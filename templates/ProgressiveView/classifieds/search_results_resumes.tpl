<script type="text/javascript" language="JavaScript">
	$.ui.dialog.prototype.options.bgiframe = true;
	function submitForm(id) {
		var lpp = document.getElementById("listings_per_page" + id);
		location.href = "?searchId={$searchId|escape:'url'}&action=search&page=1&listings_per_page=" + lpp.value + '&view={$view|escape:'url'}';
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
	function popUpWindow(url, widthWin, title, parentReload, userLoggedIn){
		reloadPage = false;
		newPageReload = false;
		$("#loading").show();
		var messageBox = $("#messageBox");
		messageBox.dialog( 'destroy' ).html('{$smarty.capture.displayJobProgressBar|escape:'javascript'}');
		messageBox.dialog({
			autoOpen: false,
			width: widthWin,
			height: 'auto',
			modal: true,
			title: title,
			close: function(event, ui) {
				if ((parentReload && !userLoggedIn) || newPageReload == true) {
					if (reloadPage)
						parent.document.location.reload();
				}
			}
		}).hide();
		$.get(url, function(data){
			$("#messageBox").html(data).dialog("open").show();
			$("#loading").hide();
		});
		return false;
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
	<div {if $refineSearch}class="results"{else}class="noRefine"{/if}>
	<div id="topResults" {if $GLOBALS.user_page_uri == '/search-results-jobs/'}class="refine-fix"{/if}>
		<div class="headerBgBlock">
			{if isset($search_criteria.username.value)}
				{assign var=tmp_listing value=$listings|@current}
				{if $tmp_listing.user.FirstName ne '' or $tmp_listing.user.LastName ne ''}
					{assign var="firstName" value=$tmp_listing.user.FirstName}
					{assign var="lastName" value=$tmp_listing.user.LastName}
					<div class="Results">{tr}Resumes by $firstName $lastName{/tr|escape:'html'}</div><span></span>
				{/if}
			{else}
			{/if}
			<!-- TOP QUICK LINKS -->
			<div class="topResultsLinks">
				<ul class="breadcrumbs-fix">
					<li class="modifySearchIco"><a href="{$GLOBALS.site_url}/search-resumes/?searchId={$searchId}">[[Modify search]]</a></li>
					{if $listing_type_id != ''}
						{if $acl->isAllowed('save_searches')}
							<li class="saveSearchIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/save-search/?searchId={$searchId}', 400, '[[Save this Search]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a></li>
						{elseif $acl->getPermissionParams('save_searches') == "message"}
							<li class="saveSearchIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 400, '[[Save this Search]]'); return false;" href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save this Search]]</a></li>
						{/if}

						{if $acl->isAllowed('use_resume_alerts')}
							<li class="saveSearchIco">
								{if $GLOBALS.current_user.logged_in}
									<a onclick="popUpWindow('{$GLOBALS.site_url}/save-search/?searchId={$searchId}&alert=1', 400, '[[Save Resume Alert]]', true, true); return false;"
									   href="{$GLOBALS.site_url}/save-search/?searchId={$searchId}">[[Save Resume Alert]]</a>
								{else}
									<a onclick="popUpWindow('{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}', 400, '[[Save Resume Alert]]', true, false); return false;"
									   href="{$GLOBALS.site_url}/guest-alerts/create/?searchId={$searchId}">[[Save Resume Alert]]</a>
								{/if}
							</li>
						{elseif $acl->getPermissionParams('use_resume_alerts') == "message"}
							<li class="saveSearchIco">
								<a onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts', 400, '[[Save Resume Alert]]', true, false); return false;"
								   href="{$GLOBALS.site_url}/access-denied/?permission=use_resume_alerts">[[Save Resume Alert]]</a>
							</li>
						{/if}
					{/if}
					{if $GLOBALS.current_user.logged_in}
						{if $acl->isAllowed('save_resume')}
							<li class="savedIco">
								<a href="{$GLOBALS.site_url}/saved-resumes/">[[Saved resumes]]</a>
							</li>
						{elseif $acl->getPermissionParams('save_resume') == "message"}
							<li class="savedIco">
								<a href="{$GLOBALS.site_url}/saved-resumes/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 440, '[[Saved resumes]]'); return false;">[[Saved resumes]]</a>
							</li>
						{/if}
					{else}
						<li class="savedIco">
							<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id=resume', 440, '{capture name="savedResumesPopup"}[[Saved resumes]]{/capture}{$smarty.capture.savedResumesPopup|escape:'quotes'}'); return false;" href="{$GLOBALS.site_url}/saved-listings/">[[Saved resumes]]</a>
						</li>
					{/if}
					{if $GLOBALS.current_user.logged_in}
						{if $acl->isAllowed('save_searches')}
							<li class="savedIco">
								<a href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
							</li>
						{elseif $acl->getPermissionParams('save_searches') == "message"}
							<li class="savedIco">
								<a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 450, '[[Saved searches]]'); return false;">[[Saved searches]]</a>
							</li>
						{/if}
					{else}
						<li class="savedIco">
							<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-searches/', 450, '[[Saved searches]]'); return false;" href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
						</li>
					{/if}
				</ul>
			</div>
		</div>
		<!-- END TOP QUICK LINKS -->
	</div>

	<div class="results-paging">
		<div class="head">
			<h1>
				{assign var="listings_number" value=$listing_search.listings_number}
				{$listings_number} {if $listings_number == 1}[[Resume]]{else}[[resumes]]{/if}
			</h1>
			{if $view_on_map}
				<div id="googleMap-links">
					<a href="{$GLOBALS.site_url}/search-results-resumes/?searchId={$searchId}&amp;action=search&amp;page=1&amp;view=list&amp;show_brief_or_detailed=brief" id="showBriefOrDetailed" {if $view == 'list' && $show_brief_or_detailed == 'brief'}onclick="return false;" class="listLink-active"{/if}>[[Show Brief]]</a> &nbsp;
					<a href="{$GLOBALS.site_url}/search-results-resumes/?searchId={$searchId}&amp;action=search&amp;page=1&amp;view=list&amp;show_brief_or_detailed=detailed" id="listView-icon" {if $view == 'list' && $show_brief_or_detailed == 'detailed'}onclick="return false;" class="listLink-active"{/if}>[[List View]]</a> &nbsp;
					<a href="{$GLOBALS.site_url}/search-results-resumes/?searchId={$searchId}&amp;action=search&amp;page=1&amp;view=map" id="mapView-icon" {if $view == 'map'}onclick="return false;" class="listLink-active"{/if}>[[Map View]]</a>
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
								<option value="TotalYearsExperience" {if $listing_search.sorting_field == 'TotalYearsExperience'}selected="selected"{/if}>[[Experience]]</option>
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
				<span>[[Number of resumes per page]]</span>
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
						<table cellpadding="0" cellspacing="0" width="100%" id="refineResults" class="refine-map-view">
							<thead>
							<tr>
								<th class="tableLeft"> </th>
								<th>[[Resume Search Results]]:</th>
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
													<a href="{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}" target="_blank"><span class="strong">{$listing.Title|escape:'html'}</span></a><br/>
													{$listing.Location.City}
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
					<tbody>
					<!-- Job Info Start -->
					{assign var='index' value=$listing_search.current_page*$listing_search.listings_per_page-$listing_search.listings_per_page}
					{foreach from=$listings item=listing name=listings}
						{if $listing.anonymous == 1 && $search_criteria.username.value != ''}
						{* it's anonimous resume and search resumes by name - don't show it *}
						{else}
							<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow' advance=true}"{/if}>
								<td>
									<div class="listing-section">
										<div class="listing-title">
											<a name="listing_{$listing.id}"></a>
											<a href="{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}" class="JobTittleSR"><span class="strong">{$listing.Title}</span></a>
										</div>
										<div class="listing-info">
											<div class="left-side">
												<span class="captions">[[Name]]:</span><span class="captions-field">{if $listing.anonymous == 1}[[Anonymous User]]{else}<span class="longtext-25">{$listing.user.FirstName}</span> <span class="longtext-25">{$listing.user.LastName}</span>{/if}</span>
												<br/><span class="captions">[[Location]]:</span><span class="captions-field">{locationFormat location=$listing.Location format="short"}</span>
												<br/><span class="captions">[[Experience]]:</span><span class="captions-field">{if $listing.TotalYearsExperience>0}{$listing.TotalYearsExperience} [[years]]{/if}</span>
												<br/><span class="captions">[[Posted]]:</span><span class="captions-field">[[$listing.activation_date]]</span>
											</div>
											<div class="show-brief">
												{if $show_brief_or_detailed != 'brief'}
													{if $listing.Objective}
														{$listing.Objective|strip_tags|truncate:120}
													{/if}
												{/if}
											</div>
										</div>
										<div class="listing-links">
											<ul>
												{if $listing.saved_listing &&  $acl->isAllowed('save_job')}
													{if $listing.saved_listing.note && $listing.saved_listing.note != ''}
														<li class="saved2Ico">
															<span id='notes_{$listing.id}'>
																<a href="{$GLOBALS.site_url}/edit-notes/?listing_id={$listing.id}" onclick="SaveAd( 'formNote_{$listing.id}', '{$GLOBALS.site_url}/edit-notes/?listing_sid={$listing.id}'); return false;"  class="action">[[Edit notes]]</a>&nbsp;&nbsp;
															</span>
														</li>
													{else}
														<li class="saved2Ico">
															<span id='notes_{$listing.id}'>
																<a href="{$GLOBALS.site_url}/add-notes/?listing_id={$listing.id}" onclick="SaveAd( 'formNote_{$listing.id}', '{$GLOBALS.site_url}/add-notes/?listing_sid={$listing.id}'); return false;"  class="action">[[Add notes]]</a>&nbsp;&nbsp;
															</span>
														</li>
													{/if}
												{else}
													{if $acl->isAllowed('save_job')}
														<li class="saved2Ico">
															<span id='notes_{$listing.id}'>
																<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="{if $GLOBALS.current_user.logged_in}SaveAd('notes_{$listing.id}', '{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=job'){else}popUpWindow('{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=job', 300, 'Save this Job', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}){/if}; return false;"  class="action">[[Save ad]]</a>&nbsp;&nbsp;
															</span>
														</li>
													{elseif $acl->getPermissionParams('save_job') == "message"}
														<li class="saved2Ico">
															<span id='notes_{$listing.id}'>
																<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 300, '[[Save ad]]'); return false;" class="action">[[Save ad]]</a>&nbsp;&nbsp;
															</span>
														</li>
													{/if}
												{/if}
												<li class="viewDetails"><a href="{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}">[[View resume details]]</a></li>
												{if $listing.video.file_url}<li class="viewVideo"><a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?field_id=video&amp;listing_id={$listing.id}', 282, 'VideoPlayer'); return false;"  href="{$GLOBALS.site_url}/video-player/?field_id=video&amp;listing_id={$listing.id}">[[Watch a video]]</a></li>{/if}
											</ul>
											<br/>
											<span id = 'formNote_{$listing.id}'>
												{if $listing.saved_listing && $listing.saved_listing.note && $listing.saved_listing.note != ''}
													<b>[[My notes]]:</b> {$listing.saved_listing.note}<br/>
												{/if}
											</span>
											<br />
										</div>
										<div class="clr"></div>
									</div>
								</td>
							</tr>
						{/if}
					{/foreach}
					<!-- END Job Info Start -->
					</tbody>
				</table>
				<div class="pageNavigation">
					<span class="prevBtn">
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">&#171;&nbsp;[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}
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
						{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]&nbsp;&#187;</a>{else}<a>[[Next]]</a>{/if}
					</span>
				</div>
			{/if}
		{else}
			<div class="noListingsFound"><p class="information">[[There are no postings meeting the criteria you specified]]</p></div>
		{/if}
	</div>
	<!-- END LISTINGS TABLE -->

	<!-- BOTTOM RESULTS - PER PAGE - PAGE NAVIGATION -->
	</div>
	<!-- END BOTTOM RESULTS - PER PAGE - PAGE NAVIGATION -->
{/if}

<script type="text/javascript" language="JavaScript">
	{if $keywordsHighlight}
		$("#listingsResults").highlight({$keywordsHighlight});
	{/if}
	{if !$listings}
		$(".topNavBar").hide();
		$(".topNavBarRight").hide();
		$(".topNavBarLeft").hide();
		$(".noListingsFound").show();
	{/if}
	$("#sort-by-select, #listings_per_page2").selectbox();

	function showRefineSearch() {
		var ajaxUrl = "{$GLOBALS.site_url}/ajax/";
		var ajaxParams = {
			'action' : 'get_refine_search_block',
			'listing_type[equal]' : 'Resume',
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

	$(function() {
		showRefineSearch();
	});
</script>