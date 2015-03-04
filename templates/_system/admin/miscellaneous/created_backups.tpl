<div id='backups'>
	{foreach from=$errors item=error key=field_caption}
		{if $error eq 'FILE_NOT_FOUND'}
			<p class="error">[[File not found]]</p>
		{/if}
	{/foreach}
	<table width="100%">
		<thead>
			<tr >
				<th>[[Name]]</th>
				<th>[[Created]]</th>
				<th>[[Backup Type]]</th>
				<th class="actions">[[Actions]]</th>
			</tr>
		</thead>
		{foreach from=$created_backups item=created_backup}
			<tr class="{cycle values = 'evenrow,oddrow'}">
				<td>
					<a href="{$GLOBALS.site_url}/backup/?filename={$created_backup.name}" title="[[Download]]">{$created_backup.name}</a>
				</td>
				<td>{$created_backup.date}</td>
				<td>[[{$created_backup.type}]]</td>
				<td>
					<a href="{$GLOBALS.site_url}/backup/?action=delete_backup&name={$created_backup.name}" class="deletebutton" style="color:white;" onclick='deteleBackup("{$created_backup.name}"); return false;' title="[[Delete]]">[[Delete]]</a>
				</td>
			</tr>
		{/foreach}
	</table>
<script>
	function deteleBackup(name) {
		if (confirm("[[Are you sure you want to delete this backup?]]")) {
			var url = "{$GLOBALS.site_url}/backup/";
			$.post(url, { action: "delete_backup", name: name}, function(data){
				$('#backups').html(data);
			});
		}
	}
</script>

</div>