{capture name="trSpecifyTheReason"}[[Please specify the reason for deleting your profile. Thank you.]]{/capture}
{capture name="trCancel"}[[Cancel]]{/capture}
{capture name="trDeleteProfile"}[[Delete profile]]{/capture}
{literal}
<script type="text/javascript">
	function deleteProfileDialog() {
		$('#reason-of-unregister').dialog({
			title: '{/literal}{$smarty.capture.trSpecifyTheReason|escape:"quotes"}{literal}',
			width: '500px',
			modal: true,
			resizable: false,
			buttons: [
				{
					text: "{/literal}{$smarty.capture.trCancel|escape:"quotes"}{literal}",
					click: function() {
						$(this).dialog("close");
					}
				},
				{
					text: "{/literal}{$smarty.capture.trDeleteProfile|escape:"quotes"}{literal}",
					click: function() {
						$("#reason-to-unregister-form").submit();
						$(this).dialog("close");
					}
				}
			]
		});
	}
</script>
{/literal}
<h1>[[My Profile]]</h1>
{* LINKEDIN : LINK PROFILE *}
<div class="soc_reg_form">
{module name="social" function="link_with_linkedin"}
</div>
{* / LINKEDIN : LINK PROFILE *}
{include file='field_errors.tpl'}
{if $action eq "delete_profile" && !$errors}
	<p class="message">[[You have successfully deleted your profile!]]</p>
{else}
	{if $form_is_submitted && !$errors}
		<p class="message">[[You have successfully changed your profile info!]]</p>
	{/if}

	<div id="reason-of-unregister-cont" style="display: none;">
		<div id="reason-of-unregister">
			<form action="" method="post" id="reason-to-unregister-form">
				<input type="hidden" name="command" value="unregister-user" />
				<fieldset>
					<div class="inputName">[[Reason]]</div>
					<div class="inputField"><textarea id="reason" name="reason" style="width: 350px; height: 200px;"></textarea></div>
				</fieldset>
			</form>
		</div>
	</div>

	<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="action" value="save_info"/>
			{set_token_field}
			{foreach from=$form_fields item=form_field}
				{if $show_mailing_flag==0 && $form_field.id=="sendmail"}
				{elseif $form_field.type == "video"}
					<fieldset>
						<div class="inputName">[[$form_field.caption]]</div>
						<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
						<div class="inputField">{input property=$form_field.id template="video_profile.tpl"}</div>
					</fieldset>
				{elseif $form_field.type == 'location'}
					{input property=$form_field.id}
				{else}
				<fieldset>
					<div class="inputName">[[$form_field.caption]]</div>
					<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
					<div class="inputField">{input property=$form_field.id}</div>
					{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="../classifieds/instructions.tpl" form_field=$form_field}{/if}
					{if in_array($form_field.type, array('multilist'))}
						<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
					{/if}
				</fieldset>
				{/if}
			{/foreach}
				<fieldset>
					<div class="inputName">
						{if $acl->isAllowed('delete_user_profile')}
							<input type="button" value="{$smarty.capture.trDeleteProfile|escape:"quotes"}" class="button" onclick="deleteProfileDialog()" />
						{elseif $acl->getPermissionParams('delete_user_profile') == "message"}
							<input type="button" value="{$smarty.capture.trDeleteProfile|escape:"quotes"}" class="button"
								onclick="popUpWindow('{$GLOBALS.site_url}/access-denied/?permission=delete_user_profile', 300, '{$smarty.capture.trDeleteProfile|escape:"quotes"}')" />
						{/if}
					</div>
					<div class="inputReq">&nbsp;</div>
					<div class="inputField"><input type="submit" value="[[Save]]" class="button" /></div>
				</fieldset>
		{if $instructionsExist}
			{literal}
			<script type="text/javascript">
				$("document").ready(function(){
					var elem = $(".instruction").prev();
					elem.children().focus(function(){
						$(this).parent().next(".instruction").children(".instr_block").show();
					});
					elem.children().blur(function(){
						$(this).parent().next(".instruction").children(".instr_block").hide();
					});
				});
				CKEDITOR.on('instanceReady', function(e) {
					e.editor.on('focus', function() {
						$("#instruction_"+ e.editor.name).show();
					});
					e.editor.on('blur', function() {
						$("#instruction_"+e.editor.name).hide();
					});
					return;
				});
			</script>
			{/literal}
		{/if}
	</form>
{/if}
