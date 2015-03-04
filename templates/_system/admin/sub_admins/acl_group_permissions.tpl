<fieldset id="fieldset_{$group}">
	<legend>
		<input type="checkbox" name="{$group}" id="{$group}" class="permissionCheck" style="display: inline; float: left;"/>
	   <span id="{$group}" class="permGroupTitle" style="display: inline; float: right;">[[{$group}]]</span>
	   <span class="permArrowDown" id="{$group}_arr"></span>
	</legend>
	<ul id="{$group}_ul">
		{foreach item=permission from=$resources}
			{if $permission.group == $group}
				<li class="{cycle values = 'evenrow,oddrow'}">
					<input type="checkbox" id="{$group}_{$permission.name}" name="{$permission.name}" {if $permission.value == "allow"}checked="checked"{/if} />
					<span class="permTitle">[[{$permission.title}]]</span>
				{if $permission.subpermissions}
					<ul>
					{foreach item=subpermission from=$permission.subpermissions}
						<li class="{cycle values = 'evenrow,oddrow'} subpermission">
							<input type="checkbox" id="{$group}_{$subpermission.name}" name="{$subpermission.name}" {if $subpermission.value == "allow"}checked="checked"{/if} />
							<span>[[{$subpermission.title}]]</span>
						</li>
					{/foreach}
					</ul>
				{/if}
				</li>
			{/if}
		{/foreach}
	</ul>
</fieldset>