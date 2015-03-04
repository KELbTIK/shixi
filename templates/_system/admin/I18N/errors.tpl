{foreach from=$errors item=error}
	{if $error eq 'LANGUAGE_ID_IS_EMPTY'}
		<p class="error">'[[Language ID]]' [[is empty]]</p>
	{elseif $error eq 'LANGUAGE_ID_CONTAINS_NOT_ALLOWED_SYMBOLS'}
		<p class="error">'[[Language ID]]' [[contains inappropriate symbols]]</p>
	{elseif $error eq 'LANGUAGE_CAPTION_IS_EMPTY'}
		<p class="error">'[[Language Caption]]' [[is empty]]</p>
	{elseif $error eq 'TOO_LONG_LANGUAGE_ID'}
		<p class="error">'[[Language ID]]' [[too long value]]</p>
	{elseif $error eq 'LANGUAGE_FILE_IS_INVALID'}
		<p class="error">[[Language file is invalid]]</p>
	{elseif $error eq 'LANGUAGE_ALREADY_EXISTS'}
		<p class="error">[[Language exists in the system]]</p>
	{elseif $error eq 'TOO_LONG_LANGUAGE_CAPTION'}
		<p class="error">'[[Language Caption]]' [[too long value]]</p>
	{elseif $error eq 'TOO_LONG_DATE_FORMAT'}
		<p class="error">'[[Date Format]]' [[too long value]]</p>
	{elseif $error eq 'INVALID_DATE_FORMAT'}
		<p class="error">'[[Language Caption]]' [[is not valid]]</p>
	{elseif $error eq 'INVALID_DECIMALS_SEPARATOR'}
		<p class="error">'[[Decimal Separator]]' [[is not valid]]</p>
	{elseif $error eq 'INVALID_THOUSANDS_SEPARATOR'}
		<p class="error">'[[Thousands Separator]]' [[is not valid]]</p>
	{elseif $error eq 'INVALID_DECIMALS'}
		<p class="error">'[[Decimals]]' [[is not valid]]</p>
	{elseif $error eq 'DECIMALS_IS_EMPTY'}
		<p class="error">'[[Decimals]]' [[is empty]]</p>
	{elseif $error eq 'PHRASE_ALREADY_EXISTS'}
		<p class="error">[[This phrase already exists in the system and cannot be added]]</p>
	{elseif $error eq 'TOO_LONG_PHRASE_ID'}
		<p class="error">'[[Phrase ID]]' [[You have exceeded the limit of maximum allowed symbols for the field]]</p>
	{elseif $error eq 'PHRASE_ID_IS_EMPTY'}
		<p class="error">'[[Phrase ID]]' [[is empty]]</p>
	{elseif is_array($error)}
		{if $error.0 eq 'TOO_LONG_TRANSLATION'}
			<p class="error">'[[{$error.1}]]' [[You have exceeded the limit of maximum allowed symbols for the field]]</p>
		{/if}
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}
