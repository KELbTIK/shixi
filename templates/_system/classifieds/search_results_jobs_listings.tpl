{assign var='index' value=$listing_search.current_page*$listing_search.listings_per_page-$listing_search.listings_per_page}
{foreach from=$listings item=listing name=listings}
	{if $listing.api}
		{if $api != $listing.api}
			<tr class="{$listing.api}Block api-jobs-plugin">
				<td colspan="6">{$listing.code}</td>
			</tr>
			{assign var="api" value=$listing.api}
			{assign var='total' value=$smarty.foreach.listings.iteration-1}
		
			{if $api == 'indeed'}
				{assign var='currentIndeedPage' value=$listing.pageNumber}
				{assign var='totalIndeedPages' value=$listing.totalPages}
			{/if}
		{/if}
		<tr {if $listing.priority == 1}class="priorityListing {$listing.api}Block"{else}class="{cycle values = 'evenrow,oddrow' advance=true} {$listing.api}Block"{/if}>
			<td colspan="6">
				<table>
					<tr>
						<td> </td>
						<td width="50%">
							<a name="listing_{$listing.id}"></a>
							<a href="{$listing.url}" {$listing.target} {$listing.onmousedown} {$listing.onclick}><span class="strong">{$listing.Title|escape:'html'}</span></a>
						</td>
						<td width="20%">{$listing.CompanyName|escape:'html'}</td>
						<td width="20%">{locationFormat location=$listing.Location format="short"}</td>
						<td width="10%">[[$listing.activation_date]]</td>
						<td> </td>
					</tr>
					<tr>
						<td> </td>
						{if $show_brief_or_detailed != 'brief'}
							{if $api == 'indeed'}
								<td colspan="4">{$listing.JobDescription|truncate:120}</td>
							{else}
								<td colspan="4">{$listing.JobDescription|strip_tags|truncate:120}</td>
							{/if}
						{else}
							<td colspan="4"></td>
						{/if}
						<td> </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="{$listing.api}Block">
			<td colspan="6" class="separateListing"> </td>
		</tr>
	{else}
		<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow' advance=true}"{/if}>
			<td colspan="6">
				<table>
					<tr>
                        <td> </td>
						<td width="50%">
							<a name="listing_{$listing.id}"></a>
							<a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}"><span class="strong">{$listing.Title|escape:'html'}</span></a>
						</td>
						<td width="20%">
							<a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">{$listing.user.CompanyName|escape:'html'}</a>
						</td>
						<td width="20%">{locationFormat location=$listing.Location format="short"}</td>
						<td width="10%">[[$listing.activation_date]]</td>
						<td> </td>
					</tr>
					<tr>
						<td> </td>
						{if $show_brief_or_detailed != 'brief'}<td colspan="4">{$listing.JobDescription|strip_tags|truncate:120}</td>
						{else}
							<td colspan="4"></td>
						{/if}
						<td> </td>
					</tr>
					<tr>
						<td> </td>
						<td colspan="4">
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
								<li class="viewDetails">
									<a href="{$GLOBALS.site_url}/display-job/{$listing.id}/{$listing.Title|regex_replace:"/[\\/\\\:*?\"<>|%#$\s]/":"-"}.html?searchId={$searchId}&amp;page={$listing_search.current_page}">[[View job details]]</a>
								</li>
								{if $listing.video.file_url}
									<li class="viewVideo">
										<a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?listing_id={$listing.id}&amp;field_id=video', 282, 'VideoPlayer'); return false;"  href="{$GLOBALS.site_url}/video-player/?listing_id={$listing.id}&amp;field_id=video">[[Watch a video]]</a>
									</li>
								{/if}
							</ul>
							<div class="clr"></div>
							<span id = 'formNote_{$listing.id}'>
								{if $listing.saved_listing && $listing.saved_listing.note && $listing.saved_listing.note != ''}
									<br/><span class="strong">[[My notes]]:</span> {$listing.saved_listing.note}
								{/if}
							</span>
						</td>
						<td> </td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="6" class="separateListing"> </td>
		</tr>

	{/if}
{/foreach}

{* Page navigation for Indeed search results *}
{if $api == 'indeed'}
	<tr class="indeedBlock">
		<td colspan="6" style="text-align: right;">
			<span id="indeed_ajax_preloader_listings_results" class="preloader">&nbsp;<img src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_16.gif" />&nbsp;</span>
			{if $currentIndeedPage > 1}<a onclick="requestToListingsProviders({$currentIndeedPage-1}, 'indeed');return false;" href="#" style="text-decoration: none;">&lt;&lt;</a>{/if}
			&nbsp;<b>{$currentIndeedPage}</b>&nbsp;
			{if $currentIndeedPage < $totalIndeedPages}<a onclick="requestToListingsProviders({$currentIndeedPage+1}, 'indeed'); return false;" href="#" style="text-decoration: none;">&gt;&gt;</a>{/if}
		</td>
	</tr>
{/if}

{if !$GLOBALS.is_ajax}
	<!-- preloader row here -->
	<tr id="ajax_preloader_listings_results" class="preloader">
		<td colspan="6">&nbsp;<img src="{$GLOBALS.site_url}/templates/_system/main/images/ajax_preloader_circular_32.gif" /></td>
	</tr>
{/if}
<script type="text/javascript">
	function addStatisticsForSimplyHired() {
		var url = window.SJB_GlobalSiteUrl + '/partnersite/';
		$.get(url, { action: "simplyHired" } );
	}
</script>