{capture name="input_text_field_from"} <input type="text" class="form-control datepicker" name="{$id}[not_less]" value="{$value.not_less|date_format:{$GLOBALS.current_language_data.date_format}}"  id="{$id}_notless"/><i class="fa fa-calendar form-control-feedback"></i> {/capture}

{capture name="input_text_field_to"}   <input type="text" class="form-control datepicker" name="{$id}[not_more]" value="{$value.not_more|date_format:{$GLOBALS.current_language_data.date_format}}"  id="{$id}_notmore"/><i class="fa fa-calendar form-control-feedback"></i> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

<div class="row">
    <div class="col-sm-5">[[$input_text_field_from]]</div>
    <div class="col-sm-2 text-center">to</div>
    <div class="col-sm-5">[[$input_text_field_to]]</div>
</div>