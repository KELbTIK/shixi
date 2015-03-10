{capture name="input_text_field_from"} <div class="col-sm-3"><input type="text" name="{$id}[not_less]" class="searchIntegerLess form-control" value="{$value.not_less|escape:'html'}" /></div> <div class="form-group visible-xs"></div>  {/capture}
{capture name="input_text_field_to"}   <div class="col-sm-3"><input type="text" name="{$id}[not_more]" class="searchIntegerMore form-control" value="{$value.not_more|escape:'html'}" /></div> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

<div class="row">[[$input_text_field_from]] <div class="col-sm-1 text-center">to</div> [[$input_text_field_to]]</div>
