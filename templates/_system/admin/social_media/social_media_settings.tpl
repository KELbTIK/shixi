{breadcrumbs}
	<a href="{$GLOBALS.site_url}/social-media/">[[Social Media]]</a> &#187; [[$networkName Settings]]
{/breadcrumbs}
<h1><img src="{image}/icons/gear32.png" border="0" alt="" class="titleicon"/>[[$networkName Settings]]</h1>

{foreach from=$errors item="error"}
	<p class="error">
		[[{$error}]]
	</p>
{/foreach}
{foreach from=$messages item="message"}
	<p class="message">
		{if $message == 'ACCOUNT_UPDATED'}
			[[Account is successfully updated.]]
		{else}
			[[{$message}]]
		{/if}
	</p>
{/foreach}

<div id="social-media">
	<div id="settingsPane">
		<form method="post">
			{if $network != 'twitter' && $network != 'googleplus'}
				<ul class="ui-tabs-nav">
					<li class="ui-tabs-selected"><a href="#connectionSettings"><span>[[$networkName Connect Settings]]</span></a></li>
					<li class="ui-tabs-unselect"><a href="#postJobs"><span>[[Post Jobs on $networkName]]</span></a></li>
				</ul>
			{elseif $network == 'twitter'}
				<ul class="ui-tabs-nav">
					<li class="ui-tabs-selected"><a href="#postJobs"><span>[[Post Jobs on $networkName]]</span></a></li>
				</ul>
			{elseif $network == 'googleplus'}
				<ul class="ui-tabs-nav">
					<li class="ui-tabs-selected"><a href="#connectionSettings"><span>[[$networkName Connect Settings]]</span></a></li>
				</ul>
			{/if}
			<input type="hidden" name="action" value="save_settings">
			{if $network != 'twitter'}
				<div id="connectionSettings" class="ui-tabs-panel">
					<table class="basetable" width="100%">
						<input type="hidden" name="soc_network" value="{$network}">
						<input type="hidden" name="submit" value="save">
						{foreach from=$settings item=networkSettings name=networkSettings}
							{if $networkSettings.id == "enable_job_sharing_for_users_googleplus"}
								<tr>
									<td colspan="4" class="head">[[Post Jobs on $networkName]]</td>
								</tr>
							{/if}
							<tr class="{cycle values = 'oddrow,evenrow'}
								{if $networkSettings.id == "oauth2_client_id" || $networkSettings.id == "client_secret" || $networkSettings.id == "developer_key" || $networkSettings.id == "enable_job_sharing_for_users_googleplus" || $networkSettings.id == "facebook_userGroup" || $networkSettings.id == "linkedin_userGroup" || $networkSettings.id == "li_allowPeopleSearch"}network-field-oddrow{/if}
								{if $networkSettings.id == "google_plus_userGroup" || $networkSettings.id == "fb_appID" || $networkSettings.id == "fb_appSecret" || $networkSettings.id == "li_apiKey" || $networkSettings.id == "li_secKey"}network-field-evenrow{/if}
								{if $networkSettings.id == "developer_key" || $networkSettings.id == "li_allowPeopleSearch" || $networkSettings.id == "facebook_userGroup" || $networkSettings.id == "fb_appSecret" || $networkSettings.id == "linkedin_userGroup" || $networkSettings.id == "li_secKey" || $networkSettings.id == "google_plus_userGroup"}network-field-last{/if}">
								{assign var=setting_name value=$networkSettings.id}
								<td><label for="{$networkSettings.id}">[[{$networkSettings.caption}]]</label></td>
								<td valign="top" class="required">{if $networkSettings.is_required}*{/if}</td>
								<td class="clear-border-left"
									{if $networkSettings.id !== "oauth2_client_id" && $networkSettings.id !== "client_secret" && $networkSettings.id !== "developer_key" &&
										$networkSettings.id !== "google_plus_userGroup" && $networkSettings.id !== "enable_job_sharing_for_users_googleplus" &&
										$networkSettings.id !== "fb_appID" && $networkSettings.id !== "fb_appSecret" && $networkSettings.id !== "facebook_userGroup" &&
										$networkSettings.id !== "li_secKey" && $networkSettings.id !== "li_apiKey" && $networkSettings.id !== "linkedin_userGroup" && $networkSettings.id !== "li_allowPeopleSearch"
										}colspan="2"{/if}>
									{$pluginSetting.tabName.id}
									{if $networkSettings.type == 'boolean'}
										<input type="hidden" name="{$setting_name}" value="0" /><input type="checkbox" id="{$networkSettings.id}" name="{$setting_name}" value="1" {if $savedSettings.$setting_name}checked="checked" {/if} />
									{elseif  $networkSettings.type == 'string'}
										<input type="text" name="{$networkSettings.id}" value="{$savedSettings.$setting_name|escape:'html'}" id="{$networkSettings.id}" />
									{elseif  $networkSettings.type == 'text'}
										<textarea name="{$networkSettings.id}" id="{$networkSettings.id}">{$savedSettings.$setting_name|escape:'html'}</textarea>
									{elseif  $networkSettings.type == 'integer'}
										<input type="text" class="inputInteger" value="{$savedSettings.$setting_name}" name="{$networkSettings.id}" id="{$networkSettings.id}"/>
									{elseif  $networkSettings.type == 'list'}
										<select name="{$networkSettings.id}" id="{$networkSettings.id}">
											<option value="">[[Please Select Item]]:</option>
											{foreach from=$networkSettings.list_values item=list}
												<option value="{$list.id}" {if $savedSettings.$setting_name == $list.id}selected="selected" {/if}>{$list.caption}</option>
											{/foreach}
										</select>
									{elseif  $networkSettings.type == 'multilist'}
										<select name="{$networkSettings.id}[]" multiple="multiple" id="{$networkSettings.id}">
											<option value="">[[Please Select Items]]:</option>
											{assign var=selectedItems value=$savedSettings.$setting_name}
											{foreach from=$networkSettings.list_values item=list}
												<option value="{$list.id}" {if (is_array($selectedItems) && in_array($list.id, $selectedItems)) || (!is_array($selectedItems) && in_array($list.id, explode(',', $selectedItems)))}selected{/if}>[[{$list.caption}]]</option>
											{/foreach}
										</select>
									{/if}
								</td>
								{if $networkSettings.id == "oauth2_client_id"}
									<td rowspan="3" class="comment-td">
										[[{$networkSettings.comment}]]
									</td>
								{elseif $networkSettings.id == "fb_appID" || $networkSettings.id == "li_apiKey"}
									<td rowspan="2" class="comment-td">
										[[{$networkSettings.comment}]]
									</td>
								{elseif $networkSettings.id == "google_plus_userGroup" || $networkSettings.id == "enable_job_sharing_for_users_googleplus" || $networkSettings.id == "facebook_userGroup" || $networkSettings.id == "linkedin_userGroup" || $networkSettings.id == "li_allowPeopleSearch"}
									<td class="comment-td">
										[[{$networkSettings.comment}]]
									</td>
								{/if}
							</tr>
						{/foreach}
					</table>
				</div>
			{/if}
			{if $network != 'googleplus'}
				<div id="postJobs" class="ui-tabs-panel" {if $network == 'twitter'}style="border-top: 1px solid #BEC4C8;"{/if}>
					<div style="vertical-align: middle;">
						[[Enable job sharing for users]]
						{capture name="jobSharingSettingName"}enable_job_sharing_for_users_{$network|lower}{/capture}
						<input type="hidden" name="{$smarty.capture.jobSharingSettingName}" value="0" />
						<input type="checkbox" id="{$networkSettings.id}" name="{$smarty.capture.jobSharingSettingName}" value="1"{if $savedSettings.{$smarty.capture.jobSharingSettingName}} checked="checked"{/if} />
						<br />
						<small>[[Users allowed to post jobs on Social Networks will be able to post jobs on $networkName.]]</small>
					</div>
					<br />
					<p><a href="{$GLOBALS.site_url}/social-media/?action=add_feed&amp;soc_network={$network}" class="grayButton">[[Add New Job Feed]]</a></p>

					{if $networkFeeds}
						<br />
						<table>
							<thead>
								<tr>
									<th>[[Feed Name]]</th>
									<th>[[Account name]]</th>
									<th class="actions">[[Actions]]</th>
									<th>[[Status]]</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$networkFeeds item=feed}
									{if $feed.active == 0}
										{assign var="stat" value="off"}
										{assign var="action" value="1"}
										{assign var="title" value="[[Not active. Click to activate]]"}
									{else}
										{assign var="stat" value="on"}
										{assign var="action" value="0"}
										{assign var="title" value="[[Active. Click to deactivate]]"}
									{/if}
									<tr class="{cycle values = 'evenrow,oddrow'}">
										<td width="30%">
											{$feed.feed_name}
											{if $feed.expired}
												<br />
												<small class="color-red">
													[[The access token for this feed is expired. To activate the feed you need to refresh the token by clicking "Grant Permission" button.]]
												</small>
											{/if}
										</td>
										<td width="30%">{$feed.account_id}</td>
										<td nowrap="nowrap">
											<a href="{$GLOBALS.site_url}/social-media/?action=edit_feed&amp;soc_network={$network}&amp;sid={$feed.sid}" class="editbutton">[[Edit]]</a>&nbsp;
											<a href="{$GLOBALS.site_url}/social-media/?action=delete_feed&amp;soc_network={$network}&amp;sid={$feed.sid}" onclick="return confirm('Are you sure?');" class="deletebutton">[[Delete]]</a>&nbsp;
											<a href="{$GLOBALS.site_url}/social-media/?action=run_manually&amp;soc_network={$network}&amp;sid={$feed.sid}" onclick="{if $feed.expired}return false;{/if} runManually('{$feed.sid}', '{$network}', 300, 500); return false;" class="editbutton greenbtn">[[Run]]</a>&nbsp;
											<a href="{$GLOBALS.site_url}/social-media/?action=authorize&amp;sub_action=grant&amp;soc_network={$network}&amp;sid={$feed.sid}" {if $feed.expired}class="redButton"{else}class="grayButton"{/if}>[[Grant permission]]</a>
										</td>
										<td>
											{if $feed.expired}
												<img title="{$title}" border=0 src="{image}off.gif">
											{else}
												<a href="?action=status&amp;active={$action}&amp;sid={$feed.sid}"><img title="{$title}" border=0 src="{image}{$stat}.gif"></a>
											{/if}
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					{/if}
				</div>
			{/if}
			<div class="clr"><br/></div>
			<div style="width: 900px;">
				<div class="floatRight" style="text-align: right;">
					<input type="submit" value="[[Apply]]" class="grayButton" id="applySettings"/>
					<input type="submit" class="grayButton" value="[[Save]]" />
				</div>
			</div>
		</form>
	</div>
