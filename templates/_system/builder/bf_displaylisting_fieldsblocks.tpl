{if $theField.type eq "complex"}
	{if $listing[$theField.id]}
		<div class="displayFieldBlock">
			<h3>[[{$theField.caption}]]:</h3>
			<div class="displayField">{display property=$theField.id customHtml=$theField.html}</div>
		</div>
	{/if}
{elseif $theField.type eq "geo" && $theField.parentID}
{if !empty($listing[$theField.parentID][$theField.id])}
<div class="displayFieldBlock">
	<h3>[[{$theField.caption}]]:</h3>
	<div class="displayField">{display property=$theField.id parent=$theField.parentID}</div>
</div>
{/if}
{elseif $theField.type eq "location"}
	{capture name=EvalReplacePattern}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
	{capture name=EvalReplaceReplacement}{literal}{display property="\1" parent=$theField.id}{/literal}{/capture}
	{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
	{eval var=$newCustomHtml assign='evalResult'}
	{if $theField.html && '/\w/'|preg_match:$evalResult}
	<div class="displayFieldBlock">
		<h3>[[{$theField.caption}]]:</h3>
		<div class="displayField">
			{$evalResult|regex_replace:'/(^[\,\s]+)|(\s,)|([\,\s]+$)/im':''}&nbsp;
		</div>
	</div>
	{/if}
{elseif $theField.b_field_sid eq "location" || $theField.id eq "location"}
	{capture name=EvalReplacePattern}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
	{capture name=EvalReplaceReplacement}{literal}{display property="\1"}{/literal}{/capture}
	{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
	{eval var=$newCustomHtml assign='evalResult'}
	{if $theField.html && '/\w/'|preg_match:$evalResult}
	<div class="displayFieldBlock">
		<h3>[[Location]]:</h3>
		<div class="displayField">
			{$evalResult|regex_replace:'/(^[\,\s]+)|(\s,)|([\,\s]+$)/im':''}&nbsp;
		</div>
	</div>
	{/if}
{elseif $listingTypeID eq 'Resume' && ($theField.b_field_sid eq "desiredSalary" || $theField.id eq "desiredSalary") && (!empty($listing.DesiredSalaryType) || !empty($listing.DesiredSalary.value))}
	{if $theField.html}
	<div class="displayFieldBlock">
		<h3>[[Desired Salary]]:</h3>
		<div class="displayField">
			{capture name=EvalReplacePattern}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
			{capture name=EvalReplaceReplacement}{literal}{display property="\1"}{/literal}{/capture}
			{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
			{eval var=$newCustomHtml}
		</div>
	</div>
	{/if}
{elseif $listingTypeID eq 'Job' && ($theField.b_field_sid eq "customSalary" || $theField.id eq "customSalary") && (!empty($listing.SalaryType) || !empty($listing.Salary.value))}
	{if $theField.html}
	<div class="displayFieldBlock">
		<h3>[[Salary]]:</h3>
		<div class="displayField">
			{capture name=EvalReplacePattern}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
			{capture name=EvalReplaceReplacement}{literal}{display property="\1"}{/literal}{/capture}
			{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
			{eval var=$newCustomHtml}
		</div>
	</div>
	{/if}
{elseif $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
	{if $theField.html}
	<div class="displayFieldBlock">
		{capture name=EvalReplacePattern}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
		{capture name=EvalReplaceReplacement}{literal}{display property="\1"}{/literal}{/capture}
		{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.EvalReplacePattern:$smarty.capture.EvalReplaceReplacement}
		{eval var=$newCustomHtml}
	</div>
	{/if}
{elseif $theField.b_field_sid eq "id" || $theField.id eq "id"}
	<div class="displayFieldBlock">
		<h3>[[{$listingTypeID} ID]]:</h3>
		<div class="displayField">[[$listing.id]]</div>
	</div>
{elseif $theField.b_field_sid eq "views" || $theField.id eq "views"}
	<div class="displayFieldBlock">
		<h3>[[{$listingTypeID} Views]]:</h3>
		<div class="displayField">{$listing.views}</div>
	</div>
{elseif $theField.b_field_sid eq "posted" || $theField.id eq "posted"}
	<div class="displayFieldBlock">
		<h3>[[Posted]]:</h3>
		<div class="displayField">[[$listing.activation_date]]</div>
	</div>
{else}
	{if (!empty($listing[$theField.id]) && !is_array($listing[$theField.id])) || (is_array($listing[$theField.id]) && in_array($theField.type,array('video','file')) && !empty($listing[$theField.id].file_url)) || (!empty($listing[$theField.id]) && is_array($listing[$theField.id]) && !in_array($theField.type,array('video','file'))) }
		<div class="displayFieldBlock">
			<h3>[[{$theField.caption}]]:</h3>
			<div class="displayField">{display property=$theField.id}</div>
		</div>
	{/if}
{/if}
