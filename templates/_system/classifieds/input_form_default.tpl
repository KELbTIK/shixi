{foreach from=$form_fields item=form_field}
	{$form_field=$form_field scope=global}
	{if $form_field.id == 'video' || $form_field.id == 'youtube'}
		{if $extraInfo.video}
			<fieldset>
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</fieldset>
		{/if}
	{elseif ($listing_type_id == "Job" || $listing.type.id == "Job") && $form_field.id == "anonymous"}
		{* this empty place of 'anonymous' checkbox in 'Job' listing *}
	{elseif ($listing_type_id == "Resume" || $listing.type.id == "Resume") && $form_field.id == "anonymous"}
			<fieldset>
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</fieldset>
	{elseif $form_field.id == "access_type"}
		{if $listing_type_id != "Job" && $listing.type.id != "Job"}{* *}
			<fieldset>
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id template='resume_access.tpl'}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</fieldset>
		{/if}
	{elseif ($listingTypeID == "Job" || $listing.type.id == "Job") && $form_field.id == 'ApplicationSettings'}
		<fieldset>
			<div class="inputName">[[$form_field.caption]]</div>
			<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
			<div class="inputField">{input property=$form_field.id template='applicationSettings.tpl'}</div>
			{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
		</fieldset>
	{elseif ($listingTypeID == "Job" || $listing.type.id == "Job") && $form_field.id == 'expiration_date'}
		{capture assign="expirationField"}
		<fieldset>
			<div class="inputName">[[$form_field.caption]]</div>
			<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
			<div class="inputField">{input property=$form_field.id template='expiration_date.tpl'}</div>
			{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
		</fieldset>
		{/capture}
	{elseif $form_field.type == 'location'}
		{input property=$form_field.id}
	{else}
		<fieldset>
			{assign var="fixInstructionsForComplexField" value=false}
			{if $form_field.type != 'complex'}
				{assign var="fixInstructionsForComplexField" value=true}
			{/if}
			{if $form_field.id == 'ListingLogo'}
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id template="logo_listing.tpl"}</div>
				{if $form_field.instructions && $fixInstructionsForComplexField}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			{else}
				<div class="inputName">[[$form_field.caption]]</div>
				<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
				<div class="inputField">{input property=$form_field.id}</div>
				{if $form_field.instructions && $fixInstructionsForComplexField}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
				{if in_array($form_field.type, array('multilist'))}
					<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
				{/if}
			{/if}
		</fieldset>
	{/if}
{/foreach}

{if !empty($showPic)}
	<fieldset>
		<div class="inputName"> [[Add Pictures]] </div>
		<div class="inputReq">&nbsp;</div>
		<div class="inputField">
			<div id="loading-progbar" class="add-picture-loading" style="display:none;">
				<img class="progBarImg" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
			</div>
			<div id="UploadPics" value="{$picValue}"></div>
		</div>
	</fieldset>
{/if}

{if $expirationField}{$expirationField}{/if}

{if $instructionsExist}
	{literal}
		<script type="text/javascript">
			function instructionFunc() {
				var elem = $(".instruction").prev();
				elem.children().focus(function() {
					$(this).parent().next(".instruction").children(".instr_block").show();
				});
				elem.children().blur(function() {
					$(this).parent().next(".instruction").children(".instr_block").hide();
				});
			}
			$("document").ready(function() {
				instructionFunc();
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