</div>
<div id="runManually"></div>
{assign var="Close" value="[[Close]]"}
{assign var="trRunManually" value="[[Run Manually]]"}
<script type="text/javascript">
	$("#settingsPane").tabs();

	function runManually(sid) {
		url = "{$GLOBALS.admin_site_url}/system/classifieds/{$network}/";
		$("#runManually").dialog( 'destroy' ).html('<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]');
		$("#runManually").dialog({
			width: 500,
			height: 300,
			title: '{$trRunManually|escape}',
			buttons: {
				Ok: function() {
					$('#runManually').dialog( 'destroy' ).html('<img style="vertical-align: middle;" src="{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]');
					$('#runManually').dialog({
						width: 500,
						height: 300,
						title: '{$trRunManually|escape}',
						buttons: {
							'{$Close|escape}': function() {
								$(this).dialog('close');
							}
						}
					}).dialog( 'open' );

					$.post(url, { action: "run_manually", sid: sid }, function(data) {
						$("#runManually").html(data);
					});
				},
				'{$Close|escape}': function() {
					$(this).dialog('close');
				}
			}
		}).dialog( 'open' );

		$.post(url, { action: "run_manually_check", sid: sid }, function(data) {
			if (data.match(/^0[^\d]+/)) {
				$(".ui-dialog-buttonpane button:eq(0)").hide();
			}
			$("#runManually").html(data);
		});
	}

	$('#applySettings').click(
		function(){
			$('input[name="submit"]').attr('value', 'apply');
		}
	);
</script>
