	<ul id="notifications_ul">
		{foreach item=permission from=$notifications}
				<li class="{cycle values = 'evenrow,oddrow'}">
					<input type="checkbox" id="notif_{$permission.name}" name="{$permission.name}" {if $permission.params != "deny"}checked="checked"{/if} />
					<span class="permTitle">[[{$permission.title}]]</span>
					<span class="status" id="{$permission.name}_status"></span>
				</li>
		{/foreach}
	</ul>
<script type="text/javascript">
	$("input[id^='notif_']").click(function(){
		el = $(this);
		attrName=$(this).attr("name")
		$("span[id='"+attrName+"_status']").text('')
		$.ajax({
			data: "name="+attrName+"&value="+$(this).attr("checked"),
			success: function(data) {
				$("span[id='"+attrName+"_status']").text(data);
			}
		});
	})
</script>