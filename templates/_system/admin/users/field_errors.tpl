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
{elseif $error eq 'RESERVED_ID_VALUE'}
	<p class="error">[["$field_caption" current value is reserved for system]]</p>
{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
	<p class="error">'[[{$field_caption}]]' [[is not valid email format]]</p>
{elseif $error eq 'NOT_SUPPORTED_VIDEO_FORMAT'}
	<p class="error"> '[[{$field_caption}]]' [[this file is not in a supported video file format]]</p>
{elseif $error eq 'NOT_CONVERT_VIDEO'}
	<p class="error">'[[{$field_caption}]]' [[Could not convert video file]]</p>
{elseif $error eq 'Administrator Current Password is Incorrect'}
	<p class="error">'[[{$field_caption}]]' [[Administrator Current Password is Incorrect]]</p>
{elseif $error eq 'Administrator Current Password is Required'}
	<p class="error">'[[{$field_caption}]]' [[Administrator Current Password is Required]]</p>
{elseif $field_caption eq 'QTY_FIELDS_IS_EMPTY'}
	<p class="error">[[All the Qty fields in Volume Based Pricing should be filled]]</p>
{elseif $field_caption eq 'QTY_FIELDS_RANGE_ERROR'}
	<p class="error">[[The Qty fields should be filled From: min To: max but not vice versa]]</p>
{elseif $field_caption eq  'EXCEED_LISTING_DURATION'}
	<p class="error">[[The period of 'Featured' and 'Priority' options activity should not exceed the Listing Duration period]]</p>
{elseif $error eq 'NOT_STRING_ID_VALUE'}
	<p class="error">[[Use at least one A-Z letter value in the '$field_caption' field]]</p>
{elseif $error eq 'NOT_PLUS_VALUE'}
	<p class="error"> '{$field_caption}' [[The number you have entered is negative. Please enter a positive number]]</p>	
{elseif $field_caption eq 'UNLIMITED_PERIOD'}
	<p class="error">[[Recurring subscription period cannot be unlimited]]</p>
{elseif $error eq 'NOT_CORRECT_YOUTUBE_LINK'}
	<p class="error">[[YouTube link is not correct]]</p>
{elseif $field_caption eq 'BANNED_USER'}
	<p class="error">[[User's IP address was banned]]</p>
{elseif $error eq 'UPLOAD_ERR_INI_SIZE'}
	<p class="error"> '[[{$field_caption}]]' [[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
{elseif $error eq 'NOT_ACCEPTABLE_FILE_FORMAT'}
	<p class="error">[[Not supported file format]]</p>
{else}
	<p class="error">[[{$error}]]</p>
{/if}
{/foreach}