<div id="displayListing">
	{title} {$listing.Title} {/title}
	{keywords} {$listing.Title} {/keywords}
	{description} {$listing.Title} {/description}
	{if $errors}
		<div class="noRefine">
			{foreach from=$errors key=error_code item=error_message}
				<p class="error">
					{if $error_code == 'UNDEFINED_LISTING_ID'} [[Listing ID is not defined]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'} [[Listing does not exist]]
						{title} [[404 Not Found]] {/title}
					{elseif $error_code == 'LISTING_IS_NOT_ACTIVE'} [[This {$listing.type.name} is no longer available]]
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
				{if $GLOBALS.user_page_uri != "/{$listing.type.id|lower}-preview/"}
					<div class="underQuickLinks">
						<div class="ModResults">
							{if $searchId != "" && $GLOBALS.user_page_uri != "/my-{$listing.type.id|lower}-details/" && $GLOBALS.user_page_uri != "/{$listing.type.id|lower}-preview/"}
								<ul>
									<li class="arrow"><a href="{$GLOBALS.site_url}{$search_uri}?action=search&amp;searchId={$searchId}&amp;page={$page}#listing_{$listing.id}">[[Back to Results]]</a></li>
									<li class="modifySearchIco"><a href="{$GLOBALS.site_url}/search-{$listing.type.id|lower}s/?searchId={$searchId}">[[Modify Search]]</a></li>
								</ul>
							{/if}
						</div>
						<div class="Rating">
							{if $show_rates && $acl->isAllowed('add_'|cat:{$listing.type.id|lower}|cat:'_ratings')}
								<ul>
									<li class="ratingPanel"><p>[[Rate This {$listing.type.name}]]: {include file="rating.tpl" listing=$listing}</p></li>
								</ul>
							{/if}
						</div>
						<div class="Comments">
							{if $show_comments && $acl->isAllowed('add_'|cat:{$listing.type.id|lower}|cat:'_comments')}
								<ul><li class="comments"><a href="#comment_1">[[Comments]] (+{$listing.comments_num})</a></li></ul>
							{elseif $show_comments && $acl->getPermissionParams('add_'|cat:{$listing.type.id|lower}|cat:'_comments') == "message"}
								<ul><li class="comments"><a href="#comment_1" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=add_{$listing.type.id|lower}_comments', 400, '[[Comments]]'); return false;">[[Comments]] (+{$listing.comments_num})</a></li></ul>
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
		{if $GLOBALS.user_page_uri != "/{$listing.type.id|lower}-preview/"}
			<div class="Pagging">
				{if $searchId != "" || $GLOBALS.user_page_uri == "/my-{$listing.type.id|lower}-details/"}
					<ul>
						<li class="pagging">
							{if $prev_next_ids.prev}
								<span class="prevBtn"><a href="{$GLOBALS.site_url}/{if $GLOBALS.user_page_uri == "/my-{$listing.type.id|lower}-details/"}my-{$listing.type.id|lower}-details{else}display-{$listing.type.id|lower}{/if}/{$prev_next_ids.prev}/?searchId={$searchId}&amp;page={$page}">&#171;&nbsp;[[Previous]]</a></span>
							{/if}
							{if $prev_next_ids.next}
								<span class="nextBtn"><a href="{$GLOBALS.site_url}/{if $GLOBALS.user_page_uri == "/my-{$listing.type.id|lower}-details/"}my-{$listing.type.id|lower}-details{else}display-{$listing.type.id|lower}{/if}/{$prev_next_ids.next}/?searchId={$searchId}&amp;page={$page}">[[Next]]&nbsp;&#187;</a></span>
							{/if}
						</li>
					</ul>
				{/if}
			</div>
		{/if}
		<div class="clr"><br/></div>
		<ul id="listing-details-menu">
			{if $GLOBALS.user_page_uri != "/my-{$listing.type.id|lower}-details/" && $GLOBALS.user_page_uri != "/{$listing.type.id|lower}-preview/"}
				{if $acl->isAllowed('save_'|cat:{$listing.type.id|lower})}
					<li class="panelSavedIco"><a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}&displayForm=1', 440, '[[Save this {$listing.type.name}]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="action"><span>[[Save this {$listing.type.name}]]</span></a></li>
				{elseif $acl->getPermissionParams('save_'|cat:{$listing.type.id|lower}) == "message"}
					<li class="panelSavedIco"><a href="{$GLOBALS.site_url}/saved-ads/?listing_id={$listing.id}"  onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_{$listing.type.id|lower}', 440, '[[Save this {$listing.type.name}]]'); return false;" class="action"><span>[[Save this {$listing.type.name}]]</span></a></li>
				{/if}
				{if $GLOBALS.current_user.logged_in}
					{if $acl->isAllowed('save_'|cat:{$listing.type.id|lower})}
						<li class="panelViewDitailsIco"><a href="{$GLOBALS.site_url}/saved-{$listing.type.id|lower}/" class="action"><span>[[View Saved {$listing.type.name}s]]</span></a></li>
					{elseif $acl->getPermissionParams('save_'|cat:{$listing.type.id|lower}) == "message"}
						<li class="panelViewDitailsIco"><a href="{$GLOBALS.site_url}/saved-{$listing.type.id|lower}/" class="action" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=save_{$listing.type.id|lower}', 440, '[[View Saved {$listing.type.name}s]]'); return false;"><span>[[View Saved {$listing.type.name}s]]</span></a></li>
					{/if}
				{else}
					<li class="panelViewDitailsIco"><a onclick="popUpWindow('{$GLOBALS.site_url}/saved-listings/?listing_type_id={$listing.type.id|lower}', 430, 'Saved {$listing.type.name}s'); return false;" href="{$GLOBALS.site_url}/saved-listings"><span>[[View Saved {$listing.type.name}s]]</span></a></li>
				{/if}
			{/if}
			<li class="tell-a-friend"><a href="{$GLOBALS.site_url}/tell-friends/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/tell-friends/?listing_id={$listing.id}', 650, '[[Tell a Friend]]'); return false;"><span>[[Tell a Friend]]</span></a></li>
			{if $acl->isAllowed('flag_'|cat:{$listing.type.id|lower})}
				<li class="flag-listing-ico"><a href="{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}', 500, '[[Flag This {$listing.type.name}]]'); return false;"><span>[[Flag This {$listing.type.name}]]</span></a></li>
			{elseif $acl->getPermissionParams('flag_'|cat:{$listing.type.id|lower}) == "message"}
				<li class="flag-listing-ico"><a href="{$GLOBALS.site_url}/flag-listing/?listing_id={$listing.id}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=flag_{$listing.type.id|lower}', 440, '[[Flag This {$listing.type.name}]]'); return false;"><span>[[Flag This {$listing.type.name}]]</span></a></li>
			{/if}
			{if $GLOBALS.user_page_uri == "/my-{$listing.type.id|lower}-details/"}
				<li class="print-listing-ico"><a target="_blank" href="{$GLOBALS.site_url}/print-my-{$listing.type.id|lower}/?listing_id={$listing.id}"><span>[[Print This Ad]]</span></a></li>
			{else}
				<li class="print-listing-ico"><a target="_blank" href="{$GLOBALS.site_url}/print-listing/?listing_id={$listing.id}"><span>[[Print This Ad]]</span></a></li>
			{/if}
			{if $listing.latitude && $listing.longitude && (!$GLOBALS.settings.cookieLaw || $smarty.cookies.cookiePreferences != "System")}
				<li class="viewMapIco"><a href="{$GLOBALS.site_url}/display-{$listing.type.id|lower}-map/?listing_id={$listing.id}&amp;searchId={$searchId}&amp;view=map" onclick="popUpWindowIframe('{$GLOBALS.site_url}/display-{$listing.type.id|lower}-map/?listing_id={$listing.id}&amp;searchId={$searchId}&amp;view=map&amp;lightbox=1', 800, 715, '[[Map]]'); return false;"><span>[[Map View]]</span></a></li>
			{/if}
		</ul>
		<div class="clr"></div>
	</div>

	<div id="listingsResults">
		<!-- LISTING INFO BLOCK -->
		<div class="listingInfo">
			<h2>{$listing.Title}</h2>
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
									<div class="text-center-class"><img src="{if $listing.ListingLogo.file_url}{$listing.ListingLogo.file_url}{else}{$listing.user.Logo.file_url}{/if}" alt="" /></div>
								{/if}
								<span class="company-name">{$listing.user.CompanyName}{module name="social" function="profile_widget" companyName=$listing.user.CompanyName}</span>

									{if !$listing.user.isJobg8}
										{if $allowViewContactInfo}{$listing.user.Location.Address}{/if}
										<br />{locationFormat city=$listing.user.Location.City stateCode=$listing.user.Location.State_Code country=$listing.user.Location.Country}
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
										{elseif $acl->getPermissionParams('view_'|cat:{$listing.type.id|lower}|cat:'_contact_info') == "message"}
											{module name="miscellaneous" function="access_denied" permission="view_{$listing.type.id|lower}_contact_info"}<br/>
										{/if}
										{if $acl->isAllowed('use_private_messages')}
											<a href="{$GLOBALS.site_url}/private-messages/contacts/?pm_action=save_contact&amp;user_id={$listing.user.id}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/contacts/?pm_action=save_contact&amp;user_id={$listing.user.id}', {if $GLOBALS.current_user.logged_in}400{else}500{/if}, '[[Save Contact]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pmSendLink">[[Save Contact]]</a><br/>
										{elseif $acl->getPermissionParams('use_private_messages') == "message"}
											<a href="#" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 400, '<span>[[Apply Now]]</span>: {$listing.Title|escape:"html"}'); return false;"  class="pmSendLink">[[Save Contact]]</a><br/>
										{/if}
										<span class="list"><a href="{$GLOBALS.site_url}/company/{$listing.user.id}/{$listing.user.CompanyName|regex_replace:"/[\s\/\\\\]/":"-"|escape:"url"}/">[[Company Profile]]</a></span><br />
									{/if}
									{if !$listing.user.isJobg8}
										{if $acl->isAllowed('use_private_messages')}
											<span class="list"><a href="{$GLOBALS.site_url}/private-messages/send/?to={$listing.user.id}{if $listing.subuser}&amp;cc={$listing.subuser.id}{/if}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/aj-send/?to={$listing.user.id}&ajaxRelocate=1{if $listing.subuser}&cc={$listing.subuser.id}{/if}', 700, '[[Send private message]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;"  class="pm_send_link">[[Send private message]]</a></span>
										{elseif $acl->getPermissionParams('use_private_messages') == "message"}
											<span class="list"><a href="{$GLOBALS.site_url}/private-messages/send/?to={$listing.user.id}{if $listing.subuser}&amp;cc={$listing.subuser.id}{/if}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 430, '[[Send private message]]'); return false;"  class="pm_send_link">[[Send private message]]</a></span>
										{/if}
									{/if}
							{else}
									<span class="text-center-class strong-class">[[Anonymous User Info]]</span>
							{/if}
							<br/>
							<div class="text-center-class">
								{foreach from=$listing.pictures key=key item=picture name=picimages }
									<br/><a target="_black" href ="{$picture.picture_url}"> <img src="{$picture.thumbnail_url}" border="0" title="{$picture.caption}" alt="{$picture.caption}" /></a><br/>
								{/foreach}
							</div>
							<br/>
						</div>
						<div class="compProfileBottom"></div>
					</div>
				<!-- END PROFILE BLOCK -->
				</div>
				<div class="clr"></div>

				{if !$myListing && $GLOBALS.plugins.ShareThisPlugin.active == 1}
					{$GLOBALS.settings.header_code}
					{$GLOBALS.settings.code}
				{/if}

				{module name="social" function="facebook_like_button" listing=$listing type="{$listing.type.id|lower}"}
				{module name="social" function="linkedin_share_button" listing=$listing}

				<div class="clr"><br/></div>

				{if $show_comments && $acl->isAllowed('add_'|cat:{$listing.type.id|lower}|cat:'_comments')}
					{include file="listing_comments.tpl" listing=$listing}
				{/if}
			</div>
			<!-- END LISTING INFO BLOCK -->

			{* >>>>> LISTING PREVIEW >>>>> *}
			{if $GLOBALS.user_page_uri == "/{$listing.type.id|lower}-preview/"}
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
