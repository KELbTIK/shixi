{if $GLOBALS.current_user.logged_in}
	<a href="{$GLOBALS.site_url}/private-messages/inbox/" class="pm">{if $url == "/private-messages/inbox/" || $url == "/private-messages/inbox/read/"}<span class="strong">[[Inbox]]</span>{else}[[Inbox]]{/if}</a> ({$unread}) |
	<a href="{$GLOBALS.site_url}/private-messages/outbox/" class="pm">{if $url == "/private-messages/outbox/" || $url == "/private-messages/outbox/read/"}<span class="strong">[[Outbox]]</span>{else}[[Outbox]]{/if}</a> |
	<a href="{$GLOBALS.site_url}/private-messages/send/" class="pm">{if $url == "/private-messages/send/"}<span class="strong">[[Compose Message]]</span>{else}[[Compose Message]]{/if}</a> |
	<a href="{$GLOBALS.site_url}/private-messages/contacts/" class="pm">{if $url == "/private-messages/contacts/"}<span class="strong">[[Contacts]]</span>{else}[[Contacts]]{/if}</a><br />
	{if $errors.NOT_EXISTS_MESSAGE}
		<p class="error">[[Message with specified ID does not exist in your mailbox]]</p>
		{assign var="include" value=''}
	{/if}
	<div class="clr"><br/></div>
	{if $include != ""}{include file="$include"}{/if}
	<script type="text/javascript">
		{literal}

			$("#pm_all_check").click(function () {
				var total = $(this).attr("checked");
				$(".pm_checkbox").attr("checked", total);
			});

			$("#pm_controll_delete, #pm_controll_mark").click(function() {
				var butt = $(this);
				if ($(".pm_checkbox:checked").size() > 0) {
					if (butt.attr("id") == "pm_controll_mark"){
						$("#pm_action").val("mark");
					} else {
						if (!confirm("{/literal}[[Are you sure?]]{literal}"))
							return false;
						$("#pm_action").val("delete");
					}
					$("#pm_form").submit();
				} else {
					{/literal}alert('[[Please select messages]]');{literal}
				}
			});

			$("#pm_reply").click(function() {
				document.location.href = $("#pm_reply_link").val();
			});

			$("#pm_delete").click(function() {
				if (confirm('{/literal}[[Are you sure?]]{literal}'))
				document.location.href = $("#pm_delete_link").val();
			});

		{/literal}
	</script>
{else}
	{assign var="url" value=$GLOBALS.site_url|cat:"/registration/"}
	<p class="error">[[Please log in to access this page. If you do not have an account, please]] <a href="{$url}">[[Register.]]</a></p>
	{module name="users" function="login"}
{/if}
