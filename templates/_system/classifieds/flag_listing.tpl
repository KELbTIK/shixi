{capture name='flagListingPermission'}flag_{$listing_type_id}{/capture}
{if ($listing_type_id && $acl->isAllowed($smarty.capture.flagListingPermission))}
	{foreach from=$errors item=error key=error_code}
		<div class="error alert alert-danger">
			{if $error_code == 'EMPTY_VALUE'}
				[[Enter Security code]]
			{elseif $error_code == 'NOT_VALID'}
				[[Security code is not valid]]
			{elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'}
				[[Listing does not exist]]
			{else}
				[[{$error}]]
			{/if}
		</div>
	{/foreach}

	<form method="post" id="flagForm" action="" onsubmit="disableSubmitButton('submitForm'); sendFlagForm(); return false;" >
		<input type="hidden" name="listing_id" value="{$listing_id|htmlspecialchars}" />
		<input type="hidden" name="action" value="flag" />
		{if count($flag_types)}
			<div class="form-group has-feedback">
				<label class="inputName">[[Select Flag Type]]</label>
				<div class="inputField">
					<select class="form-control"   name="reason">
						{foreach from=$flag_types item=type}
							<option value="{$type.sid}" {if $reason == $type.sid} selected="selected"{/if}>[[{$type.value}]]</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		<div class="form-group has-feedback">
			<div class="inputName">[[Comment]]</div>
			<div class="inputField"><textarea class="form-control"   name="comment" cols="42" rows="3">{$comment}</textarea></div>
		</div>
		<div class="form-group has-feedback">
			{module name="miscellaneous" function="captcha_handle" currentFunction="flag_listing" displayMode="fieldset"}
		</div>
		<div class="form-group has-feedback">
			<div class="inputName">&nbsp;</div>
			<div class="inputButton"><input type="submit" name="sendForm" value="[[Send]]" class="btn btn-default" id="submitForm" /></div>
		</div>
	</form>
{elseif $listing_type_id == ''}
	<div class="error alert alert-danger">  [[Listing does not exist]]</div>
{else}
	<div class="error alert alert-danger">[[You do not have permissions to flag listing]]</div>
{/if}