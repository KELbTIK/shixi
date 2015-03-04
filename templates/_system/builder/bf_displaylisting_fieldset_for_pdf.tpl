{capture name=patternEvalReplace}/{literal}{([\w\d.]+)}{/literal}/i{/capture}
{capture name=replacementEvalReplace}{literal}{display property="\1"}{/literal}{/capture}
<table cellspacing="2" cellpadding="5">
	{if !empty($fields_active)}
		{foreach from=$fields_active item=theField}
			{if $theField.type eq "complex"}
				{assign var="emptyBlock" value="true"}
				{if $listing[$theField.id]}
					<tr>
						<td>
							<strong>[[{$theField.caption}]]:</strong><br />
							{display property=$theField.id customHtml=$theField.html template="complex_for_pdf.tpl"}
						</td>
					</tr>
				{/if}
			{elseif $theField.type eq "geo" && $theField.parentID}
				{assign var="emptyBlock" value="true"}
				{if !empty($listing[$theField.parentID][$theField.id])}
					<tr>
						<td>
							<strong>[[{$theField.caption}]]:</strong>
							{display property=$theField.id parent=$theField.parentID}
						</td>
					</tr>
				{/if}
			{elseif $theField.type eq "location"}
				{assign var="emptyBlock" value="true"}
				{if $theField.html}
					<tr>
						<td>
							<strong>[[{$theField.caption}]]:</strong>
							{capture name=replacementEvalReplaceLocation}{literal}{display property="\1" parent=$theField.id}{/literal}{/capture}
							{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.patternEvalReplace:$smarty.capture.replacementEvalReplaceLocation}
							{eval var=$newCustomHtml assign='evalResult'}
							{$evalResult|regex_replace:"/(^[\,\s]+)|(\s,)|([\,\s]+$)/im":""}&nbsp;
						</td>
					</tr>
				{/if}
			{elseif $theField.b_field_sid eq "location" || $theField.id eq "location"}
				{assign var="emptyBlock" value="true"}
				{if $theField.html}
					<tr>
						<td>
							<strong>[[Location]]:</strong>
							{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.patternEvalReplace:$smarty.capture.replacementEvalReplace}
							{eval var=$newCustomHtml assign='evalResult'}
							{$evalResult|regex_replace:"/(^[\,\s]+)|(\s,)|([\,\s]+$)/im":""}&nbsp;
						</td>
					</tr>
				{/if}
			{elseif $listingTypeID eq 'Resume' && ($theField.b_field_sid eq "desiredSalary" || $theField.id eq "desiredSalary") && (!empty($listing.DesiredSalaryType) || !empty($listing.DesiredSalary.value))}
				{assign var="emptyBlock" value="true"}
				{if $theField.html}
					<tr>
						<td>
							<strong>[[Desired Salary]]:</strong>
							{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.patternEvalReplace:$smarty.capture.replacementEvalReplace}
							{eval var=$newCustomHtml}
						</td>
					</tr>
				{/if}
			{elseif $listingTypeID eq 'Job' && ($theField.b_field_sid eq "customSalary" || $theField.id eq "customSalary") && (!empty($listing.SalaryType) || !empty($listing.Salary.value))}
				{assign var="emptyBlock" value="true"}
				{if $theField.html}
					<tr>
						<td>
							<strong>[[Salary]]:</strong>
							{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.patternEvalReplace:$smarty.capture.replacementEvalReplace}
							{eval var=$newCustomHtml}
						</td>
					</tr>
				{/if}
			{elseif $theField.html || $theField.b_field_sid|substr:0:9 eq "htmlBlock"}
				{assign var="emptyBlock" value="true"}
				{if $theField.html}
					<tr>
						<td>
							{assign var="newCustomHtml" value=$theField.html|regex_replace:$smarty.capture.patternEvalReplace:$smarty.capture.replacementEvalReplace}
							{eval var=$newCustomHtml}
						</td>
					</tr>
				{/if}
			{elseif $theField.b_field_sid eq "id" || $theField.id eq "id"}{assign var="emptyBlock" value="true"}
				{assign var="emptyBlock" value="true"}
				<tr>
					<td>
						<strong>[[{$listingTypeID} ID]]:</strong>
						[[$listing.id]]
					</td>
				</tr>
			{elseif $theField.b_field_sid eq "views" || $theField.id eq "views"}
				{assign var="emptyBlock" value="true"}
				<tr>
					<td>
						<strong>[[{$listingTypeID} Views]]:</strong>
						{$listing.views}
					</td>
				</tr>
			{elseif $theField.b_field_sid eq "posted" || $theField.id eq "posted"}
				{assign var="emptyBlock" value="true"}
				<tr>
					<td>
						<strong>[[Posted]]:</strong>
						[[$listing.activation_date]]
					</td>
				</tr>
			{elseif $theField.id eq "video" || $theField.type eq "video"}
				{assign var="emptyBlock" value="true"}
				<tr><td style="line-height: 0px;"></td></tr>
			{else}
				{if (!empty($listing[$theField.id]) && !is_array($listing[$theField.id])) || (is_array($listing[$theField.id]) && in_array($theField.type,array('video','file')) && !empty($listing[$theField.id].file_url)) || (!empty($listing[$theField.id]) && is_array($listing[$theField.id]) && !in_array($theField.type,array('video','file'))) }
					{assign var="emptyBlock" value="true"}
					<tr>
						<td>
							<strong>[[{$theField.caption}]]:</strong>
							{display property=$theField.id}
						</td>
					</tr>
				{else}
					{assign var="emptyBlock" value="false"}
				{/if}
			{/if}
		{/foreach}
		{if $emptyBlock == "false"}
			<tr><td style="line-height: 0px;"></td></tr>
		{/if}
	{else}
		<tr><td style="line-height: 0px;"></td></tr>
	{/if}
</table>
