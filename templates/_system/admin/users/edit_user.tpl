{if $GLOBALS.is_ajax}
	<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/themes/green/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
	    
	<script >
	
	var url = "{$GLOBALS.site_url}/edit-user/";
	

	$("#editUserForm").submit(function() {
		var options = {
			target: "#messageBox",
			url:  url,
			succes: function(data) {
				$("#messageBox").html(data).dialog({ width: 200});
			}
		};
		$(this).ajaxSubmit(options);
		return false;
	});
	</script>
{/if}

{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-users/{$user_group_info.id|lower|lower}/{if $restore}?restore=1{/if}">[[Manage {if $user_group_info.id == 'Employer' || $user_group_info.id == 'JobSeeker'}{$user_group_info.name}s{else}'{$user_group_info.name}' Users{/if}]]</a> &#187; [[Edit {$user_group_info.name}]]{/breadcrumbs}
<h1><img src="{image}/icons/users32.png" border="0" alt="" class="titleicon"/> [[Edit {$user_group_info.name}]]</h1>

<p>
	{foreach from=$listingTypes item=listingType}
		<a href="{$GLOBALS.site_url}/add-listing/?listing_type_id={$listingType.id}&username={$user_info.username}&edit_user=1" class="grayButton">[[Add New {$listingType.name}]]</a>
	{/foreach}
    <a href="{$GLOBALS.site_url}/system/applications/view/?user_sid={$user_info.sid}" class="grayButton">[[Manage Applications]]</a>
    <a href="{$GLOBALS.site_url}/user-products/?user_sid={$user_info.sid}" class="grayButton">[[Manage User Products]]</a>
    <a href="{$GLOBALS.site_url}/private-messages/pm-main/?user_sid={$user_info.sid}" class="grayButton">[[Manage Personal messages]]</a>
    <a href="{$GLOBALS.site_url}/system/users/acl/?type=user&amp;role={$user_info.sid}" class="grayButton">[[View Permissions]]</a>
    <a href="{$GLOBALS.site_url}/email-log/?user_sid={$user_info.sid}" class="grayButton">[[View Email Log]]</a>
	<a href="{$GLOBALS.site_url}/add-invoice/?user_sid={$user_info.sid}" class="grayButton" >[[Create Invoice]]</a>
</p>
{include file='field_errors.tpl'}
<br/>
<fieldset>
	<legend>[[User Info]]</legend>
	<form method="post" enctype="multipart/form-data" id="editUserForm">
		{set_token_field}
		<input type="hidden" id="action_name" name="action_name" value="save_info" />
		<table>
            {if $parent_name}
                <tr>
                    <td>
                        [[Sub-user of]] <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$user_info.parent_sid}" title="[[Edit]]">[[{$parent_name}]]</a>
                    </td>
                </tr>
            {/if}
			{foreach from=$form_fields item=form_field}
			{if $form_field.type == "video"}
				<tr>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>					<td >{input property=$form_field.id template="video_profile.tpl"}</td>
				</tr>
			{else}
				<tr>
					<td valign="top">[[{$form_field.caption}]]</td>
					<td valign="top" class="required">{if $form_field.is_required}*{/if}</td>
					<td>
						<div style="float: left;">{input property=$form_field.id}</div>
						{if in_array($form_field.type, array('multilist'))}
							<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
						{/if}
					</td>
				</tr>
			{/if}
			{/foreach}
			<tr>
				<td valign="top">IP</td>
				<td valign="top"></td>
				<td>{if $user_info.ip_is_banned}<a href="{$GLOBALS.site_url}/banned-ips/" target="_blank" class="required">{$user_info.ip}</a>{else}{$user_info.ip}{/if}</td>
			</tr>
			<tr>
				<td colspan="3">
                    <div class="floatRight">
                        <input type="hidden" name="user_sid" value="{$user_info.sid}" />
                        <input type="submit" id="apply" value="[[Apply]]" class="grayButton" />
                        <input type="submit" value="[[Save]]" class="grayButton" />
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script>
	$('#apply').click(
		function() {
			$('#action_name').attr('value', 'apply_info');
		}
	);
</script>