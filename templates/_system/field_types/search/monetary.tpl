
{capture name="input_text_field_from"} <div class="col-sm-3"> <input type="text" name="{$id}[monetary][not_less]" class="searchMoney form-control" value="{$value.monetary.not_less}" /><div class="form-group visible-xs"></div> </div> {/capture}
{capture name="input_text_field_to"}   <div class="col-sm-3"> <input type="text" name="{$id}[monetary][not_more]" class="searchMoney form-control" value="{$value.monetary.not_more}" /><div class="form-group visible-xs"></div> </div> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

<div class="salary-abbr row">[[$input_text_field_from]] <div class="col-sm-1 text-center">to</div><div class="form-group visible-xs"></div> [[$input_text_field_to]]
    <div class="col-sm-3 col-sm-offset-2">
        <select class="form-control" name='{$id}[monetary][currency]' id='{$id}_list'>
            <option value="">[[Select]] [[Currency]]</option>
            {foreach from=$list_currency item=list_curr}
                <option value='{$list_curr.currency_code}' {if $list_curr.currency_code == $value.monetary.currency || (!$value.monetary.currency && $list_curr.main==1)}selected="selected"{/if} >{tr mode="raw"}{$list_curr.currency_sign}{/tr}</option>
            {/foreach}
        </select>
    </div>
</div>
