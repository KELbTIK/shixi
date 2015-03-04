<div id="displayListing">
	{title} {$listing.Title|escape:'html'} {/title}
	{keywords} {$listing.Title|escape:'html'} {/keywords}
	{description} {$listing.Title|escape:'html'} {/description}
	{head}
		{module name="miscellaneous" function="opengraph_meta" listing_id=$listing.id}
	{/head}
	{if $errors}
		<div class="noRefine">
			{foreach from=$errors key=error_code item=error_message}
				<p class="error">
					{if $error_code == 'UNDEFINED_LISTING_ID'} [[Listing ID is not defined]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'} [[Listing does not exist]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'LISTING_IS_NOT_ACTIVE'} [[This Job is no longer available]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'NOT_OWNER'} [[You're not the owner of this posting]]
					{elseif $error_code == 'LISTING_IS_NOT_APPROVED'} [[Listing with specified ID is not approved by admin]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'WRONG_DISPLAY_TEMPLATE'} [[Wrong template to display listing]]
					{/if}
				</p>
			{/foreach}
		</div>
	{else}

	<script type="text/javascript">

	function popUpWindowIframe(url, widthWin, heightWin, title) {
		$("#messageBox").dialog( 'destroy' ).html('{$smarty.capture.displayJobProgressBar|escape:'javascript'}');
		$("#messageBox").dialog({
			width: widthWin,
			height: heightWin,
			modal: true,
			title: title
		}).dialog( 'open' );
		$("#messageBox").html('<iframe border="0" runat="server" height="650" width="750" frameborder="0" src="'+url+'"><\/iframe>');
		return false;
	}

	function windowMessage() {
		$("#messageBox").dialog( 'destroy' ).html('[[You already applied]]');
		$("#messageBox").dialog({
			bgiframe: true,
			modal: true,
			title: '[[Error]]',
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
	}

	var link = "{$GLOBALS.site_url}/flag-listing/";
	{literal}
	// send flagForm and show result
	function sendFlagForm() {
		$("#flagForm").ajaxSubmit({
			url: link,
			success: function(response, status) {
				$("#messageBox").html(response);
			}
		});
		return false;
	}
	$(function() {
		/* Textarea Hint */
		if ($("#FormBar #message").val() == '') {
			$("#FormBar #message").val('{/literal}[[Enter your note here]]{literal}');
		}
		$("#FormBar #message").focus(function(){
			if ($("#FormBar #message").val()=='{/literal}[[Enter your note here]]{literal}')
			$("#FormBar #message").val('');
		});

		$("#FormBar #message").blur(function(){
			if ($("#FormBar #message").val()=='') {
				$("#FormBar #message").val('{/literal}[[Enter your note here]]{literal}');
			}
		});

		$("#FormBar input").click(function(){
			if ($("#FormBar #message").val()=='{/literal}[[Enter your note here]]{literal}')
			$("#FormBar #message").val('');
		});
	});
	</script>
	{/literal}

	<div class="results">
		<div id="topResults">
			<!-- SAVE LISTING / PRINT LISTING -->
			<div class="searchResultsHeaderLineNew">
				<div id="header-searchres-left"></div>
				{if $GLOBALS.user_page_uri != "/job-preview/"}
					<div class="underQuickLinks">
						<div class="ModResults">
							{if $searchId != "" && $GLOBALS.user_page_uri != "/my-job-details/" && $GLOBALS.user_page_uri != "/job-preview/"}
								<ul>
									<li class="arrow"><a href="{$GLOBALS.site_url}{$search_uri}?action=search&amp;searchId={$searchId}&amp;page={$page}#listing_{$listing.id}">[[Back to Results]]</a></li>
									<li class="modifySearchIco"><a href="{$GLOBALS.site_url}/find-jobs/?searchId={$searchId}">[[Modify Search]]</a></li>
								</ul>
							{/if}
						</div>
						<div class="Rating">
							{if $show_rates && $acl->isAllowed('add_job_ratings')}
								<ul>
									<li class="ratingPanel"><p>[[Rate This Job]]: {include file="rating.tpl" listing=$listing}</p></li>
								</ul>
							{/if}
						</div>
						<div class="Comments">
							{if $show_comments && $acl->isAllowed('add_job_comments')}
								<ul><li class="comments"><a href="#comment_1">[[Comments]] (+{$listing.comments_num})</a></li></ul>
							{elseif $show_comments && $acl->getPermissionParams('add_job_comments') == "message"}
								<ul><li class="comments"><a href="#comment_1" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=add_job_comments', 400, '[[Comments]]'); return false;">[[Comments]] (+{$listing.comments_num})</a></li></ul>
							{/if}
						</div>
					</div>
				{/if}
				<!-- END MODIFY RESULTS / RATING / COMMENTS / PAGGING -->
				<div id="header-searchres-right"></div>
			</div>
			<!-- END SAVE LISTING / PRINT LISTING -->

			<!-- MODIFY RESULTS / RATING / COMMENTS / PAGGING -->
			<div class="clr"></div>
		</div>
	</div>

	<div class="clr"></div>

	<div class="after-quick-links">
		{if $GLOBALS.user_page_uri != "/job-preview/"}
			<div class="Pagging">
				{if $searchId != "" || $GLOBALS.user_page_uri == "/my-job-details/"}
					<ul>
						<li class="pagging">
							{if $prev_next_ids.prev}
								<span class="prevBtn"><a href="{$GLOBALS.site_url}/{if $GLOBALS.user_page_uri == "/my-job-details/"}my-job-details{else}display-job{/if}/{$prev_next_ids.prev}/?searchId={$searchId}&amp;page={$page}">&#171;&nbsp;[[Previous]]</a></span>
							{/if}
							{if $prev_next_ids.next}
								<span class="nextBtn"><a href="{$GLOBALS.site_url}/{if $GLOBALS.user_page_uri == "/my-job-details/"}my-job-details{else}display-job{/if}/{$prev_next_ids.next}/?searchId={$searchId}&amp;page={$page}">[[Next]]&nbsp;&#187;</a></span>
							{/if}
						</li>
					</ul>
				{/if}
			</div>
		{/if}
		<div class="clr"><br/></div>
		<ul id="listing-details-menu">
			{if $acl->isAllowed('apply_for_a_job')}
				{if $isApplied}
					{capture assign='applyBtn_onClick'}onclick="windowMessage();"{/capture}
				{else}
					{if isset($listing.ApplicationSettings.add_parameter) && $listing.ApplicationSettings.add_parameter == 2}
						{capture assign='applyBtn_onClick'}onclick="javascript:window.open('{if $listing.user.isJobg8 && $listing.jobType != 'ATS'}{$GLOBALS.site_url}/apply-now-external/?listing_id={$listing.id}{else}{$listing.ApplicationSettings.value}{/if}');"{/capture}
					{else}
						{capture assign='applyBtn_onClick'}onclick="popUpWindow('{$GLOBALS.site_url}/apply-now/?listing_id={$listing.id}&ajaxRelocate=1', 650, '<span>[[Apply Now]]</span>: {$listing.Title|addslashes|escape}')"{/capture}
					{/if}
				{/if}
			{elseif $acl->getPermissionParams('apply_for_a_job') == "message"}
				<li class="apply-now-li">{capture assign='applyBtn_onClick'}onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=apply_for_a_job', 440, '<span>[[Apply Now]]</span>: {$listing.Title|escape}')"{/capture}<li>
			{elseif $acl->getPermissionParams('apply_for_a_job') == "login_request"}
				<li class="apply-now-li">{capture assign='applyBtn_onClick'}onclick="popUpWindow('{$GLOBALS.site_url}/login/?return_url={$uri}', 440, '[[Login]]', false, false)"{/capture}</li>
			{/if}
			{if isset($applyBtn_onClick)}
				<li class="apply-now-li"><input type="button" class="buttonApply" {$applyBtn_onClick} value='[[Apply Now]]' /></li>
			{/if}
			{if $GLOBALS.user_page_uri != "/my-job-details/" && $GLOBALS.user_page_uri != "/job-preview/"}
				{if $acl->isAllowed('save_job')}
					<li class="panelSavedIco"><a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&displayForm=1', 440, '[[Save this Job]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="action"><span>[[Save this Job]]</span></a></li>
				{elseif $acl->getPermissionParams('save_job') == "message"}
					<li class="panelSavedIco"><a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 440, '[[Save this Job]]'); return false;" class="action"><span>[[Save this Job]]</span></a></li>
				{/if}
				{if $GLOBALS.current_user.logged_in}
					{if $acl->isAllowed('save_job')}
						<li class="panelViewDitailsIco"><a href="{$GLOBALS.site_url}/saved-jobs/" class="action"><span>[[View Saved Jobs]]</span></a></li>
					{elseif $acl->getPermissionParams('save_job') == "message"}
						<li class="panelViewDitailsIco"><a href="{$GLOBALS.site_url}/saved-jobs/" class="action" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_job', 440, '[[View Saved Jobs]]'); return false;"><span>[[View Saved Jobs]]</span></a></li>
					{/if}
				{else}
					<li class="panelViewDitailsIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id=job', 430, 'Saved jobs'); return false;" href="{$GLOBALS.site_url}/saved-listings"><span>[[View Saved Jobs]]</span></a></li>
				{/if}
			{/if}
			<li class="tell-a-friend"><a href="{$GLOBALS.site_url}/tell-friends/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/tell-friends/?listing_id={$listing.id}', 650, '[[Tell a Friend]]'); return false;"><span>[[Tell a Friend]]</span></a></li>
			{if $acl->isAllowed('flag_job')}
				<li class="flag-listing-ico"><a href="{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}', 500, '[[Flag This Job]]'); return false;"><span>[[Flag This Job]]</span></a></li>
			{elseif $acl->getPermissionParams('flag_job') == "message"}
				<li class="flag-listing-ico"><a href="{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=flag_job', 440, '[[Flag This Job]]'); return false;"><span>[[Flag This Job]]</span></a></li>
			{/if}
			{if $GLOBALS.user_page_uri == "/my-job-details/"}
				<li class="print-listing-ico"><a target="_blank" href="{$GLOBALS.site_url}/print-my-job/?listing_id={$listing.id}"><span>[[Print This Ad]]</span></a></li>
			{else}
				<li class="print-listing-ico"><a target="_blank" href="{$GLOBALS.site_url}/print-listing/?listing_id={$listing.id}"><span>[[Print This Ad]]</span></a></li>
			{/if}
			{if $listing.latitude && $listing.longitude && (!$GLOBALS.settings.cookieLaw || $smarty.cookies.cookiePreferences != "System")}
				<li class="viewMapIco"><a href="{$GLOBALS.site_url}/display-job-map/?listing_id={$listing.id}&amp;searchId={$searchId}&amp;view=map" onclick="popUpWindowIframe('{$GLOBALS.site_url}/display-job-map/?listing_id={$listing.id}&amp;searchId={$searchId}&amp;view=map&amp;lightbox=1', 800, 715, '[[Map]]'); return false;"><span>[[Map View]]</span></a></li>
			{/if}
		</ul>
		<div class="clr"></div>
	</div>

	<div id="listingsResults">
		<!-- LISTING INFO BLOCK -->
		<div class="listingInfo">
			<h2>{$listing.Title|escape:'html'}</h2>
			<div class="clr"></div>

			{* >>>>>>>>>>>>> FIELDS >>>>>>>>>>>>>>>> *}
			{include file="../builder/bf_displaylisting_fieldsholders.tpl"}
			{* <<<<<<<<<<<<< FIELDS <<<<<<<<<<<<<<<< *}

			<div id="refineResults" class="company-info-right">
				<!-- PROFILE BLOCK -->
				<div class="userInfo">
					<div id="blockTop"></div>
					<div class="compProfileTitle">[[Company Info]]</div>
					<div class="compProfileInfo">
						<div class="comp-profile-content">
							{if $listing.anonymous != 1 || $applications.anonymous === 0 }

								{if $listing.user.Logo.file_url || $listing.ListingLogo.file_url}
									<div class="text-center"><img src="{if $listing.ListingLogo.file_url}{$listing.ListingLogo.file_url}{else}{$listing.user.Logo.file_url}{/if}" alt="" /></div>
								{/if}
								<span class="company-name">{$listing.user.CompanyName|escape:'html'}{module name="social" function="profile_widget" companyName=$listing.user.CompanyName}</span>

									{if !$listing.user.isJobg8}
										{if $allowViewContactInfo}{$listing.user.Location.Address}{/if}
										<br />{locationFormat location=$listing.user.Location format="long"}
									{/if}
									<br/>
									<br/>
									{* Check for JobG8 listings property to post link to company profile *}
									{if $listing.user.isJobg8}
										<a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">[[Company Profile]]</a>
									{else}
										{if $allowViewContactInfo}
											<span class="company-name">[[Phone]]:</span> <span class="longtext-26">{$listing.user.PhoneNumber}</span><br/>
											<span class="company-name">[[Web Site]]:</span> <a href="{if strpos($listing.user.WebSite, 'http://') === false}http://{/if}{$listing.user.WebSite}" target="_blank"><span class="longtext-26">{$listing.user.WebSite}</span></a><br/><br/>
										{elseif $acl->getPermissionParams('view_job_contact_info') == "message"}
											{module name="miscellaneous" function="access_denied" permission="view_job_contact_info"}<br/>
										{/if}
										{if $acl->isAllowed('use_private_messages')}
											<span class="list"><a href="{$GLOBALS.site_url}/private-messages/contacts/?pm_action=save_contact&amp;user_id={$listing.user.id}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/contacts/?pm_action=save_contact&amp;user_id={$listing.user.id}', {if $GLOBALS.current_user.logged_in}400{else}500{/if}, '[[Save Contact]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pmSendLink">[[Save Contact]]</a></span><br />
										{elseif $acl->getPermissionParams('use_private_messages') == "message"}
											<span class="list"><a href="#" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 400, '<span>[[Apply Now]]</span>: {$listing.Title|escape:"html"}'); return false;"  class="pmSendLink">[[Save Contact]]</a></span><br />
										{/if}
										<span class="list"><a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">[[Company Profile]]</a></span><br/>
									{/if}
									{if !$listing.user.isJobg8}
										{if $acl->isAllowed('use_private_messages')}
											<span class="list"><a href="{$GLOBALS.site_url}/private-messages/send/?to={$listing.user.id}{if $listing.subuser}&amp;cc={$listing.subuser.id}{/if}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/aj-send/?to={$listing.user.id}&ajaxRelocate=1{if $listing.subuser}&cc={$listing.subuser.id}{/if}', 700, '[[Send private message]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pm_send_link">[[Send private message]]</a></span>
										{elseif $acl->getPermissionParams('use_private_messages') == "message"}
											<span class="list"><a href="{$GLOBALS.site_url}/private-messages/send/?to={$listing.user.id}{if $listing.subuser}&amp;cc={$listing.subuser.id}{/if}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 430, '[[Send private message]]'); return false;"  class="pm_send_link">[[Send private message]]</a></span>
										{/if}
									{/if}
							{else}
									<span class="text-center strong">[[Anonymous User Info]]</span>
							{/if}
							<br/>
							<div class="text-center">
								{foreach from=$listing.pictures key=key item=picture name=picimages }
									<br/><a class="info-picture" target="_black" href ="{$picture.picture_url}"> <img src="{$picture.thumbnail_url}" border="0" title="{$picture.caption}" alt="{$picture.caption}" /></a><br/>
								{/foreach}
							</div>
							<br/>
						</div>
						<div class="compProfileBottom"></div>
					</div>
				<!-- END PROFILE BLOCK -->
				</div>
				<div class="clr"></div>

				{if !$myListing && $GLOBALS.plugins.ShareThisPlugin.active == 1 && $GLOBALS.settings.display_on_job_page == 1}
					{$GLOBALS.settings.header_code}
					{$GLOBALS.settings.code}
				{/if}

				{module name="social" function="facebook_like_button" listing=$listing type="Job"}
				{module name="social" function="linkedin_share_button" listing=$listing}

				<div class="clr"><br/></div>

				{if $show_comments && $acl->isAllowed('add_job_comments')}
					{include file="listing_comments.tpl" listing=$listing}
				{/if}
			</div>
			<!-- END LISTING INFO BLOCK -->

			{* >>>>> LISTING PREVIEW >>>>> *}
			{if $GLOBALS.user_page_uri == '/job-preview/'}
				<div class="preview-buttons">
					<form action="{$referer}" method="post">
						<input type="hidden" name="from-preview" value="1" />
						<input type="submit" name="edit_temp_listing" value="[[Edit]]" class="button" id="listingPreview" />
						{if $contract_id == 0 && !$checkouted}
							<input type="hidden" name="proceed_to_checkout" />
							<input type="submit" name="action_add" value="[[Proceed to Checkout]]" class="button" />
						{else}
							<input type="submit" name="action_add" value="[[Post]]" class="button" />
						{/if}
					</form>
				</div>
			{/if}
			{* <<<<< LISTING PREVIEW <<<<< *}
		</div>
		<div class="clr"></div>
	</div>
	{/if}
</div>

<script type="text/javascript">
	{if $keywordsHighlight}
		$("#listingsResults").highlight({$keywordsHighlight});
	{/if}
</script>