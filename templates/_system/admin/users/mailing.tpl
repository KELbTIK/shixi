{foreach from=$errors key=key item=error}
	{if $key === 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}
<link type="text/css" href="{$GLOBALS.user_site_url}/system/ext/jquery/css/jquery-ui.css" rel="stylesheet" />
{capture name="test_send"}[[Test Send]]{/capture}

<script type="text/javascript">

	var site_url = '{$GLOBALS.site_url}';

	$(function() {
        var action = '{$smarty.get.edit}';
        if (! action) {
            $('#apply').attr('value', '[[Add]]');
    	}
    });

	function onClickActionButton()
	{
		var action = $("#selected_action").val();
		if (action == "test_send") {
			showSendWindow();
		} else {
			go();
		}
	}

	function showSendWindow(mail_id)
	{
		$("#test_mail_id").val(mail_id);
		$("#test_mailer").dialog('destroy');
		$("#test_mailer").attr({ title: "{$smarty.capture.test_send|escape:"javascript"}"});
		$("#test_mailer").dialog();
	}

   function go()
    {
        if ($("#saved_mailings input:checked").length > 0 && $("#selected_action").val() != '') {
            var action = $("#selected_action").val();
            $('#action').attr('name', action);
            $('#saved_mailings input:checked').val('1');
            if (action != 'delete' || confirm('[[Are you sure you want to delete selected mailing(s)?]]')) {
                $('#saved_mailings').submit();
            }
        } else {
            $("#dialog").dialog('destroy');
            $("#dialog").attr({ title: "[[Information]]"});
            $("#dialog").html("[[Please choose an action first]]").dialog({ width: 300});
        }
    }

	function closeWindow()
	{
		$("#test_mailer").dialog('destroy');
	}

    function set_checkbox(param)
    {
        for (i = 1; i <= total; i++) {
            if (checkbox = document.getElementById('checkbox_' + i)) {
                checkbox.checked = param;
            }
        }
    }

	function onClickTestSendButton()
	{
		var test_mail_id = $("#test_mail_id").val();
		if (test_mail_id) {
			sendTestEmail(test_mail_id);
		} else {
			var email = $("#test_email").val();
			$("#test_email_field").val(email);
			go();
		}
	}

	function sendTestEmail(mail_id)
	{
		location.href = site_url + '/mailing/?test_send=' + mail_id + "&email=" + encodeURIComponent($("#test_email").val());
	}
</script>

