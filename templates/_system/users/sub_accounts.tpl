{if $errors}
	{foreach item='error' from=$errors}
		<div class="error alert alert-danger">
			{if $error == 'ACCESS_DENIED'}
				[[You don't have permissions to access this page.]]
			{else}
				{$error}
			{/if}
		</div>
	{/foreach}
{else}
	{if $isSubuserRegistered}
		<div class="message alert alert-success">[[Sub-user registered successfully]]</div>
	{/if}

	<div id="sub-accounts">
        <br/>
		<a class="btn btn-dark" href="{$GLOBALS.site_url}/sub-accounts/new/">[[Create New Account]]</a>

		<form method="post" action="{$GLOBALS.site_url}/sub-accounts/">
			<input type="hidden" name="action_name" value="delete" />
			[[Actions with Selected]]: &nbsp;
			<input type="submit" name="action_delete" value="[[Delete]]" class="button btn btn-danger" onclick="return confirm('[[Are you sure?]]')" />
			<div class="clearfix"></div>

			<div class="results table-responsive">
				<table class="table table-condensed table-striped">
					<thead>
						<tr>
							<th class="tableLeft"> </th>
							<th width="1%"><input type="checkbox" id="all_checkboxes_control" /></th>
							<th width="50%">{if !$isEmailAsUsername}[[Username]]{/if}</th>
							<th>[[Email]]</th>
							<th colspan="2">[[Actions]]</th>
							<th class="tableRight"> </th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$subusers item=subuser name=subuser_block}
							<tr {if $listing.priority == 1}class="priorityListing"{else}class="{cycle values = 'evenrow,oddrow'}"{/if}>
								<td> </td>
								<td><div>&nbsp;</div><input type="checkbox" name="user_id[]" value="{$subuser.sid}" id="checkbox_{$smarty.foreach.subuser_block.iteration}" /></td>
								<td><div>&nbsp;</div>{if !$isEmailAsUsername}<a href="{$GLOBALS.site_url}/sub-accounts/edit/?user_id={$subuser.sid}"><span class="strong"><span class="longtext-45">{$subuser.username}</span></span></a>{/if}</td>
								<td><div>&nbsp;</div><a href="{$GLOBALS.site_url}/sub-accounts/edit/?user_id={$subuser.sid}"><span class="strong"><span class="longtext-35">{$subuser.email}</span></span></a></td>
								<td><a class="btn btn-warning btn-sm radius" href="{$GLOBALS.site_url}/sub-accounts/edit/?user_id={$subuser.sid}">[[Edit]]</a></td>
								<td><a class="btn btn-danger btn-sm radius" href="{$GLOBALS.site_url}/sub-accounts/?action_name=delete&amp;user_id[]={$subuser.sid}" onclick="return confirm('[[Are you sure?]]')">[[Delete]]</a></td>
								<td> </td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</form>
	</div>

	<script type="text/javascript">
		var total = {$smarty.foreach.subuser_block.total};
		{literal}
		function set_checkbox(param) {
			for (i = 1; i <= total; i++) {
				if (checkbox = document.getElementById('checkbox_' + i))
					checkbox.checked = param;
			}
		}
		
		$("#all_checkboxes_control").click(function() {
			set_checkbox(this.checked);
		});
		{/literal}
	</script>
{/if}