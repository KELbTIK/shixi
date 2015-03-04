<fieldset id="fieldset_{$group}">
	<legend>
	   <span class="permGroupTitle">[[{$group}]]</span>
	</legend>
	<ul id="{$group}_ul">
		{foreach item=permission from=$resources}
			{if $permission.group == $group && $permission.value eq 'allow'}
				<li class="{cycle values = 'evenrow,oddrow'}">
					<input disabled="disabled" type="checkbox" id="{$group}_{$permission.name}" name="{$permission.name}" class="permissionCheck" {if $permission.value == "allow"}checked="checked"{/if} />
				   <span class="permTitle">[[{$permission.title}]]</span>
				{if $permission.subpermissions}
					<ul>
					{foreach item=subpermission from=$permission.subpermissions}
						<li class="{cycle values = 'evenrow,oddrow'} subpermission">
								<input disabled="disabled" type="checkbox" id="{$group}_{$subpermission.name}" name="{$subpermission.name}" class="permissionCheck" {if $subpermission.value == "allow"}checked="checked"{/if} />
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