{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-subadmins/">[[Admin Sub Accounts]]</a> &#187; {if $sid}[[Edit Admin Sub-Account]]{else}[[Create a New Admin Sub-Account]]{/if}{/breadcrumbs}
<h1>
	{if $sid}
        <img src="{image}/icons/usersminus32.png" border="0" alt="" class="titleicon"/>[[Edit Admin Sub-Account]]
	{else}
        <img src="{image}/icons/usersplus32.png" border="0" alt="" class="titleicon"/> [[Create a New Admin Sub-Account]]
	{/if}
</h1>
{include file="../users/field_errors.tpl"}

<form method="post" action="">
	<fieldset>
		<legend>[[Admin Sub-Account Info]]</legend>
		<table>
			{foreach from=$form_fields item=form_field}
			<tr>
				<td width="15%" valign=top>[[{$form_field.caption}]]</td>
				<td valign=top class="required">{if $form_field.is_required}*{/if}</td>
				<td width="85%" nowrap="nowrap"> {input property=$form_field.id}
					{if $form_field.id eq 'username'}
					<span class="note">[[You can use only alphanumeric characters for Sub-Admin Username]]</span>
					{/if}
				</td>
			</tr>
			{/foreach}
		</table>
	</fieldset>
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="subadmin" value="{$sid}" />
	<div class="clr"><br/></div>
	<fieldset id="perm_settings_fieldset">
		<legend class="title"><strong>[[Admin Sub-Account Permissions and Settings]]</strong></legend>
		<ul class="subadminPerm">
			{foreach item=group from=$groups}
				<li>
					{include file="acl_group_permissions.tpl" group=$group}
				</li>
			{/foreach}
		</ul>
        <div class="floatRight">
            {if $sid}<input type="submit" id="apply" value="[[Apply]]" class="grayButton" />{/if}
            <input class="grayButton" type="submit" value="[[Save]]" />
        </div>
	</fieldset>
</form>
<script type="text/javascript">
	$(".permissionCheck").click(function(){
		var group = $(this).attr("id");
		if($(this).attr("checked")){
			$("input[id^='"+group+"_']").each(function(){
				$(this).attr("checked","checked");
			});
		}else{
			$("input[id^='"+group+"_']").each(function(){
				$(this).removeAttr("checked");
			});
		}
	});
	$(".permGroupTitle").click(function(){
		var group = $(this).attr("id");
		var elem = $("[id='"+group+"_ul']");
		if(elem.css("display") == 'block'){
			elem.hide();
			$("[id='"+group+"_arr']").removeClass('permArrowDown').addClass('permArrow');
		}else{
			elem.show();
			$("[id='"+group+"_arr']").removeClass('permArrow').addClass('permArrowDown');
		}
	});

	$('#apply').click(
			function(){
				$("input[name='action']").attr('value', 'apply');
			}
	);
</script>
