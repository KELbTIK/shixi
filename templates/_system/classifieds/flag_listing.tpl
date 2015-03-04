{capture name='flagListingPermission'}flag_{$listing_type_id}{/capture}
{if ($listing_type_id && $acl->isAllowed($smarty.capture.flagListingPermission))}
	{foreach from=$errors item=error key=error_code}
		<p class="error">
			{if $error_code == 'EMPTY_VALUE'}
				[[Enter Security code]]
			{elseif $error_code == 'NOT_VALID'}
				[[Security code is not valid]]
			{elseif $error_code == 'WRONG_LISTING_ID_SPECIFIED'}
				[[Listing does not exist]]
			{else}
				[[{$error}]]
			{/if}
		</p>
	{/foreach}

	<form method="post" id="flagForm" action="" onsubmit="disableSubmitButton('submitForm'); sendFlagForm(); return false;" >
		<input type="hidden" name="listing_id" value="{$listing_id|htmlspecialchars}" />
		<input type="hidden" name="action" value="flag" />
		{if count($flag_types)}
			<fieldset>
				<div class="inputName">[[Select Flag Type]]</div>
				<div class="inputField">
					<select name="reason">
						{foreach from=$flag_types item=type}
							<option value="{$type.sid}" {if $reason == $type.sid} selected="selected"{/if}>[[{$type.value}]]</option>
						{/foreach}
					</select>
				</div>
			</fieldset>
		{/if}
		<fieldset>
			<div class="inputName">[[Comment]]</div>
			<div class="inputField"><textarea name="comment" cols="42" rows="3">{$comment}</textarea></div>
		</fieldset>
		<fieldset>
			{module name="miscellaneous" function="captcha_handle" currentFunction="flag_listing" displayMode="fieldset"}
		</fieldset>
		<fieldset>
			<div class="inputName">&nbsp;</div>
			<div class="inputButton"><input type="submit" name="sendForm" value="[[Send]]" class="button" id="submitForm" /></div>
		</fieldset>
	</form>
{elseif $listing_type_id == ''}
	<p class="error">[[Listing does not exist]]</p>
{else}
	<p class="error">[[You do not have permissions to flag listing]]</p>
{/if}