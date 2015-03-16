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

	<form method="post" id="flagForm" action="" onsubmit="disableSubmitButton('submitForm'); sendFlagForm(); return false;" class="form-horizontal">
		<input type="hidden" name="listing_id" value="{$listing_id|htmlspecialchars}" />
		<input type="hidden" name="action" value="flag" />
		{if count($flag_types)}
			<div class="form-group has-feedback">
				<label class="inputName label-control col-sm-3">[[Select Flag Type]]</label>
				<div class="inputField col-sm-8">
					<select class="form-control"   name="reason">
						{foreach from=$flag_types item=type}
							<option value="{$type.sid}" {if $reason == $type.sid} selected="selected"{/if}>[[{$type.value}]]</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		<div class="form-group has-feedback">
			<label class="inputName label-control col-sm-3">[[Comment]]</label>
			<div class="inputField col-sm-8"><textarea class="form-control" name="comment" rows="10">{$comment}</textarea></div>
		</div>
		<div class="form-group has-feedback">
			<div class="col-sm-8 col-sm-offset-3">{module name="miscellaneous" function="captcha_handle" currentFunction="flag_listing" displayMode="fieldset"}</div>
		</div>
		<div class="form-group has-feedback">
			<label class="inputName label-control col-sm-3">&nbsp;</label>
			<div class="inputButton col-sm-8"><input type="submit" name="sendForm" value="[[Send]]" class="btn btn-default" id="submitForm" /></div>
		</div>
	</form>
{elseif $listing_type_id == ''}
	<div class="error alert alert-danger">  [[Listing does not exist]]</div>
{else}
	<div class="error alert alert-danger">[[You do not have permissions to flag listing]]</div>
{/if}