{foreach from=$field_errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		{if $field_caption == "Enter code from image"}
			<p class="error">[[Enter Security code]]</p>
		{else}
			<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
		{/if}
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'{$field_caption}' [[this value is already used in the system]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'{$field_caption}' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an integer value]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'{$field_caption}' [[value is out of range]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'{$field_caption}' [[is not an float value]]</p>
	{elseif $error eq 'LOCATION_NOT_EXISTS'}
		<p class="error">'[[{$field_caption}]]' [[is unknown]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">'{$field_caption}' [[is not valid]]</p>
	{elseif $error eq 'NOT_VALID'}
		{if $field_caption == "Enter code from image"}
			<p class="error">[[Security code is not valid]]</p>
		{else}
			<p class="error">'{$field_caption}' [[is not valid]]</p>
		{/if}
	{elseif $error eq 'HAS_BAD_WORDS'}
		<p class="error">'{$field_caption}' [[has bad words]]</p>
	{else}
		<p class="error">{$error}</p>
	{/if}
{/foreach}
