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
{elseif $error eq 'NOT_VALID_EMAIL_FORMAT'}
<p class="error">'[[{$field_caption}]]' [[is not valid email format]]</p>
{elseif $error eq 'NOT_SUPPORTED_VIDEO_FORMAT'}
<p class="error"> '[[{$field_caption}]]' [[this file is not in a supported video file format]]</p>
{elseif $error eq 'NOT_CONVERT_VIDEO'}
<p class="error">'[[{$field_caption}]]' [[Could not convert video file]]</p>
{elseif $error eq 'NO_POLLS_SELECTED'}
<p class="error">[[Polls are not selected]]</p>
{elseif $error eq 'Administrator Current Password is Incorrect'}
<p class="error">'[[{$field_caption}]]' [[Administrator Current Password is Incorrect]]</p>
{elseif $error eq 'Administrator Current Password is Required'}
<p class="error">'[[{$field_caption}]]' [[Administrator Current Password is Required]]</p>
{/if}
{/foreach}