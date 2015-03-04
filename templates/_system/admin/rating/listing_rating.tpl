{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-{$listingType.link}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing_id}">[[Edit Listing]]</a>
	&#187; [[Listing Rating]]
{/breadcrumbs}
<h1>[[Listing Rating]]</h1>
<script>
	var total = {$rating_num}

	function check_all() {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = true;
		}
	};
	
	function uncheck_all() {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = false;
		}
	};
</script>

<form method="post" name="rating" action="{$GLOBALS.site_url}/listing-rating/">
	<input type="hidden" name="action" />
	<input type="hidden" name="listing_id" value="{$listing_id}" />
	<p>
		<a href="#" onclick="check_all(); return false">[[Check all]]</a> / <a href="#" onclick="uncheck_all(); return false">[[Uncheck all]]</a><br />
		[[Actions with Selected]]:
		<input onClick="if(!confirm('[[Are you sure you want to delete these ratings]]?')) return false; document.forms['rating'].elements['action'].value = 'delete'; return true" value="[[Delete]]" class="deletebutton" type="submit">
	</p>
	
	<table>
		<thead>
			<tr>
				<th align="center">#</th>
				<th>[[Author]]</th>
				<th>[[Date published]]</th>
				<th>[[Rating]]</th>
				<th colspan="1" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$rating item=value name=each_rating}
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td align="center"><input type="checkbox" name="rating[{$value.id}]" id="checkbox_{$smarty.foreach.each_rating.iteration}" /></td>
					<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$value.user_sid}">{$value.username}</a></td>
					<td>{$value.ctime|date_format:"%d.%m.%Y, %H:%M"}</td>
					<td>{$value.vote}</td>
					<td><a href="{$GLOBALS.site_url}/listing-rating/?action=Delete&amp;rating_id={$value.id}" onClick="return confirm('[[Are you sure you want to delete this rating]]?')" title="[[Delete]]"><img src="{image}delete.png" border=0 alt="[[Delete]]"></a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	
	<p>
		<a href="#" onclick="check_all();return false">[[Check all]]</a> / <a href="#" onclick="uncheck_all();return false">[[Uncheck all]]</a><br />
		[[Actions with Selected]]:
		<input onClick="if(!confirm('[[Are you sure you want to delete this rating]]?')) return false; document.forms['rating'].elements['action'].value = 'delete'; return true" value="[[Delete]]" class="deletebutton" type="submit">
	</p>
</form>