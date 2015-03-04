{capture name="input_text_field_from"} <input type="text" name="{$id}[not_less]" class="searchIntegerLess" value="{$value.not_less|escape:'html'}" /> {/capture}
{capture name="input_text_field_to"}   <input type="text" name="{$id}[not_more]" class="searchIntegerMore" value="{$value.not_more|escape:'html'}" /> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

[[$input_text_field_from to $input_text_field_to]]
