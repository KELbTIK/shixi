{capture name="input_text_field_from"} <input type="text" class="searchActDate" name="{$id}[not_less]" value="{$value.not_less|date_format:{$GLOBALS.current_language_data.date_format}}"  id="{$id}_notless"/> {/capture}

{capture name="input_text_field_to"}   <input type="text" class="searchActDate" name="{$id}[not_more]" value="{$value.not_more|date_format:{$GLOBALS.current_language_data.date_format}}"  id="{$id}_notmore"/> {/capture}

{assign var="input_text_field_from" value="`$smarty.capture.input_text_field_from`"}
{assign var="input_text_field_to" value="`$smarty.capture.input_text_field_to`"}

[[$input_text_field_from to $input_text_field_to]]
