{foreach from=$form_fields item=form_field}
	{$form_field=$form_field scope=global}
	{if $form_field.id == 'video' || $form_field.id == 'youtube'}
		{if $extraInfo.video}
			<div class="form-group has-feedback">
				<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</div>
		{/if}
	{elseif ($listing_type_id == "Job" || $listing.type.id == "Job") && $form_field.id == "anonymous"}
		{* this empty place of 'anonymous' checkbox in 'Job' listing *}
	{elseif ($listing_type_id == "Resume" || $listing.type.id == "Resume") && $form_field.id == "anonymous"}
			<div class="form-group has-feedback">
				<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</div>
	{elseif $form_field.id == "access_type"}
		{if $listing_type_id != "Job" && $listing.type.id != "Job"}{* *}
			<div class="form-group has-feedback">
				<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id template='resume_access.tpl'}</div>
				{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			</div>
		{/if}
	{elseif ($listingTypeID == "Job" || $listing.type.id == "Job") && $form_field.id == 'ApplicationSettings'}
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
			<div class="inputField col-sm-8">{input property=$form_field.id template='applicationSettings.tpl'}</div>
			{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
		</div>
	{elseif ($listingTypeID == "Job" || $listing.type.id == "Job") && $form_field.id == 'expiration_date'}
		{capture assign="expirationField"}
		<div class="form-group has-feedback">
			<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>

			<div class="inputField col-sm-8">{input property=$form_field.id template='expiration_date.tpl'}</div>
			{if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
		</div>
		{/capture}
	{elseif $form_field.type == 'location'}
		{input property=$form_field.id}
	{else}
		<div class="form-group has-feedback">
			{assign var="fixInstructionsForComplexField" value=false}
			{if $form_field.type != 'complex'}
				{assign var="fixInstructionsForComplexField" value=true}
			{/if}
			{if $form_field.id == 'ListingLogo'}
				<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id template="logo_listing.tpl"}</div>
				{if $form_field.instructions && $fixInstructionsForComplexField}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
			{else}
				<label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
				<div class="inputField col-sm-8">{input property=$form_field.id}
				{if $form_field.instructions && $fixInstructionsForComplexField}{assign var="instructionsExist" value="1"}{include file="instructions.tpl" form_field=$form_field}{/if}
				{if in_array($form_field.type, array('multilist'))}
					<div id="count-available-{$form_field.id}" class="mt-count-available"></div>
				{/if}
				</div>
			{/if}
		</div>
	{/if}
{/foreach}

{if !empty($showPic)}
	<div class="form-group has-feedback">
		<label class="inputName col-sm-3 control-label"> [[Add Pictures]] </label>
		<div class="inputReq">&nbsp;</div>
		<div class="inputField col-sm-8">
			<div id="loading-progbar" class="add-picture-loading" style="display:none;">
				<img class="progBarImg" src="{$GLOBALS.site_url}/system/ext/jquery/progbar.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
			</div>
			<div id="UploadPics" value="{$picValue}"></div>
		</div>
	</div>
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