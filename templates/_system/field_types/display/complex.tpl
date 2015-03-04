{assign var="complexField" value=$id scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}
{if $customHtml}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		{capture name=EvalReplacePattern}/{literal}{([\w\d]+)}{/literal}/i{/capture}
		{capture name=EvalReplaceReplacement}{literal}{display property="\1" complexParent=$complexField complexStep=$complexElementKey}{/literal}{/capture}
		{assign var="newCustomHtml" value=$customHtml|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
		{eval var=$newCustomHtml}
	{/foreach}
	{*{eval var=$customHtml}*}

{elseif $complexField == "Education"}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="leftDisplaySIde">
			<span class="strong">{display property="EntranceDate" complexParent=$complexField complexStep=$complexElementKey} - {display property=$form_fields.GraduationDate.id complexParent=$complexField complexStep=$complexElementKey}</span>
		</div>
		<div class="rightDisplaySIde">
			{display property=$form_fields.InstitutionName.id complexParent=$complexField complexStep=$complexElementKey}<br/>
			{display property=$form_fields.Major.id complexParent=$complexField complexStep=$complexElementKey}<br/>
			{display property=$form_fields.DegreeLevel.id complexParent=$complexField complexStep=$complexElementKey }
		</div>
		<div class="clrBorder"><br/></div>
	{/foreach}

{elseif $complexField == "WorkExperience"}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="leftDisplaySIde">
			<span class="strong">{display property=$form_fields.StartDate.id complexParent=$complexField complexStep=$complexElementKey} - {display property=$form_fields.EndDate.id complexParent=$complexField complexStep=$complexElementKey}</span>
		</div>
		<div class="rightDisplaySIde">
			{display property=$form_fields.JobTitle.id complexParent=$complexField complexStep=$complexElementKey}<br/>
			{display property=$form_fields.CompanyName.id complexParent=$complexField complexStep=$complexElementKey} | {display property=$form_fields.Industry.id complexParent=$complexField complexStep=$complexElementKey}<br/>
			{display property=$form_fields.Description.id complexParent=$complexField complexStep=$complexElementKey}
		</div>
		<div class="clrBorder"><br/></div>
	{/foreach}

{else}

	{foreach from=$complexElements key="complexElementKey" item="complexElementItem"}
		<div class="complexField">
			{foreach from=$form_fields item=form_field}
				<fieldset>
					<span class="strong"> {tr}{$form_field.caption}{/tr|escape:'html'}:&nbsp;</span>
					{display property=$form_field.id complexParent=$complexField complexStep=$complexElementKey}
				</fieldset>
			{/foreach}
		</div>
	{/foreach}

{/if}
{assign var="complexField" value=false scope=global} {* nwy: Если не очистить переменную то в последующих полях начинаются проблемы (некоторые воспринимаются как комплексные)*}