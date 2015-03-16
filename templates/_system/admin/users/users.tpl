<script  type="text/javascript" src="{common_js}/pagination.js"></script>
<script type="text/javascript">
	$.ui.dialog.prototype.options.bgiframe = true;
	var progbar = "<img src='{$GLOBALS.user_site_url}/system/ext/jquery/progbar.gif' />";
	var parentReload = false;
	$(function() {
		$(".getUser").click(function() {
			$("#dialog").dialog('destroy');
			$("#dialog").attr({ title: "Loading"});
			$("#dialog").html(progbar).dialog({ width: 180});
			var link = $(this).attr("href");
			$.get(link, function(data) {
				$("#dialog").dialog('destroy');
				$("#dialog").attr({ title: "User Product Details"});
				$("#dialog").html(data).dialog({
					width: 560,
					close: function(event, ui) {
						$("#expired_date").datepicker( 'hide' );
						if (parentReload == true) {
							window.location = "?restore=1";
						}}
				});
			});
			return false;
		});

		$("#change_product_send_button").click(function() {
			val = $("#product_select").val();
			$("#product_to_change").val( val );
			var number_of_listings = $("#number_of_listings_"+val).val();
			$("#number_of_listings").val( number_of_listings );
			$("input[name='action_name']").val('change_product');
			$("#change_product_dialog").dialog('destroy').html("[[Please wait ...]]" + progbar).dialog( { width: 200});
			$("form[name='users_form']").submit();
		});
		
		$("#user_reject_send_button").click(function() {
			val = $("#rejection_reason_text").val();
			$("#rejection_reason").val(val);
			$("input[name='action_name']").val('reject');
			$("#user_reject_dialog").dialog('destroy').html("[[Please wait ...]]" + progbar).dialog( { width: 200});
			$("form[name='users_form']").submit();
		});
		
		$("tr[id^='users']").click(function() {
			var name = ($(this).attr('id'));
			if( !$(this).attr('style') ) {
				$("input[name='" + name + "']").attr('checked','checked');
				$(this).attr('style','background-color: #ffcc99');
			} else {
				$(this).removeAttr('style');
				$("input[name='" + name + "']").removeAttr('checked');
			}
		});
	});

	function login_as_user( name, pass ) {
		$.get('{$GLOBALS.site_url}/login-as-user/', { username: name, password: pass}, function (data) {
			var response = $.trim(data);
			if (response == "") {
				document.login.username.value = name;
				document.login.password.value = pass;
				document.getElementById('login').submit();
			}
			else {
				popUpMessageWindow(300, 100, '[[Error]]', data);
			}
		});
	}

	function isPopUp(button, textChooseAction, textChooseItem, textToDelete) {
		if(isActionEmpty(button, textChooseAction, textChooseItem)) {
			var action = $("#selectedAction_" + button).val();

			switch (action) {
				case 'send_activation_letter':
					var users = [];
					var userids = [];
					users = $("input:checked");

					for (var i = 0; i < users.length; i++) {
						userids[i] = users[i].name.substring(users[i].name.indexOf('[')+1,users[i].name.lastIndexOf(']'));
					}

					var progbar = "<img src='{$GLOBALS.site_url}/../system/ext/jquery/progbar.gif'>";
					$(function() {
						var data = '';
						$("#dialog").dialog('destroy');
						$("#dialog").attr({ title: "Loading"});
						$("#dialog").html(progbar).dialog({ width: 180 });

						$.get("{$GLOBALS.site_url}/send-activation-letter/",{ 'userids[]':userids, ajax:true}, function(data) {
							$("#dialog").dialog('destroy');
							$("#dialog").attr({ title: "[[Sending activation emails]] " });
							$("#dialog").html(data).dialog({ width: 300 });
						});
					});
					break;
				case 'reject':
					$("#user_reject_dialog").dialog('destroy');
					$("#user_reject_dialog").dialog({ title: "[[User Rejection]]", width: 350});
					break;
				case 'change_product':
					$("#change_product_dialog").dialog('destroy');
					$("#change_product_dialog").dialog({ title: "[[Change Product]]", width: 350});
					break;
				case "delete":
					if (confirm(textToDelete)) {
						submitForm("delete");
					}
					break;
				default :
					submitForm(action);
					break;
			}
		}
	}

	function viewListingBlock() {
        $("#product_select option").each(function () {
        	$("#block_"+this.value).css('display', 'none');
          });
	
        $("#product_select option:selected").each(function () {
           $("#block_"+this.value).css('display', 'block');
         });
	}	
	</script>

