<div id="no-padding">
	<script type="text/javascript" language="JavaScript">
		$.ui.dialog.prototype.options.bgiframe = true;
		function submitForm(id) {
			var lpp = document.getElementById("listings_per_page" + id);
			location.href = "?searchId={$searchId|escape:'url'}&action=search&page=1&listings_per_page=" + lpp.value + '&view={$view|escape:'url'}';
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

		<div id="topResults">
			<div class="headerBgBlock">
				{if isset($search_criteria.username.value)}
					{assign var=tmp_listing value=$listings|@current}
					{if $tmp_listing.user.FirstName ne '' or $tmp_listing.user.LastName ne ''}
						{assign var="firstName" value=$tmp_listing.user.FirstName}
						{assign var="lastName" value=$tmp_listing.user.LastName}
						<div class="Results">{tr}Resumes by $firstName $lastName{/tr|escape:'html'}</div><span></span>
					{/if}
				{else}
					<div class="Results">[[Resume Search Results]]</div><span>&nbsp;</span>
				{/if}
				<!-- TOP QUICK LINKS -->
				<div class="topResultsLinks">
					<ul>
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
						<li class="savedIco">
							{if $GLOBALS.current_user.logged_in}
								{if $acl->isAllowed('save_resume')}
									<a href="{$GLOBALS.site_url}/saved-resumes/">[[Saved resumes]]</a>
								{elseif $acl->getPermissionParams('save_resume') == "message"}
									<a href="{$GLOBALS.site_url}/saved-resumes/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 400, '[[Saved resumes]]'); return false;">[[Saved resumes]]</a>
								{/if}
							{else}<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id=resume', 400, '{capture name="savedResumesPopup"}[[Saved resumes]]{/capture}{$smarty.capture.savedResumesPopup|escape:'quotes'}'); return false;" href="{$GLOBALS.site_url}/saved-listings/">[[Saved resumes]]</a>
							{/if}
						</li>
						<li class="savedIco">
							{if $GLOBALS.current_user.logged_in}
								{if $acl->isAllowed('save_searches')}
									<a href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
								{elseif $acl->getPermissionParams('save_searches') == "message"}
									<a href="{$GLOBALS.site_url}/saved-searches/" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_searches', 400, '[[Saved searches]]'); return false;">[[Saved searches]]</a>
								{/if}
							{else}<a onclick="popUpWindow('{$GLOBALS.site_url}/saved-searches/', 400, '[[Saved searches]]'); return false;" href="{$GLOBALS.site_url}/saved-searches/">[[Saved searches]]</a>
							{/if}
						</li>
					</ul>
				</div>
				<!-- END TOP QUICK LINKS -->
			</div>

			{if $view_on_map}
				<div id="googleMap-links">
					<a href="{$GLOBALS.site_url}/search-results-resumes/?searchId={$searchId}&amp;action=search&amp;page=1&amp;view=list" id="listView-icon" {if $view == 'list'}onclick="return false;" class="listLink-active"{/if}>[[List View]]</a> &nbsp;
					<a href="{$GLOBALS.site_url}/search-results-resumes/?searchId={$searchId}&amp;action=search&amp;page=1&amp;view=map" id="mapView-icon" {if $view == 'map'}onclick="return false;" class="listLink-active"{/if}>[[Map View]]</a>
				</div>
				<div class="clr"></div>
			{/if}

			<!-- TOP RESULTS - PER PAGE - PAGE NAVIGATION -->
			<div class="topNavBarLeft"></div>
			<div class="topNavBar">
				<div class="numberResults">
					{assign var="listings_number" value=$listing_search.listings_number}
					[[Results:]] {$listings_number} {if $listings_number == 1}[[Resume]]{else}[[resumes]]{/if}
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
						[[Number of resumes per page]]:
						<select id="listings_per_page1" name="listings_per_page1" onchange="submitForm(1); return false;">
							<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
							<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
							<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
							<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
						</select>
					</form>
				</div>

				<ul class="pagination">
					<li class="prevBtn">
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</li>
					<li class="navigationItems">
						{if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1&amp;view={$view}">1</a>{/if}
						{if $listing_search.current_page-3 > 1}...{/if}
						{if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}&amp;view={$view}">{$listing_search.current_page-2}</a>{/if}
						{if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">{$listing_search.current_page-1}</a>{/if}
						<a href="#">{$listing_search.current_page}</a>
						{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">{$listing_search.current_page+1}</a>{/if}
						{if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}&amp;view={$view}">{$listing_search.current_page+2}</a>{/if}
						{if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
						{if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}&amp;view={$view}">{$listing_search.pages_number}</a>{/if}
					</li>
					<li class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}

					</li>
				</ul>
			</div>
			<div class="topNavBarRight"></div>
			<!-- END RESULTS / PER PAGE / NAVIGATION -->
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
                                            <ul class="pagination">
                                                <li class="prevBtn">
                                                    {if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</li>
                                                <li class="navigationItems">
                                                    {if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1&amp;view={$view}">1</a>{/if}
                                                    {if $listing_search.current_page-3 > 1}...{/if}
                                                    {if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}&amp;view={$view}">{$listing_search.current_page-2}</a>{/if}
                                                    {if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">{$listing_search.current_page-1}</a>{/if}
                                                    <a href="#">{$listing_search.current_page}</a>
                                                    {if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">{$listing_search.current_page+1}</a>{/if}
                                                    {if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}&amp;view={$view}">{$listing_search.current_page+2}</a>{/if}
                                                    {if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
                                                    {if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}&amp;view={$view}">{$listing_search.pages_number}</a>{/if}
                                                </li>
                                                <li class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}

                                                </li>
                                            </ul>
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
							<th width="24%">[[Name]]</th>
							<th width="29%">
								<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=Title&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'Title'}DESC{else}ASC{/if}">[[Title]]</a>
								{if $is_show_brief_or_detailed}
									<a href="?{if $searchId}searchId={$searchId}&amp;{/if}{if $params|strpos:"searchId" !== false}{$params|regex_replace:"/searchId=$searchId&amp;/":""|regex_replace:"/&amp;show_brief_or_detailed=$show_brief_or_detailed/":""}{else}{$params}{/if}&amp;show_brief_or_detailed={if $show_brief_or_detailed == 'brief'}detailed{else}brief{/if}" id="showBriefOrDetailed">({if $show_brief_or_detailed == 'brief'}[[show detailed]]{else}[[Show Brief]]{/if})</a>
								{/if}
								{if $listing_search.sorting_field == 'Title'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
							</th>
							<th width="14%">
								<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=TotalYearsExperience&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'TotalYearsExperience'}DESC{else}ASC{/if}">[[Experience]]</a>
								{if $listing_search.sorting_field == 'TotalYearsExperience'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
							</th>
							<th width="19%">
								<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field[0]=Location_City&amp;sorting_field[1]=Location_State&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field.0 == 'Location_City'}DESC{else}ASC{/if}">[[Location]]</a>
								{if $listing_search.sorting_field.0 == 'Location_City'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
							</th>
							<th width="9%">
								<a href="?searchId={$searchId}&amp;action=search&amp;sorting_field=activation_date&amp;sorting_order={if $listing_search.sorting_order == 'ASC' && $listing_search.sorting_field == 'activation_date'}DESC{else}ASC{/if}">[[Posted]]</a>
								{if $listing_search.sorting_field == 'activation_date'}{if $listing_search.sorting_order == 'ASC'}<img src="{image}b_up_arrow.png" alt="Up" />{else}<img src="{image}b_down_arrow.png" alt="Down" />{/if}{/if}
							</th>
							<th class="tableRight"> </th>
						</tr>
						</thead>
						<tbody>
						<!-- Job Info Start -->
						{assign var='index' value=$listing_search.current_page*$listing_search.listings_per_page-$listing_search.listings_per_page}
						{foreach from=$listings item=listing name=listings}
							{if $listing.anonymous == 1 && $search_criteria.username.value != ''}
							{* it's anonimous resume and search resumes by name - don't show it *}
							{else}
								<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow' advance=true}"{/if}>
									<td colspan="7">
										<table>
											<tr>
												<td> </td>
												<td width="24%">
													<a name="listing_{$listing.id}">&nbsp;</a>
													<span class="strong">{if $listing.anonymous == 1}[[Anonymous User]]{else}<span class="longtext-25">{$listing.user.FirstName}</span> <span class="longtext-25">{$listing.user.LastName}</span>{/if}</span>
												</td>
												<td width="29%">
													<a href="{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}" class="JobTittleSR"><span class="strong">{$listing.Title}</span></a>
												</td>
												<td width="14%"> {if $listing.TotalYearsExperience>0}{$listing.TotalYearsExperience} [[years]]{/if}</td>
												<td width="19%"> {locationFormat location=$listing.Location format="short"}</td>
												<td width="9%"> [[$listing.activation_date]]</td>
												<td> </td>
											</tr>
											{if $show_brief_or_detailed != 'brief'}
												{if $listing.Objective}
													<tr>
														<td> </td>
														<td colspan="5">{$listing.Objective|strip_tags|truncate:120}</td>
														<td> </td>
													</tr>
												{/if}
											{/if}
											<tr>
												<td> </td>
												<td colspan="5">
													<ul>
														{if $listing.saved_listing && $acl->isAllowed('save_resume')}
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
															{if $acl->isAllowed('save_resume')}
																<li class="saved2Ico">
																	<span id='notes_{$listing.id}'>
																		<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&amp;listing_type=resume" onclick="{if $GLOBALS.current_user.logged_in}SaveAd('notes_{$listing.id}', '{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=resume'){else}popUpWindow('{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&listing_type=resume', 300, '[[Save this Resume]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}){/if}; return false;"  class="action">[[Save ad]]</a>
																	</span>
																</li>
															{elseif $acl->getPermissionParams('save_resume') == "message"}
																<li class="saved2Ico">
																	<span id='notes_{$listing.id}'>
																		<a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_resume', 300, '[[Save ad]]'); return false;"  class="action">[[Save ad]]</a>
																	</span>
																</li>
															{/if}
														{/if}
														<li class="viewDetails"><a href="{$GLOBALS.site_url}/display-resume/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}">[[View resume details]]</a></li>
														{if $listing.video.file_url}<li class="viewVideo"><a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?field_id=video&amp;listing_id={$listing.id}', 282, 'VideoPlayer'); return false;"  href="{$GLOBALS.site_url}/video-player/?field_id=video&amp;listing_id={$listing.id}">[[Watch a video]]</a></li>{/if}
													</ul>
													<div class="clr"><br/></div>
													<span id = 'formNote_{$listing.id}'>
														{if $listing.saved_listing && $listing.saved_listing.note && $listing.saved_listing.note != ''}
															<b>[[My notes]]:</b> {$listing.saved_listing.note}
														{/if}
													</span>
													<br/><br/>
												</td>
												<td> </td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="7" class="separateListing"> </td>
								</tr>
							{/if}
						{/foreach}
						<!-- END Job Info Start -->
						</tbody>
					</table>
				{/if}
			{else}
				<div class="noListingsFound"><p class="information">[[There are no postings meeting the criteria you specified]]</p></div>
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
						[[Results:]] {$listings_number} {if $listings_number == 1}[[Resume]]{else}[[resumes]]{/if}
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
							[[Number of resumes per page]]:
							<select id="listings_per_page2" name="listings_per_page2" onchange="submitForm(2); return false;">
								<option value="10" {if $listing_search.listings_per_page == 10}selected="selected"{/if}>10</option>
								<option value="20" {if $listing_search.listings_per_page == 20}selected="selected"{/if}>20</option>
								<option value="50" {if $listing_search.listings_per_page == 50}selected="selected"{/if}>50</option>
								<option value="100" {if $listing_search.listings_per_page == 100}selected="selected"{/if}>100</option>
							</select>
						</form>
					</div>
					{if !$view == 'map'}
                        <ul class="pagination">
                            <li class="prevBtn">
                                {if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">[[Previous]]</a>{else}<a>[[Previous]]</a>{/if}</li>
                            <li class="navigationItems">
                                {if $listing_search.current_page-3 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page=1&amp;view={$view}">1</a>{/if}
                                {if $listing_search.current_page-3 > 1}...{/if}
                                {if $listing_search.current_page-2 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-2}&amp;view={$view}">{$listing_search.current_page-2}</a>{/if}
                                {if $listing_search.current_page-1 > 0}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page-1}&amp;view={$view}">{$listing_search.current_page-1}</a>{/if}
                                <a href="#">{$listing_search.current_page}</a>
                                {if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">{$listing_search.current_page+1}</a>{/if}
                                {if $listing_search.current_page+2 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+2}&amp;view={$view}">{$listing_search.current_page+2}</a>{/if}
                                {if $listing_search.current_page+3 < $listing_search.pages_number}...{/if}
                                {if $listing_search.current_page+3 < $listing_search.pages_number + 1}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.pages_number}&amp;view={$view}">{$listing_search.pages_number}</a>{/if}
                            </li>
                            <li class="nextBtn">{if $listing_search.current_page+1 <= $listing_search.pages_number}<a href="?searchId={$searchId}&amp;action=search&amp;page={$listing_search.current_page+1}&amp;view={$view}">[[Next]]</a>{else}<a>[[Next]]</a>{/if}

                            </li>
                        </ul>
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
			$("#listingsResults").highlight({$keywordsHighlight});
		{/if}
		{if !$listings}
			$(".topNavBar").hide();
			$(".topNavBarRight").hide();
			$(".topNavBarLeft").hide();
			$(".noListingsFound").show();
		{/if}

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
					$('#refineResults div#blockBg div#blockInner div#ajaxRefineSearch').html(data);
				}
			});
		}

		$(function() {
			showRefineSearch();
		});
	</script>
</div>
