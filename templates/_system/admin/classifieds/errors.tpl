{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_CONFIRMED'}
		<p class="error">'[[{$field_caption}]]' [[not confirmed]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[invalid value]]</p>
	{elseif $error eq 'NOT_PLUS_VALUE'}
		<p class="error"> '[[{$field_caption}]]' [[The number you have entered is negative. Please enter a positive number]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an float value]]</p>
	{else}
		<p class="error">[[{$field_caption}]] [[{$error}]]</p>
	{/if}
{/foreach}