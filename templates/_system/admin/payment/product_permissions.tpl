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

<table width="100%">
	<tr>
		<td>
			<input type="hidden" name="role" value="{$role}" />
			{if $countGeneralPermissions}
				<h3>[[General permissions]]</h3>
				{include file="../users/acl_group_permissions.tpl" group="general"}
			{/if}
					
			{foreach item=listingType from=$listingTypes}
				<h3>[[{$listingType.name} permissions]]</h3>
				{include file="../users/acl_group_permissions.tpl" group=$listingType.id}
			{/foreach}
		</td>
	</tr>
</table>