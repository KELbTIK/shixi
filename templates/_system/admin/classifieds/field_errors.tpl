{foreach from=$errors item=error key=field_caption}
	{if $error eq 'EMPTY_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is empty]]</p>
	{elseif $error eq 'NOT_UNIQUE_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[this value is already used in the system]]</p>
	{elseif $error eq 'NOT_CONFIRMED'}
		<p class="error">'[[{$field_caption}]]' [[not confirmed]]</p>
	{elseif $error eq 'DATA_LENGTH_IS_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[length is exceeded]]</p>
	{elseif $error eq 'NOT_INT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an integer value]]</p>
	{elseif $error eq 'OUT_OF_RANGE'}
		<p class="error">'[[{$field_caption}]]' [[value is out of range]]</p>
	{elseif $error eq 'NOT_FLOAT_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not an float value]]</p>
	{elseif $error eq 'LOCATION_NOT_EXISTS'}
		<p class="error">'[[{$field_caption}]]' [[is unknown]]</p>
	{elseif $error eq 'NOT_VALID_ID_VALUE'}
		<p class="error">'[[{$field_caption}]]' [[is not valid]]</p>
	{elseif $error eq 'NOT_SUPPORTED_VIDEO_FORMAT'}
		<p class="error">'[[{$field_caption}]]' [[this file is not in a supported video file format]]</p>
	{elseif $error eq 'MAX_FILE_SIZE_EXCEEDED'}
		<p class="error">'[[{$field_caption}]]' [[filesize exceeds the quota]]</p>
	{elseif $error eq 'NO_SUCH_FILE'}
		<p class="error">'[[No such file found in the system]]</p>
	{elseif $error eq 'NOT_STRING_ID_VALUE'}
		<p class="error">[[Use at least one A-Z letter value in the '$field_caption' field]]</p>
	{elseif $error eq 'WRONG_DATE_FORMAT'}
		<p class="error">'[[{$field_caption}]]' [[The date format is incorrect]]</p>
	{elseif $error eq 'NOT_PLUS_VALUE'}
		<p class="error"> '[[{$field_caption}]]' [[The number you have entered is negative. Please enter a positive number]]</p>
	{elseif $error eq 'INVALID_EMAIL_TEMPLATE_SID_WAS_SPECIFIED'}
		<p class="error"> '[[Invalid Email Id has been specified]]</p>
	{elseif $error eq 'WRONG_GROUP'}
		<p class="error"> '[[Wrong Group has been specified]]</p>
	{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
		<p class="error"> '[[{$field_caption}]]' [[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{elseif $error eq 'NEWS_CATEGORY_NOT_SAVED'}
		<p class="error"> [[News category was not saved. Please try again.]]</p>
	{elseif $error eq 'NEWS_CATEGORY_NOT_DELETED'}
		<p class="error"> [[News category was not deleted. Please try again.]]</p>
	{elseif $error eq 'UNABLE_TO_ADD_ARTICLE'}
		<p class="error"> [[News was not saved. Please try again.]]</p>
	{elseif $error eq 'NO_ITEM_SID_PRESENT'}
		<p class="error">[[News ID is not found.]]</p>
	{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
		<p class="error">'[[{$field_caption}]]' [[YouTube link is not correct]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}