<script language="javascript">
		function formSubmit(url) {
			$("#messageBox").dialog('destroy');
			var options = {
				target: "#messageBox",
				url:  url,
				success: function(data) {
					$("#messageBox").dialog({
						width: 900,
						modal: true,
						title: "[[Choose User]]"
					}).dialog( 'open' );
				}
			};
			$("#userGroups").ajaxSubmit(options);
			return false;
		}
</script>
<br/>
<form id="userGroups" method="post" >
	<input type="hidden" name="search_template" value="choose_user_search.tpl" />
	<input type="hidden" name="template" value="choose_user.tpl" />
	{foreach from=$userGroupsInfo item=userGroup}
		<div style="float: left; padding-left: 10px; padding-bottom: 10px;"><a href="#" onClick="formSubmit('{$GLOBALS.site_url}/manage-users/{$userGroup.id|lower}/')" class="grayButton">[[{$userGroup.name}]]</a></div>
	{/foreach}
</form>