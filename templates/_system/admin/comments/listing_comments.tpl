{breadcrumbs}
	<a href="{$GLOBALS.site_url}/manage-users/{$listingType.id}/?restore=1">
		[[Manage {$listingType.name}s]]
	</a>
	&#187; <a href="{$GLOBALS.site_url}/edit-listing/?listing_id={$listing_id}">[[Edit Listing]]</a>
	&#187; [[Listing Comments]]
{/breadcrumbs}
<h1>[[Listing Comments]]</h1>
<script>
	var total = {$comments_num}

	function check_all() {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = true;
		}
	}
	
	function uncheck_all() {
		for (i = 1; i <= total; i++) {
			if (checkbox = document.getElementById('checkbox_' + i))
				checkbox.checked = false;
		}
	}
</script>
<form method="post" name="comments" action="{$GLOBALS.site_url}/listing-comments/">
	<input type="hidden" name="action" />
	<input type="hidden" name="listing_id" value="{$listing_id}" />
	<a href="#" onclick="check_all(); return false">[[Check all]]</a> / <a href="#" onclick="uncheck_all(); return false">[[Uncheck all]]</a>
	<br/>[[Actions with Selected]]:
	<div class="clr"></div>
	<span class="greenButtonInEnd"><input onClick="document.forms['comments'].elements['action'].value = 'enable'" value="[[Enable]]" class="greenButtonIn" type="submit"></span>
	<span class="greenButtonInEnd"><input onClick="document.forms['comments'].elements['action'].value = 'disable'" value="[[Disable]]" class="greenButtonIn" type="submit"></span>
	<input onClick="if(!confirm('[[Are you sure you want to delete this comments?]]')) return false; document.forms['comments'].elements['action'].value = 'delete'; return true" value="[[Delete]]" class="deleteButton" type="submit">
	<div class="clr"><br/></div>
	<table>
		<thead>
			<tr>
				<th align="center">#</th>
				<th>[[Author]]</th>
				<th>[[Date published]]</th>
				<th>[[Message]]</th>
				<th colspan="3" class="actions">[[Actions]]</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$comments item=comment name=each_comment}
				<tr class="{cycle values = 'evenrow,oddrow'}">
					<td align="center"><input type="checkbox" name="comment[{$comment.id}]" id="checkbox_{$smarty.foreach.each_comment.iteration}" /></td>
					<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$comment.user.id}">{$comment.user.username}</a></td>
					<td>{$comment.added|date_format:"%d.%m.%Y, %H:%M"}</td>
					<td>{$comment.message}</td>
					<td>
						{if $comment.disabled}
							<a href="{$GLOBALS.site_url}/listing-comments/?action=enable&amp;comment_id={$comment.id}" title="[[Enable]]">[[Enable]]</a>
						{else}
							<a href="{$GLOBALS.site_url}/listing-comments/?action=disable&amp;comment_id={$comment.id}" title="[[Disable]]">[[Disable]]</a>
						{/if}
					</td>
					<td><a href="{$GLOBALS.site_url}/listing-comments/?action=edit&amp;comment_id={$comment.id}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
					<td><a href="{$GLOBALS.site_url}/listing-comments/?action=Delete&amp;comment_id={$comment.id}" onClick="return confirm('[[Are you sure you want to delete this comment?]]')" title="[[Delete]]" class="deletebutton">[[Delete]]</a></td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="clr"><br/></div>
	<a href="#" onclick="check_all();return false">[[Check all]]</a> / <a href="#" onclick="uncheck_all();return false">[[Uncheck all]]</a>
	<br/>[[Actions with Selected]]:
	<div class="clr"></div>
	<span class="greenButtonInEnd"><input onClick="document.forms['comments'].elements['action'].value = 'enable'" value="[[Enable]]" class="greenButtonIn" type="submit"></span>
	<span class="greenButtonInEnd"><input onClick="document.forms['comments'].elements['action'].value = 'disable'" value="[[Disable]]" class="greenButtonIn" type="submit"></span>
	<input onClick="if(!confirm('[[Are you sure you want to delete this comments?]]')) return false; document.forms['comments'].elements['action'].value = 'delete'; return true" value="[[Delete]]" class="deleteButton" type="submit">
</form>