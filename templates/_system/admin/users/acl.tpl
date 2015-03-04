<script type="text/javascript">
<!--
	function viewPermission(el, value)
    {
    	var amountDiv = '#' + el.name + '_amountPermissions';
    	var typeDiv = '#' + el.name + '_typePermissions';
    	var userGroupPerm = '#' + el.name + '_userGroup';
    	var messageDiv = '#' + el.name + '_messagePermissions';

    	if (el.tagName == 'INPUT') {
    		if (el.checked) {
    			$(amountDiv).show();
    			$(typeDiv).hide();
    		}
    		else {
    			$(amountDiv).hide();  
    			$(typeDiv).show();
    		}
    	}
    	else {
        	switch (el.value) {
        		case 'inherit':
            		$(amountDiv).hide();
            		$(typeDiv).hide();
            		break;
        		case 'allow':
            		$(amountDiv).show();
            		$(typeDiv).hide();
            		break;
        		case 'deny':
        			$(amountDiv).hide();
        			$(typeDiv).show();
            		break;
        	}
    	}
    	if ($(typeDiv).css('display') == 'block') {
    		if ($(typeDiv +' input[type=radio]:checked').val() == "message")
        		$(messageDiv).show();	
    		else
    			$(messageDiv).hide();	
        }
    	else {
    		$(messageDiv).hide();
    	}
	}

    $(document).ready(function () {
        $(".permissionSelect").each(function () {
        	viewPermission(this, this.value);
        });
    });

	function viewMessage(p_name)
    {
		var typeDiv = '#' + p_name + '_typePermissions';
		var messageDiv = '#' + p_name + '_messagePermissions';
		if ($(typeDiv +' input[type=radio]:checked').val() == "message")
			$(messageDiv).show();	
		else
			$(messageDiv).hide();
	}

//-->
</script>

{breadcrumbs}
    {if $type == 'user'}
    	<a href="{$GLOBALS.site_url}/manage-users/{$userGroupInfo.id|lower}/">[[Manage {if $userGroupInfo.id == 'Employer' || $userGroupInfo.id == 'JobSeeker'}{$userGroupInfo.name}s{else}'{$userGroupInfo.name}' Users{/if}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user/?user_sid={$role}">[[Edit {$userGroupInfo.name}]]</a> &#187; [[View Permissions]]
    {elseif $type == 'group'}
    	<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$role}">[[{$userGroupInfo.name}]]</a> &#187; [[Manage {if $type == 'group'}{$userGroupInfo.name} {/if}Permissions]]
    {elseif $type == 'guest'}
    	<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; [[Manage Guest Permissions]]
    {elseif $type == 'plan'}
    	<a href="{$GLOBALS.site_url}/membership-plans/">[[Membership Plans]]</a> &#187; <a href="{$GLOBALS.site_url}/membership-plan/?id={$role}">[[{$membershipPlanInfo.name}]]</a> &#187; [[Manage {$membershipPlanInfo.name} Permissions]]
    {/if}
{/breadcrumbs}
<h1>
	{if $type == "user"}
        <img src="{image}/icons/contactcard32.png" border="0" alt="" class="titleicon"/> [[View]]
    {else}
        <img src="{image}/icons/contactcard32.png" border="0" alt="" class="titleicon"/> [[Manage]]
    {/if}
	{if $type == 'group'}[[{$userGroupInfo.name}]]{elseif $type == 'guest'}[[Guest]]{elseif $type == 'plan'}[[{$membershipPlanInfo.name}]]{/if} [[Permissions]]
</h1>
<div style="width: 700px;	display: block;">
	<form method="post" action="{$GLOBALS.site_url}/system/users/acl/">
		<input type="hidden" id="action" name="action" value="save" />
		<input type="hidden" name="type" value="{$type}" />
		<input type="hidden" name="role" value="{$role}" />
		<h3>[[General permissions]]</h3>
		{include file="acl_group_permissions.tpl" group="general"}
		
		{foreach item=listingType from=$listingTypes}
			<h3>[[{$listingType.name} permissions]]</h3>
			{include file="acl_group_permissions.tpl" group=$listingType.id}
		{/foreach}
	
		{if $type != 'user'}
	    	{if $type == 'plan'}
	        	<table width="100%" id="clear">
	        		<tr>
	        			<td  width="100%" style="text-align: right;"><small><b>[[Apply changes to all users currently subscribed to this plan]]</b></small></td><td align="right" ><input type="radio" name="update_users" value="1" checked="checked" /></td>
	        		</tr>
	        		<tr>
	        			<td  style="text-align: right;"><small><b>[[Changes will be applied to newly subscribed users only]]</b></small></td><td align="right" ><input type="radio" name="update_users" value="0"></td>
	        		</tr>
	        	</table>
	    	{/if}
			<br/>
            <div class="floatRight">
                <input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
                <input type="submit" value="[[Save]]" class="grayButton" />
            </div>
		{/if}
	</form>
</div>

<script>
	$('#apply').click(
		function() {
			$('#action').attr('value', 'apply_info');
		}
	);
</script>