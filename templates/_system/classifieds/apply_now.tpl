{literal}
<script type="text/javascript">
	function applySubmit() {
		$("#ApplicationForm").hide();
		$("#ProgressBar").show();
		$("#applyForm").ajaxSubmit( {
			url: $("#applyForm").attr("action"),
			type: "POST",
			success: function (data) {
				$("#messageBox").html(data);
			}
		});
		return false;
	}
</script>
{/literal}

<div id="ProgressBar" style="display:none"><img src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" />[[Please wait ...]]</div>

<div id="ApplicationForm">
	{if $is_data_submitted && !$errors}
		<div class="message alert alert-info">[[You applied successfully]]</div>
	{else}
		{foreach from=$errors key=error_code item=error_message}
				<div class="error alert alert-danger">
					{if $error_code  eq 'EMPTY_VALUE'} [[Enter Security code]]
					{elseif $error_code eq 'NOT_VALID'} [[Security code is not valid]]
					{else}[[{$error_message}]]
					{/if}
				</div>
		{/foreach}
		{include file='field_errors.tpl'}
		<form method="post" enctype="multipart/form-data" action="{$GLOBALS.site_url}/apply-now/" id="applyForm">
			<input type="hidden" name="is_data_submitted" value="1">
			<input type="hidden" name="listing_id" value="{$listing_id}">
			{if NOT $GLOBALS.current_user.logged_in}
				<div class="form-group has-feedback">
					<div class="inputName">[[Your name]]:</div>
					<div class="inputField"><input class="form-control" type="text" name="name" value="{$request.name}" /></div>
				</div>
				<div class="form-group has-feedback">
					<div class="inputName">[[Your e-mail]]:</div>
					<div class="inputField"><input class="form-control" type="text" name="email" value="{$request.email}" /></div>
				</div>
			{/if}
			<div class="form-group has-feedback">
				<div class="inputName">[[Cover letter (optional)]]:</div>
				<div class="inputField"><textarea class="form-control" name="comments" rows="5">{$request.comments}</textarea></div>
			</div>
			{if $GLOBALS.current_user.logged_in && $resume}
				<div class="form-group has-feedback">
					<div class="inputName">[[Select your resume]]:</div>
					<div class="inputField">
						<select class="form-control" name="id_resume">
							<option value="0">[[Select your resume]]</option>
							{html_options options=$resume selected=$request.id_resume}
						</select>
						<br />or
					</div>
				</div>
			{/if}
			<div class="form-group has-feedback">
				<div class="inputName">[[Attach your resume]]:</div>
				<div class="inputField"><input class="form-control" type="file" name="file_tmp" /></div>
			</div>
			<div class="form-group has-feedback">
				{module name="miscellaneous" function="captcha_handle" currentFunction="apply_now" displayMode="fieldset"}
			</div>
			<input type="hidden" name="anonymous" value="1" />
			{if $form_fields}
				<div class="form-group has-feedback">
					{include file="questionnaire.tpl" form_fields=$form_fields}
				</div>
			{/if}
			<div class="form-group has-feedback">
				<div class="inputName">&nbsp;</div>
				<div class="inputButton"><input class="btn btn-default" id="SubmitButton" type="submit" value="[[Send]]" onclick="return applySubmit();"/></div>
			</div>
		</form>
	{/if}
</div>