{if $rangeIPs}
	<div id="bannedIPsInfo" title="Attention!" style="display:none">
		<p>
			<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
			[[The range of the IP addresses has been banned. That's why you are not able to unblock the following IP addresses]]:
			{foreach from=$rangeIPs item=IP}
				<b>{$IP}</b><br/>
			{/foreach}
		</p>
	</div>
	<script type="text/javascript"><!--
	$("#bannedIPsInfo").dialog({
		bgiframe: true,
		buttons: {
			Close: function() {
				$(this).dialog('close');
			}
		}
	});
	--></script>
{/if}

{if $cantBanUsers}
	<div id="usersInfo" title="[[Attention!]]" style="display:none">
		<p>
			<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
			[[IPs of the following users were not defined, therefore they can’t be banned]]:<br/>
			{foreach from=$cantBanUsers item=username}
				<b>{$username}</b><br/>
			{/foreach}
		</p>
	</div>
	<script type="text/javascript"><!--
	$("#usersInfo").dialog({
		bgiframe: true,
		buttons: {
			Close: function() {
				$(this).dialog('close');
			}
		}
	});
	--></script>
{/if}
{if $errors}
	{foreach from=$errors item="error"}
		<p class="error">[[$error]]</p>
	{/foreach}
{/if}
<div id="dialog" style="display: none"></div>
<form id="login" name="login" target="_blank"  action="{$GLOBALS.user_site_url}/login/" method="post">
    <input type="hidden" name="action" value="login" />
    <input type="hidden" name="as_user" />
    <input type="hidden" name="username" value="" />
    <input type="hidden" name="password" value="" />
</form>

<div class="clr"><br/></div>
<form method="post" name="users_form">
	<input type="hidden" name="action_name" id="action_name" value="" />
	<input type="hidden" name="product_to_change" id="product_to_change" value="" />
	<input type="hidden" name="number_of_listings" id="number_of_listings" value="" />
	<input type="hidden" name="rejection_reason" id="rejection_reason" value="" />
	<div class="box" id="displayResults">
		<div class="box-header">
			{include file="../pagination/pagination.tpl" layout="header"}
			<div id="change_product_dialog" style="display: none">
				[[Select Action]]:
				<select name="product_select" id="product_select" style="width: 219px;"  onChange="viewListingBlock()">
					<option value='0'>[[Clear Subscriptions]]</option>
					{foreach from=$products item=product}
						<option value="{$product.sid}">[[Add]] [[{$product.name}]]</option>
					{/foreach}
				</select>
				<br/><br/>
				{foreach from=$products item=product}
					<div id="block_{$product.sid}" style="display: none">
						{if $product.count_listings}
							[[Number of Listings]]:
							<select name="number_of_listings_{$product.sid}" id="number_of_listings_{$product.sid}" style="width: 50px;">
								{foreach from=$product.count_listings item=count_listings}
									<option value="{$count_listings}">[[{$count_listings}]]</option>
								{/foreach}
							</select>
						{/if}
					</div>
				{/foreach}
				<div class="clr"><br/></div>
				<div class="floatRight"><input type="button" id="change_product_send_button" name="change_product_send_button" value="[[Change]]" class="greenButton" /></div>
			</div>

			<div id="user_reject_dialog" style="display: none">
				[[Enter Reject Reason]]:
				<textarea name="rejection_reason_text" id="rejection_reason_text" style="width: 315px; height: 200px;"></textarea>
				<div class="clr"><br/></div>
				<div class="floatRight"><input type="button" id="user_reject_send_button" name="user_reject_send_button" value="[[Reject]]" class="grayButton" /></div>
			</div>
		</div>
		<div class="innerpadding">
			<div id="displayResultsTable">
				<table width="100%">
					<thead>
					{include file="../pagination/sort.tpl"}
					</thead>
					{foreach from=$found_users item=found_user name=users_block}
						<tr class="{cycle values = 'evenrow,oddrow'}">
							<td><input type="checkbox" name="users[{$found_user.sid}]" value="1" id="checkbox_{$smarty.foreach.users_block.iteration}" /></td>
							<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$found_user.sid}" title="Edit"><b>{$found_user.sid}</b></a></td>
							<td><a href="{$GLOBALS.site_url}/edit-user/?user_sid={$found_user.sid}" title="Edit"><b>{$found_user.username}{if $found_user.parent_sid}&nbsp;<small>([[sub-user]])</small>{/if}</b></a></td>
							{if $userGroupInfo.id == 'Employer'}
								<td>{$found_user.CompanyName|escape:'html'}</td>
							{elseif $userGroupInfo.id == 'JobSeeker'}
								<td>{$found_user.FirstName|escape:'html'}</td>
								<td>{$found_user.LastName|escape:'html'}</td>
							{/if}
							<!-- for ie -->	<td style="word-break: break-all;"><!-- for firefox--><div style="word-wrap: break-word; width: 130px;"><a href="mailto:{$found_user.email}">{$found_user.email}</a></div></td>
							<td>{if $found_user.products > 0}<a href="{$GLOBALS.site_url}/user-product/?user_sid={$found_user.sid}" target="_blank" class="getUser">{$found_user.products}</a>{else}[[N/A]]{/if}</td>
							<td>{$found_user.registration_date}</td>
							<td>
								{if $found_user.active == "1"}
									[[Active]]
								{else}
									[[Not Active]]
								{/if}
							</td>
							{if $userGroupInfo.approve_user_by_admin}
								<td>[[{$found_user.approval}]]</td>
							{/if}
							<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/edit-user/?user_group={$userGroupInfo.id}&amp;user_sid={$found_user.sid}" title="[[Edit]]" class="editbutton">[[Edit]]</a></td>
							<td nowrap="nowrap" style="border-left: 0px;"><span class="greenButtonEnd"><input type="button" name="button" value="[[Login]]" class="greenButton" onclick="login_as_user('{$found_user.username}', '{$found_user.password}');" /></span></td>
						</tr>
					{/foreach}
				</table>
			</div>
		</div>
		<div class="box-footer">
			{include file="../pagination/pagination.tpl" layout="footer"}
		</div>
	</div>
</form>