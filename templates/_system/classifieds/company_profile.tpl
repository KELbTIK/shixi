{assign var="listing" value=$tmp_listing}
{title}{$userInfo.CompanyName}{/title}
<div class="text-center"><h1>[[Company Profile]]</h1></div>
<!-- PROFILE BLOCK -->
<div class="userInfo">
	<div class="compProfileTitle">[[Company Info]]</div>
	<div class="compProfileInfo">
		{if $userInfo.Logo.file_url}
			<div class="text-center"><img src="{$userInfo.Logo.file_url}" alt="" /></div><br/>
		{/if}
		<span class="strong">{$userInfo.CompanyName}</span>
		{if !$userInfo.isJobg8}
		{if $allowViewContactInfo}<br />{$userInfo.Location.Address}{/if}
		<br />{locationFormat location=$userInfo.Location format="long"}
		{/if}
		<br/><br/>
		{if !$userInfo.isJobg8}
			{if $allowViewContactInfo}
				<span class="strong">[[Phone]]</span>: <span class="longtext-25">{$userInfo.PhoneNumber}</span><br/>
				<span class="strong">[[Web Site]]</span>: <a href="{if strpos($userInfo.WebSite, 'http://') === false}http://{/if}{$userInfo.WebSite}" target="_blank"><span class="longtext-25">{$userInfo.WebSite}</span></a><br /><br />
			{elseif $acl->getPermissionParams('view_job_contact_info') == "message"}
				{module name="miscellaneous" function="access_denied" permission="view_job_contact_info"}<br/>
			{/if}
			{if $acl->isAllowed('use_private_messages')}
				<a href="{$GLOBALS.site_url}/private-messages/send/?to={$userInfo.id}" onclick="popUpWindow('{$GLOBALS.site_url}/private-messages/aj-send/?to={$userInfo.id}&ajaxRelocate=1', 700, '[[Send private message]]', true, {if $GLOBALS.current_user.logged_in}true{else}false{/if}); return false;" class="pm_send_link">[[Send private message]]</a>
			{elseif $acl->getPermissionParams('use_private_messages') == "message"}
				<a href="{$GLOBALS.site_url}/private-messages/send/?to={$userInfo.id}" onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=use_private_messages', 400, '[[Send private message]]'); return false;"  class="pm_send_link">[[Send private message]]</a>
			{/if}
			<br/>
		{/if}

		<script type="text/javascript"><!--

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

		--></script>
	</div>
	<div class="compProfileBottom"></div>
	<div class="text-center">
	{foreach from=$listing.pictures key=key item=picture name=picimages }
		<a target="_black" href="{$picture.picture_url}"> <img src="{$picture.thumbnail_url}" border="0" title="{$picture.caption}" alt="{$picture.caption}" /></a><br />
	{/foreach}
	</div>
</div>
<!-- END PROFILE BLOCK -->

<div class="listingInfo">
	<h2>[[Company Description]]:</h2>
	{$userInfo.CompanyDescription}
	<div class="clr"><br/></div>
	<div id="company-profile-video">{display property="video"}</div>
</div>
{module name="social" function="company_insider_widget" companyName=$companyName}
<div class="clr"><br /></div>