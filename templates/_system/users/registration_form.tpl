<script type="text/javascript" >
function checkField( obj, name ) {
	if (obj.val() != "") {
		var options = {
			data: { isajaxrequest: 'true', type: name },
			success: showResponse
		};
		$("#registr-form").ajaxSubmit( options );
	}
	function showResponse(responseText, statusText, xhr, $form) {
		var mes = "";
		switch(responseText) {
			case 'NOT_VALID_EMAIL_FORMAT':
				mes = "[[Email format is not valid]]";
				break;
			case 'NOT_UNIQUE_VALUE':
				mes = "[[this value is already used in the system]]";
				break;
			case 'HAS_BAD_WORDS':
				mes = "[[has bad words]]";
				break;
			case '1':
				mes = "";
				break;
		}
		$("#am_" + name).text(mes);
	}
};
</script>
<div class="form-block center-block">
    <h2 class="title">[[{$user_group_info.name}]] [[Registration]]</h2>
    <hr/>
    {* SOCIAL PLUGIN: LOGIN BUTTONs *}
    <div class="soc_reg_form">
    {module name="social" function="social_plugins"}
    </div>
    {* / SOCIAL PLUGIN: LOGIN BUTTONs *}
    {foreach from=$errors item=error key=field_caption}
        <div class="error alert alert-danger">
            {if $error eq 'EMPTY_VALUE'}
                {if $field_caption == "Enter code from image"}
                    [[Enter Security code]]
                {else}
                    '[[{$field_caption}]]' [[is empty]]
                {/if}
            {elseif $error eq 'NOT_UNIQUE_VALUE'}
                '[[{$field_caption}]]' [[this value is already used in the system]]
            {elseif $error eq 'NOT_CONFIRMED'}
                '[[{$field_caption}]]' [[not confirmed]]
            {elseif $error eq 'NOT_VALID_ID_VALUE'}
                [[You can use only alphanumeric characters for]] '{$field_caption}'
            {elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
                [[Email format is not valid]]
            {elseif $error eq 'NOT_VALID'}
                {if $field_caption == "Enter code from image"}
                    [[Security code is not valid]]
                {else}
                    '[[{$field_caption}]]' [[is not valid]]
                {/if}
            {elseif $error eq 'LOCATION_NOT_EXISTS'}
                '[[{$field_caption}]]' [[value does not exist in the database]]
            {elseif $error eq 'HAS_BAD_WORDS'}
                '{$field_caption}' [[has bad words]]
            {else}
                [[{$error}]]
            {/if}
        </div>
    {/foreach}
    {* for social plugins *}
    {if $socialRegistration}
    <p>[[You’re almost registered on our site! Please complete the form below to finish the registration.]]</p>
    {/if}
    {* end of "for social plugins" *}
    <div class="alert alert-info">
        [[Fields marked with an asterisk (]]<span class="text-danger small">*</span>[[) are mandatory]]
    </div>
    <form method="post" class="form-horizontal" action="" enctype="multipart/form-data" onsubmit="return checkform();" id="registr-form">
    <input type="hidden" name="action" value="register" />
    {set_token_field}
        {foreach from=$form_fields item=form_field}
            {if $user_group_info.show_mailing_flag==0 && $form_field.id=="sendmail"}
            {elseif $form_field.type == 'location'}
                {input property=$form_field.id}
            {else}
                <div class="form-group has-feedback">
                    <label class="col-sm-3 control-label">[[$form_field.caption]] <span class="text-danger small">{if $form_field.is_required}*{/if}</span></label>
                    <div class="col-sm-8">
                        {*{$form_field.type}*}
                        {if $form_field.type == "video"}
                            <div class="file-field">
                                {input property=$form_field.id template="video_profile.tpl"}
                            </div>
                        {else}
                            {if $form_field.id == "sendmail"}
                                <div class="checkbox">
                                    <label>
                                        {input property=$form_field.id}
                                    </label>
                                </div>
                            {elseif $form_field.id == "FirstName" || $form_field.id == "LastName"}
                                {input property=$form_field.id}
                                <i class="fa fa-pencil form-control-feedback"></i>
                            {elseif  $form_field.type == "logo" || $form_field.type == "captcha"}
                                <div class="file-field">
                                    {input property=$form_field.id}
                                </div>
                            {else}
                                {input property=$form_field.id}
                            {/if}

                        {/if}
                    </div>
                </div>
                {*<fieldset>*}
                    {if $form_field.instructions}{assign var="instructionsExist" value="1"}{include file="../classifieds/instructions.tpl" form_field=$form_field}{/if}
                    {if in_array($form_field.type, array('multilist'))}
                        <div id="count-available-{$form_field.id}" class="mt-count-available"></div>
                    {/if}
                {*</fieldset>*}
            {/if}
        {/foreach}
        {if $terms_of_use_check != 0}
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3">
                    [[Accept terms of use]]
                    <span class="text-danger small">*</span>
                </label>
                <div class="inputField col-sm-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="terms" {if $smarty.post.terms}checked{/if} id="terms" />
                            <a class="text-underline" onclick="popUpWindow('{$GLOBALS.site_url}/terms-of-use-pop/', 512, '[[Terms of use]]')">[[Read terms of use]]</a>
                        </label>
                    </div>
                </div>
            </div>
        {/if}
            <div class="clearfix">
                <div class="inputField col-sm-8 col-sm-offset-3"><input type="hidden" name="user_group_id" value="{$user_group_info.id}" /> <input class="btn btn-default" type="submit" value="[[Register]]" /></div>
            </div>
    </form>
</div>
{if $instructionsExist}
	<script type="text/javascript">
		$("document").ready(function() {
			var elem = $(".instruction").prev();
			elem.children().focus(function() {
				$(this).parent().next(".instruction").children(".instr_block").show();
			});
			elem.children().blur(function() {
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
{/if}
<script language='JavaScript' type='text/javascript'>
	function checkform() {
		{if $terms_of_use_check != 0}
			if (!document.getElementById('terms').checked) {
				alert('[[Read terms of use]]');
				return false;
			}
		{/if}
		return true;
	}
</script>