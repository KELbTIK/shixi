{foreach from=$errors item=error key=field_caption}
	<p class="error">
		{if $error eq 'EMPTY_VALUE'}
			'[[{$field_caption}]]' [[is empty]]
		{elseif $error eq 'NOT_UNIQUE_VALUE'}
			'{$field_caption}' [[this value is already used in the system]]
		{elseif $error eq 'NOT_CONFIRMED'}
			'{$field_caption}' [[not confirmed]]
		{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
			'{$field_caption}' [[length is exceeded]]
		{elseif $error eq 'NOT_INT_VALUE'}
			'{$field_caption}' [[is not an integer value]]
		{elseif $error eq 'OUT_OF_RANGE'}
			'{$field_caption}' [[value is out of range]]
		{elseif $error eq 'NOT_FLOAT_VALUE'}
			'{$field_caption}' [[is not an float value]]
		{elseif $error eq 'LOCATION_NOT_EXISTS'}
			'[[{$field_caption}]]' [[is unknown]]
		{elseif $error eq 'NOT_VALID_ID_VALUE'}
			[[You can use only alphanumeric characters for]] '{$field_caption}'
		{elseif $error eq 'NOT_SUPPORTED_IMAGE_FORMAT'}
			'{$field_caption}' - [[Image format is not supported]]
		{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
			[[Email format is not valid]]
		{elseif $error eq 'HAS_BAD_WORDS'}
			'{$field_caption}' [[has bad words]]
		{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
			[[YouTube link is not correct]]
		{elseif $error eq 'NOT_VALID'}
			{if $field_caption == "Enter code from image"}
				[[Security code is not valid]]
			{else}
				'[[{$field_caption}]]' [[is not valid]]
			{/if}
		{else}
			[[{$error}]]
		{/if}
	</p>
{/foreach}


