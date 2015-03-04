{breadcrumbs}<a href="{$GLOBALS.site_url}/user-groups/">[[User Groups]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-group/?sid={$user_group_sid}">[[{$user_group_info.name}]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile/?user_group_sid={$user_group_sid}">[[Edit User Profile Fields]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-user-profile-field/?sid={$field_sid}&amp;user_group_sid={$user_group_sid}">[[{$field_info.caption}]]</a> &#187; [[Edit Tree]]{/breadcrumbs}
<h1><img src="{image}/icons/linedpaperpencil32.png" border="0" alt="" class="titleicon" /> [[Edit Tree]]</h1>
<script type="text/javascript">
	function windowMessage(){
		$("#messageBox").dialog( 'destroy' ).html('<textarea name="list" id="list"></textarea>');
		$("#messageBox").dialog({
			width: 500,
			height: 500,
			title: '[[Add multiple values]]',
				buttons: {
				Add: function() {
					document.getElementById('add_values').action.value = "add_multiple";
					document.getElementById('add_values').tree_multiItem_value.value = document.getElementById('list').value;
					document.getElementById('add_values').submit();
				},
				Cancel: function(){
					$(this).dialog('close');
				}
			}

		}).dialog( 'open' );
		return false;
	}
</script>
{foreach from=$field_errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'{$field_caption}' [[is empty]]</p>
	{/if}
{/foreach}

{if $node_info.sid}
<fieldset class="inlineField">
	{assign var=previous_level value=$current_level-1}
	<legend>[[Edit {if $field_info.levels_captions.$previous_level ne ''}{$field_info.levels_captions.$previous_level}{else}Tree Value{/if}]]</legend>
	<form method="post" action="">
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="field_sid" value="{$field_sid}"/>
		<input type="hidden" name="node_sid" value="{$node_sid}"/>
		<input type="hidden" name="user_group_sid" value="{$user_group_sid}"/>
		<table>
			<tr>
				<td>[[Value]] </td>
				<td class="required">*</td>
				<td><input type="text" name="tree_item_value" value="{$node_info.caption|escape}"/></td>
			</tr>
			<tr>
				<td rowspan="3">[[Move]]</td>
				<td colspan="2"><input type="radio" name="order" value="begin" id="move_order_begin" /><label for="move_order_begin">[[to the beginning]]</label></td>
			</tr>
			<tr>
				<td colspan="2"><input type="radio" name="order" value="end" id="move_order_end" /><label for="move_order_end">[[to the end]]</label></td>
			</tr>
			<tr>
				<td colspan="2">
					<nobr>
						<input type="radio" name="order" value="after" id="move_order_after" /><label for="move_order_after">[[after]]</label>
						<select name="after_tree_item_sid" onclick="document.getElementById('move_order_after').checked = true">
							{foreach from=$tree_parent_items item=tree_value key=sid}
								{if $sid != $node_sid}
									<option value="{$sid}">{$tree_value}</option>
								{/if}
							{/foreach}
						</select>
					</nobr>
				</td>
			</tr>
			<tr>
				<td colspan="3"><span class="greenButtonEnd"><input type="submit" value="[[Save]]" class="greenButton" /></span></td>
			</tr>
		</table>
	</form>
</fieldset>
{/if}

{if $node_info.node_path|count < 5}
	<fieldset  class="inlineField">
		<legend>[[Add a New {if $field_info.levels_captions.$current_level ne ''}{$field_info.levels_captions.$current_level}{else}Tree Value{/if}]]</legend>
		<form method="post" action="" id="add_values">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="field_sid" value="{$field_sid}" />
			<input type="hidden" name="node_sid" value="{$node_sid}" />
			<input type="hidden" name="user_group_sid" value="{$user_group_sid}" />
			<table>
				<tr>
					<td>[[Value]] </td>
						<td><input type="text" name="tree_item_value" /><span class="required">*</span></td>
						<textarea name="tree_multiItem_value" style="display:none"></textarea>
				</tr>
				<tr>
					<td colspan="3"><input type="radio" name="order" value="begin" id="order_begin" /><label for="order_begin">[[to the beginning]]</label></td>
				</tr>
				<tr>
					<td colspan="3"><input type="radio" name="order" value="end" checked id="order_end" /><label for="order_end">[[to the end]]</label></td>
				</tr>
				<tr>
					<td colspan="3"><input type="radio" name="order" value="after" id="order_after" /><label for="order_after">[[after]]</label>
						<select name="after_tree_item_sid" onclick="document.getElementById('order_after').checked = true">
							{foreach from=$tree_items item=tree_value key=sid}
								<option value="{$sid}">{$tree_value}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<input type="submit" value="[[Add]]" class="greenButton"/>
						<input type="button" value="[[Add multiple values]]" class="grayButton" onClick="windowMessage();"/>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
{/if}
<div class="clr"><br/></div>

<p class="strong">
	{foreach from=$node_info.node_path item=node key=level name=node_path_block}
		{if $smarty.foreach.node_path_block.iteration > 1} / {/if}
		{if $smarty.foreach.node_path_block.iteration < $smarty.foreach.node_path_block.total}
			<a href="?field_sid={$field_sid}&amp;node_sid={$node.sid}&amp;user_group_sid={$user_group_sid}">[[{$node.caption}]]</a>
		{else}
			[[{$node.caption}]]
		{/if}
	{/foreach}
</p>

{if $node_info.node_path|count < 5}
	<table>
		<thead>
			<tr>
				<th>[[Tree Values]]</th>
				<th colspan="4" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		{foreach from=$tree_items item=tree_value key=sid name=items_block}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>{$tree_value}</td>
				<td><a href="?field_sid={$field_sid}&amp;node_sid={$sid}&amp;user_group_sid={$user_group_sid}" title="[[Edit]]" class="editbutton">[[Edit]]</td>
				<td><a href="?field_sid={$field_sid}&amp;node_sid={$node_sid}&amp;action=delete&amp;item_sid={$sid}&amp;user_group_sid={$user_group_sid}" onclick="return confirm('[[Are you sure you want to delete this?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				<td>
					{if $smarty.foreach.items_block.iteration < $smarty.foreach.items_block.total}
						<a href="?field_sid={$field_sid}&amp;node_sid={$node_sid}&amp;action=move_down&amp;item_sid={$sid}&amp;user_group_sid={$user_group_sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
					{/if} 
				</td>
				<td>
					{if $smarty.foreach.items_block.iteration > 1}
						<a href="?field_sid={$field_sid}&amp;node_sid={$node_sid}&amp;action=move_up&amp;item_sid={$sid}&amp;user_group_sid={$user_group_sid}"><img src="{image}b_up_arrow.gif" border="0" alt=""/></a>
					{/if} 
				</td>
			</tr>
		{/foreach}
	</table>
{/if}