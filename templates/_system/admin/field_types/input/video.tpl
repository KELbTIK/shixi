{capture assign='listingId'}{if $listing_id}{$listing_id}{else}{$listing.id}{/if}{/capture}
{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}

	{foreach from=$errors key=key item=error}
		{if $key == 'NOT_SUPPORTED_VIDEO_FORMAT' || $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<p class="error">[[Not supported video format]]</p>
		{elseif $key === 'UPLOAD_ERR_INI_SIZE'}
			<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
		{else}
			<p class="error">{$key}</p>
		{/if}
	{/foreach}

	<input type="file"
		   field_id="{$id}"
		   field_action="upload_classifieds_video"
		   field_target="video_field_content_{$id}"
		   name="{$id}"
		   class="autouploadField"
		   id="input_video_{$id}"
		   {if $value.file_url}style="display:none;"{/if}/>
	{if !$value.file_url}
		<small>([[max.]] {$uploadMaxFilesize} M)</small>
	{/if}

	<span id="video_uploader_image_{$id}" style="display: none;">&nbsp;<img src="{$GLOBALS.user_site_url}/templates/_system/main/images/ajax_preloader_circular_16.gif" /></span>

		{if $value.file_name ne null && $url != '/add-listing/'}
			<div id="classifieds_video_{$id}" style="float:left;">
				<a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?videoFileId={$value.file_id}&listing_id={$listingId}&amp;field_id={$id}', 330, 305, ''); return false;" href="{$GLOBALS.site_url}/video-player/?listing_id={$listingId}&amp;field_id={$id}"> [[Watch a video]]</a>
				|
				{if $copy_listing ne null}
					<a href="{$GLOBALS.site_url}/clone-job/?listing_id={$listingId}&amp;action=delete&amp;field_id={$id}">[[Remove]]</a>
					<input type="hidden" name="{$id}_hidden" value="1" />
				{else}
					<a class="delete_classifieds_video" listing_id="{$listingId}" field_id="{$id}" file_id="{$value.file_id}" href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listingId}&amp;field_id={$id}">[[Remove]]</a>
				{/if}
				<br/><br/>
			</div>
			<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
		{/if}
{else}
	<div id="video_field_content_{$id}">
		<div class="errors"></div>

		<input type="file"
			field_id="{$id}"
			field_action="upload_classifieds_video"
			field_target="video_field_content_{$id}"
			name="{$id}"
			class="autouploadField"
			id="input_video_{$id}"
			{if $value.file_url}style="display:none;"{/if} />
		{if !$value.file_url}
			<small>([[max.]] {$uploadMaxFilesize} M)</small>
		{/if}
		
		<span id="video_uploader_image_{$id}" style="display: none;">&nbsp;<img src="{$GLOBALS.user_site_url}/templates/_system/main/images/ajax_preloader_circular_16.gif" /></span>

		{if $value.file_name ne null && $url != '/add-listing/'}
			<div id="classifieds_video_{$id}" style="float:left;">
				<a onclick="popUpWindow('{$GLOBALS.site_url}/video-player/?listing_id={$listingId}&amp;field_id={$id}', 330, 305, ''); return false;" href="{$GLOBALS.site_url}/video-player/?listing_id={$listingId}&amp;field_id={$id}"> [[Watch a video]]</a>
				|
				{if $copy_listing ne null}
					<a href="{$GLOBALS.site_url}/clone-job/?listing_id={$listingId}&amp;action=delete&amp;field_id={$id}">[[Remove]]</a>
					<input type="hidden" name="{$id}_hidden" value="1" />
				{else}
					<a class="delete_classifieds_video" listing_id="{$listingId}" field_id="{$id}" file_id="{$value.file_id}" href="{$GLOBALS.site_url}/classifieds/delete-uploaded-file/?listing_id={$listingId}&amp;field_id={$id}">[[Remove]]</a>
				{/if}
				<br/><br/>
			</div>
			<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
		{/if}
	</div>

	<script>
		{literal}
		// check temporary uploaded data of field
		$(function() {
			{/literal}
				getClassifiedsVideoData('{$id}', '{$listingId}');
			{literal}
		});

		{/literal}
	</script>

{/if}