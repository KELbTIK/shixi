{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; [[{$user_group_info.id}]]{/breadcrumbs}
{assign var="switchColumns" value=false}
<h1><img src="{image}/icons/users32.png" border="0" alt="" class="titleicon"/> [[Edit User Group]]</h1>
<p>
	<a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group_sid}" class="grayButton">[[Edit User Profile Fields]]</a>
	&nbsp;<a href="{$GLOBALS.site_url}/system/users/acl/?type=group&amp;role={$user_group_sid}" class="grayButton">[[Manage Permissions]]</a>
</p>

{foreach from=$errors key=error item=message}
	{if $error eq "USER_GROUP_SID_NOT_SET"}
	<p class="error">[[User group SID is not set]]</p>
	{/if}
{/foreach}
<fieldset>
	<legend>[[User Group Info]]</legend>
	<form method="post" action="">
		<input type="hidden" id="submit" name="submit" value="save_info" />
		<input type="hidden" name="sid" value="{$object_sid}" />

		{include file="user_group_form_fields.tpl"}

		<div class="clr"><br/></div>
		<div class="floatRight">
			<input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
			<input type="submit" value="[[Save]]" class="grayButton" />
		</div>
	</form>
</fieldset>

<div  class="setting_block" style="display: none"></div>
<div class="clr"><br/></div>

<h2>[[Products]]</h2>
<table>
	<thead>
		<tr>
			<th>[[ID]]</th>
			<th>[[Name]]</th>
			<th>[[User number]]</th>
			<th>[[Default]]</th>
			<th colspan="2">[[Order]]</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$user_group_products_info item=products_info  name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$products_info.sid}</td>
				<td><a href="{$GLOBALS.site_url}/edit-product/?sid={$products_info.sid}">{$products_info.name}</a></td>
				{assign var="product_sid" value=$products_info.sid}
				<td>{$user_group_product_user_number.$product_sid}</td>
				<td><input class="default-plan" value="{$products_info.sid}" type="checkbox" {if $user_group_info.default_product == $products_info.sid}checked="checked"{/if}" /></td>
				<td>
					{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
						<a href="?product_sid={$products_info.sid}&amp;action=move_down&amp;sid={$user_group_sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
					{/if}
				</td>
				<td>
					{if $smarty.foreach.items_block.iteration > 1}
						<a href="?product_sid={$products_info.sid}&amp;action=move_up&amp;sid={$user_group_sid}"><img src="{image}b_up_arrow.gif" border="0" alt="" /></a>
					{/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<p>[[Users of this group will be automatically subscribed (free of charge) to the product marked as <strong>Default</strong> after registration]]</p>

<script type="text/javascript">
$(document).ready(function() {
	$('.default-plan').change( function() {
		var plan = 0;
		if (this.checked) {
			plan = this.value
		}
		location.href = '?product_sid=' + plan + '&action=set_default_product&sid={$user_group_sid}';
	});

	$('#apply').click(function(){
				$('#submit').attr('value', 'apply_info');
		}
	);

	$('input[name="id"]').attr("disabled","disabled").after('<div style="font-size:11px;margin-top:5px">[[This is a system field. It cannot be changed.]]</div>');
});
</script>