{if $subject eq ''}
    {breadcrumbs}[[Mass Mailing]]{/breadcrumbs}
    {else}
    {breadcrumbs}<a href="{$GLOBALS.site_url}/mailing/">[[Mass Mailing]]</a> &#187; [[Edit '{$subject}']]{/breadcrumbs}
{/if}
<div id="dialog" style="display: none"></div>
<div id="massMailling">
	<h1><img src="{image}/icons/mailstar32.png" border="0" alt="" class="titleicon"/>[[Mass Mailing]]</h1>
	{if $undeliveredMailingsForTest}
		<p class="error">[[Test Emails to the following email address(es) were undelivered. Please check your mail settings.]]  <br />
			{$undeliveredMailingsForTest}
			{elseif $test_message} <p class="message">[[Test send is completed]]</p>
	{/if}

	{if $send_result} <p class="message">[[Mass Mailing is in the send queue and will be sent to users shortly]]</p>
		{elseif $UndeliveredMailings}
		<p class="error">[[Emails to the following email address(es) were undelivered. Please check your mail settings.]] <br />
			{foreach from=$UndeliveredMailings item=undelievered}
				{$undelievered.email}<br />
			{/foreach}
		</p>
	{/if}
	{if $testEmailNotValid == true}
		<p class="error"> [[Test email is not valid]]</p>
	{/if}
	{if $mail_list && $subject eq '' && $mail_id eq ''}
        <form method="post" id="saved_mailings">
			<input type="hidden" id="test_email_field" name="email" value=""/>
            <input type="hidden" id="action" name="" value="1">
            <h3>[[Saved Mailings]]</h3>
            <div style="margin-bottom: 20px; width: 435px;">
                [[Actions with Selected]]:
                <select id="selected_action">
                    <option value="">[[Select action]]</option>
                    <option value="test_send">[[Test Send]]</option>
                    <option value="sending">[[Send]]</option>
                    <option value="delete">[[Delete]]</option>
                </select>
                <input type="button" value="[[Go]]" class="grayButton" onclick="onClickActionButton()"/>
            </div>
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="all_checkboxes" /></th>
                        <th width="35%">[[Subject]]</th>
                        <th width="20%">[[Number of users]]</th>
                        <th width="20%">[[Undelivered emails]]</th>
                        <th class="actions" width="1%">[[Actions]]</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$mail_list item=mail key=mail_key}
                        <tr class="{cycle values = 'evenrow,oddrow'}">
                            <td><input type="checkbox" name="mailing[{$mail.id}]" value="" /></td>
                            <td>{$mail.subject}</td>
                            <td>{$mail.count}</td>
                            <td>
                                <table id="clear">
                                    <tr>
                                        <td>{$mail.not_send}</td>
                                        {if $mail.not_send!=0}
											<td nowrap="nowrap"><a href="{$GLOBALS.site_url}/mailing/?sendToUndeliveredEmails={$mail.id}" >[[Send to undelivered emails]]</a></td>
                                        {/if}
                                    </tr>
                                </table>
                            </td>
							<td nowrap="nowrap">
								<a id="test_send" class="grayButton" onclick="showSendWindow('{$mail.id}')"/>[[Test Send]]</a>
								<a href="{$GLOBALS.site_url}/mailing/?sending={$mail.id}" class="grayButton">[[Send]]</a>
								<a href="{$GLOBALS.site_url}/mailing/?edit={$mail.id}" class="editbutton">[[Edit]]</a>
								<a href="{$GLOBALS.site_url}/mailing/?delete={$mail.id}" onClick="return confirm('[[Are you sure you want to delete this Mailing?]]');" title="[[Delete]]" class="deletebutton">[[Delete]]</a>
							</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
	{/if}
	
	{if $subject neq '' || $mail_id neq ''}
		<h3>[[Edit Mailing '$subject']]</h3>
	{else}
		<h3>[[Create a New Mailing]]</h3>
	{/if}
	
	<form method="POST" enctype="multipart/form-data" name="mailing_create_form" onsubmit="disableSubmitButton('apply');">
	    {if $mail_id}<input type="hidden" name="mail_id" value="{$mail_id}" />{/if}
        <input type="hidden" id="submit" name="submit" value="save" />
	    <table id="clear"  width="100%">
	        <tr>
	            <td width="5%">[[To]]:</td>
	            <td colspan="2">
	            	<div style="float: left;">
		                <fieldset id="mailing_to">
		                    <legend>[[Recipient Criteria]]</legend>
		                    <br/>
		                    <div class="to_block">
		                        <label for="user_group">[[User Group]]</label>
		                        <select name="users" id="user_group">
		                            <option {if $param.users == 0}selected{/if} value="0">[[Any]]</option>
		                                    {foreach from=$groups item=group}
		                            <option {if $param.users == $group.sid}selected="selected"{/if} value="{$group.sid}">[[{$group.id}]]</option>
		                                    {/foreach}
		                        </select>
		                    </div>
		                    <div id="memb_plans" class="to_block">
		                    </div>
		                    <div class="to_block">
                                <label for="country">[[Country]]</label>
                                <select id="country" name="country[]" multiple="multiple" size="3">
                                    {foreach from=$fields.country item=list_value}
                                        <option value='{$list_value.id}'{foreach from=$param.country item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >[[{$list_value.caption}]]</option>
                                    {/foreach}
                                </select>
		                    </div>
                            <div class="to_block">
                                <label for="state">[[State]]</label>
                                <select id="state" name="state[]" multiple="multiple" size="3">
                                    {foreach from=$fields.state item=list_value}
                                        <option value='{$list_value.id}'{foreach from=$param.state item=value_id}{if $list_value.id == $value_id}selected="selected"{/if}{/foreach} >[[{$list_value.caption}]]</option>
                                    {/foreach}
                                </select>
		                    </div>
                            <div class="to_block">
                                <label>[[City]]</label>
                                <input type="text" name="city" value="{$param.city}" size="50" />
		                    </div>
                            <div class="to_block">
		                        <label for="user_status">[[User Status]]</label>
		                        <select id="user_status" name="user_status" >
		                            <option value="">[[Any Status]]</option>
		                            <option {if $param.status eq '1'}selected="selected"{/if} value="1">[[Active]]</option>
		                            <option {if $param.status eq '0'}selected="selected"{/if} value="0">[[Not Active]]</option>
		                        </select>
		                    </div>
		                    <div class="to_block">
		                        <label for="activation_date_notless">[[Registration date]]</label>
		                        <input type="text" name="registration_date[not_less]" value="{$param.registration.not_less}" id="registration_date_notless" style="width:110px"/>
		                        [[to]] <input type="text" name="registration_date[not_more]" value="{$param.registration.not_more}" id="registration_date_notmore" style="width:110px"/>
		                    </div>
		                    <div class="to_block">
		                        <label for="without_cv">[[Without Listings]]</label>
		                        <input id="without_cv" type="checkbox" name="without_cv" value="1" {if $param.without_cv == '1'}checked="checked"{/if} />
		                    </div>
		                </fieldset>
					</div>
	                {*<div style="float: left; margin: 130px 0 0 10px;">
	                	<span class="greenButtonInEnd"><input type="submit" name="send" value="Save mailing" class="greenButtonIn" /></span>
	                </div>*}
	            </td>
	        </tr>
	        <tr>
	            <td>[[Subject]]:</td>
	            <td colspan="2"><input type="text" name="subject" value="{$subject}" size="50" /></td>
	        </tr>
	        <tr>
	            <td>[[File]]:</td>
	            <td colspan="2">
	                <input type="file" name="file_mail" /> <small>([[max.]] {$uploadMaxFilesize} M)</small><br />
					{if $file}
						<input type="hidden" name="old_file" value="{$file}" />
						<a href="{$GLOBALS.user_site_url}/{$file_url}">{$GLOBALS.user_site_url}/{$file_url}</a><br />
						<input type="checkbox" name="delete_file" /> [[Delete File]]
					{/if}
	            </td>
	        </tr>
	        <tr>
	            <td colspan="3">[[Text]]:{WYSIWYGEditor name="text" width="100%" height="400" value="$text" conf="BasicAdmin"}</td>
	        </tr>
	        <tr>
	        	<td colspan="3">
                    <div class="floatRight">
                        <input type="submit" id="apply" value="[[Apply]]" class="grayButton" />
                        {if ! empty($smarty.get.edit)}
                            <input type="submit" value="[[Save]]" class="grayButton" />
                        {/if}
                    </div>
                </td>
	        </tr>
	    </table>
	</form>
		<div id="test_mailer" style="display: none">
			<input type="hidden" id="test_mail_id" value=""/>
			<table>
				<tr>
					<td colspan="2">[[Send Test email to]]:</td>
				</tr>
				<tr>
					<td colspan="2"><input type="text" id="test_email" value="[[{$test_email}]]"><td>
				</tr>
				<tr>
					<td><input type="button" id="test_send_button" class="grayButton" value="[[Test Send]]" onclick="onClickTestSendButton()"></td>
					<td><input type="button" id="close_button" class="grayButton" value="[[Close]]" onclick="closeWindow()"></td>
				</tr>
			</table>
		</div>
</div>


	<script type="text/javascript">
	    $("document").ready(function(){
	        $("#user_group").change(function () {
	                $.ajax({
	                    type: "GET",
	                    data: "usergr="+$(this).val(),
	                    success: function(msg){
	                        $("#memb_plans").html(msg);
	                    }
	                });
	        })
	        .change();
	    })
	
	var dFormat = '{$GLOBALS.current_language_data.date_format}';

	dFormat = dFormat.replace('%m', "mm");
	dFormat = dFormat.replace('%d', "dd");
	dFormat = dFormat.replace('%Y', "yy");
	
	$( function() {
		$("#registration_date_notless, #registration_date_notmore").datepicker({
			dateFormat: dFormat,
			showOn: 'both',
			yearRange: '-99:+99',
			buttonImage: '{image}icons/icon-calendar.png'
		});
	
		$(".setting_button").click(function(){
			var butt = $(this);
			$(this).next(".setting_block").slideToggle("normal", function(){
				if ($(this).css("display") == "block") {
					butt.children(".setting_icon").html("[-]");
					butt.children("b").text("[[Click to hide search criteria]]");
				} else {
					butt.children(".setting_icon").html("[+]");
					butt.children("b").text("[[Click to modify search criteria]]");
				}
			});
		});

        $('#apply').click(
            function(){
                $('#submit').attr('value', 'apply');
            }
        );
	
	});

    $("#all_checkboxes").click(function() {
        if ($(this).attr('checked') == false){
            $("#saved_mailings input[type='checkbox']").attr('checked', false);
        } else {
            $("#saved_mailings input[type='checkbox']").attr('checked', true);
        }
    });

</script>
