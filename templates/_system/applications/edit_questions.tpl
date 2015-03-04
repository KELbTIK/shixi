<div class="BreadCrumbs">
	<p><a href="{$GLOBALS.site_url}/my-account/">[[My Account]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/">[[Screening Questionnaires]]</a> &#187; <a href="{$GLOBALS.site_url}/screening-questionnaires/edit/{$sid}/">[[Edit Questionnaire]]</a> &#187; [[Questions]]</p>
</div>
<h1>[[Questions of]] &quot;{$questionnarieInfo.name}&quot;</h1>
{include file="../classifieds/field_errors.tpl" field_errors=$errors}
{if $edit}
	<p class="message">[[Your changes were successfully saved]]</p>
{/if}
<form method="post" action="">
<input type="hidden" name="action" value="add" />
{foreach from=$form_fields key=field_name item=form_field}
{if $form_field.id == 'type'}
	<fieldset>
		<div class="inputName">[[$form_field.caption]]</div>
		<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
		<div class="inputField">{input property=$form_field.id  template='radio.tpl'}</div>
	</fieldset>
{else}
	<fieldset>
		<div class="inputName">[[$form_field.caption]]</div>
		<div class="inputReq">&nbsp;{if $form_field.is_required}*{/if}</div>
		<div class="inputField">{input property=$form_field.id}</div>
	</fieldset>
{/if}
{/foreach}
<fieldset id="boolean" {if !$answer_boolean}style="display:none"{/if}>
	<div class="inputName">[[Answers]]</div>
	<div class="inputReq">&nbsp;</div>
	<div class="inputField">
		<div style="width:50px; float:left; padding: 10px 0 0 0;">[[Yes]]&nbsp;<input type="hidden" name="answer_boolean[]" value="Yes" /></div>
		<div>
		<select name="score_boolean[]">
			<option value="no" {if $score_boolean.0 == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
			<option value="0" {if $score_boolean.0 == 0} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			<option value="1" {if $score_boolean.0 == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			<option value="2" {if $score_boolean.0 == 2} selected="selected"{/if}>[[Good - 2]]</option>
			<option value="3" {if $score_boolean.0 == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			<option value="4" {if $score_boolean.0 == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		</select>
		</div>
		<div class="clr"></div>
		<div style="width:50px; float:left; padding: 10px 0 0 0;">[[No]]&nbsp;<input type="hidden" name="answer_boolean[]" value="No" /></div>
		<div>
		<select name="score_boolean[]">
			<option value="no" {if $score_boolean.1 == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
			<option value="0" {if $score_boolean.1 == 0} selected="selected"{/if}>[[Not acceptable - 0]]</option>
			<option value="1" {if $score_boolean.1 == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
			<option value="2" {if $score_boolean.1 == 2} selected="selected"{/if}>[[Good - 2]]</option>
			<option value="3" {if $score_boolean.1 == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
			<option value="4" {if $score_boolean.1 == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
		</select>
		</div>
	</div>
</fieldset>
<fieldset id="answers"  {if !$answers}style="display:none"{/if}>
	<div class="inputName">[[Answers]]</div>
	<div class="inputReq">&nbsp;</div>
	<div class="inputField">
		{if $answers}
            {foreach from=$answers key=key item=answer}
                <div id="answerBlock{$key}">
                    <div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="{$answer}" />&nbsp;</div>
                    <div style="float:left;">
                        <select name="score[]">
                            <option value="no" {if $score.$key == 'no'} selected="selected"{/if}>[[Don’t assign score]]</option>
                            <option value="0" {if $score.$key == '0'} selected="selected"{/if}>[[Not acceptable - 0]]</option>
                            <option value="1" {if $score.$key == 1} selected="selected"{/if}>[[Acceptable - 1]]</option>
                            <option value="2" {if $score.$key == 2} selected="selected"{/if}>[[Good - 2]]</option>
                            <option value="3" {if $score.$key == 3} selected="selected"{/if}>[[Very Good - 3]]</option>
                            <option value="4" {if $score.$key == 4} selected="selected"{/if}>[[Excellent - 4]]</option>
                        </select>
                    </div>
                    <div>&nbsp;&nbsp;<a href="#" onclick="deleteAnswerBlock('answerBlock{$key}'); return false;" class="remove">[[Delete]]</a></div>
                </div>
                <div class="clr"></div>
            {/foreach}
            <div class="clr"></div>
            <div id="answerAdd"></div>
            <div id="add_answer"><a href="#" onclick="addAnswerBlock();  return false;" class="add">[[Add Answer]]</a></div>
        {else}
            <div id="answerBlock">
                <div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="" />&nbsp;</div>
                <div style="float:left;">
                    <select name="score[]">
                        <option value="no">[[Don’t assign score]]</option>
                        <option value="0">[[Not acceptable - 0]]</option>
                        <option value="1">[[Acceptable - 1]]</option>
                        <option value="2">[[Good - 2]]</option>
                        <option value="3">[[Very Good - 3]]</option>
                        <option value="4">[[Excellent - 4]]</option>
                    </select>
                </div>
                <div>&nbsp;&nbsp;<a href="#" onclick="deleteAnswerBlock('answerBlock'); return false;" class="remove">[[Delete]]</a></div>
            </div>
            <div class="clr"></div>
            <div id="answerAdd"></div>
            <div id="add_answer"><a href="#" onclick="addAnswerBlock();  return false;" class="add">[[Add Answer]]</a></div>
        {/if}
		<div id="answerBlockNone" style="display: none">
		<div style="float:left;padding-bottom:10px"><input type="text" name="answer[]" value="" />&nbsp;</div>
		<div style="float:left;">
		<select name="score[]">
			<option value="no" >[[Don’t assign score]]</option>
			<option value="0">[[Not acceptable - 0]]</option>
			<option value="1">[[Acceptable - 1]]</option>
			<option value="2">[[Good - 2]]</option>
			<option value="3">[[Very Good - 3]]</option>
			<option value="4">[[Excellent - 4]]</option>
		</select>
		</div>
		</div>
	</div>
</fieldset>
<fieldset>
	<div class="inputName">&nbsp;</div>
	<div class="inputReq">&nbsp;</div>
	<div class="inputField"><input type="submit" name="action_add" value="[[Add]]" class="button" /></div>
</fieldset>
</form>
<br /><br />
<table cellspacing="0" id="edit-questions">
	<thead>
		<tr>
			<th class="tableLeft">&nbsp;</th>
			<th width="5%" colspan="2">[[Order]]</th>
			<th width="25%">&nbsp;[[Name]]</th>
			<th width="20%">[[Required]]</th>
			<th width="40%">[[Answer Type]]</th>
			<th width="10%" colspan="2">[[Actions]]</th>
			<th class="tableRight">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$questions item=question  name=question_block}
		<tr class="{cycle values = 'evenrow,oddrow'}">
			<td></td>
			<td>
                {if $smarty.foreach.question_block.iteration < $smarty.foreach.question_block.total}
               		<a href="?action=move_down&amp;question={$question.sid}"><img src="{image}b_down_arrow.gif" border="0" alt=""/></a>
                {/if} 
            </td>
            <td>
                {if $smarty.foreach.question_block.iteration > 1}
                	<a href="?action=move_up&amp;question={$question.sid}"><img src="{image}b_up_arrow.gif" border="0" alt="" /></a>
                {/if} 
            </td>
			<td><span class="strong">{$question.caption}</span></td>
			<td>{if $question.is_required}[[Yes]]{else}[[No]]{/if}</td>
			<td>{if $question.type=='boolean'}[[Yes/No]]{elseif $question.type=='string'}[[Text]]{elseif $question.type=='multilist'}[[List of answers with multiple choice]]{elseif  $question.type=='list'}[[List of answers with single choice]]{/if}</td>
			<td><a href="{$GLOBALS.site_url}/edit-question/{$question.sid}">[[Edit]]</a></td>
			<td><a href="{$GLOBALS.site_url}/edit-questions/{$question.questionnaire_sid}/{$question.sid}/?action=delete" onclick="return confirm('{capture name="areYouSureToDelete"}[[Are you sure you want to delete this question?]]{/capture}{$smarty.capture.areYouSureToDelete|escape:'quotes'}')">[[Delete]]</a></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="9" class="separateListing"></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{literal}
<script type="text/javascript">
<!--
var i = 1;
function addAnswerBlock() {
	var id = "answerAdd"+i;
	$("<div id='"+id+"'><\/div>").appendTo("#answerAdd");
	var block = $('#answerBlockNone').clone();
	block.appendTo('#'+id); 
	block.show();
	$('#'+ id +' input[type=text]').val('');
	$('#'+ id).html($('#'+ id).html() + "<div>&nbsp;&nbsp;<a href='#' onclick=\"deleteAnswerBlock('"+id+"'); return false;\" class=\"remove\">{/literal}[[Delete]]{literal}<\/a><\/div><div class='clr'><\/div>");
	i++;
}

function deleteAnswerBlock(id) {
	$('#'+ id).remove();
}
//-->
</script>
{/literal}