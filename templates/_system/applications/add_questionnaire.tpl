<h1>[[Create New Questionnaire]]</h1>
{foreach from=$errors item=error key=field_caption}

    <div class="error alert alert-danger">'{$field_caption}' [[is empty]]</div>
    {if $error eq 'EMPTY_VALUE'}<div class="error alert alert-danger">'{$field_caption}' [[is empty]]</div>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}<div class="error alert alert-danger">'{$field_caption}' [[this value is already used in the system]]</div>
	{elseif $error eq 'NOT_FLOAT_VALUE'}<div class="error alert alert-danger">'{$field_caption}' [[is not an float value]]</div>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}<div class="error alert alert-danger">'{$field_caption}' [[is not valid]]</div>
	{elseif $error eq 'CAN_NOT_EQUAL_NULL'}<div class="error alert alert-danger">'{$field_caption}' [[can not equal "0"]]</div>
	{/if}
{/foreach}
<form method="post" action="" class="form-horizontal">
    {if $action == 'edit'}
        <input type="hidden" name="submit" value="edit" />
    {else}
        <input type="hidden" name="submit" value="add" />
    {/if}
    {foreach from=$form_fields item=form_field}
        {if $form_field.id == 'email_text_more'}
            <div class="form-group has-feedback" id="email_text_more_set" {if $request.send_auto_reply_more != 1}style='display:none'{/if}>
                <label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
                <div class="inputField col-sm-8">{input property=$form_field.id}</div>
            </div>
        {elseif $form_field.id == 'email_text_less'}
            <div class="form-group has-feedback" id="email_text_less_set" {if $request.send_auto_reply_less != 1}style="display:none"{/if} >
                <label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
                <div class="inputField col-sm-8">{input property=$form_field.id}</div>
            </div>
        {elseif $form_field.id == "send_auto_reply_more"}
            <p><span class="strong">[[Send Auto-Reply email to candidates whose score is]]</span></p>
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
                <div class="inputField col-sm-8">{input property=$form_field.id}</div>
            </div>
        {else}
            <div class="form-group has-feedback">
                <label class="inputName col-sm-3 control-label">[[$form_field.caption]] <span class="small text-danger">{if $form_field.is_required}*{/if}</span></label>
                <div class="inputField col-sm-8">{input property=$form_field.id}</div>
            </div>
        {/if}
    {/foreach}
    <div class="form-group has-feedback">
        <div class="inputName">&nbsp;</div>
        <div class="inputReq">&nbsp;</div>
        {if $action == 'edit'}
            <div class="inputField"><input type="submit" name="action_add" value="[[Edit]]" class="btn btn-default" /></div>
        {else}
            <div class="inputField"><input type="submit" name="action_add" value="[[Add]]" class="btn btn-success" /></div>
        {/if}
    </div>
</form>
{literal}
<script type="text/javascript">
<!--
$("#send_auto_reply_more").bind("click", function() {
   	$("#email_text_more_set").css('display', this.checked ? 'block' : 'none');
})

$("#send_auto_reply_less").bind("click", function() {
   	$("#email_text_less_set").css('display', this.checked ? 'block' : 'none');
})
//-->
</script>
{/literal}