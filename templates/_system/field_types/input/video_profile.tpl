{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}

	{foreach from=$errors key=key item=error}
		<p class="error">
			{if $key == 'NOT_SUPPORTED_VIDEO_FORMAT' || $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
				[[Not supported video format]]
			{elseif $key == 'NOT_CONVERT_VIDEO'}
				[[Could not convert video file]]
			{elseif $key === 'UPLOAD_ERR_INI_SIZE'}
				[[File size exceeds system limit]]
			{else}
				[[{$key}]]
			{/if}
		</p>
	{/foreach}

	{if $value.file_url}
	<div id="user_video_{$id}">
		<div>
		{$value.file_name|escape:'html'} ({$filesize|string_format:"%.2f"} {$size_token}) |
			<a class="delete_profile_video"
			   form_token="{$form_token}"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>

		</div>
		<br />
	</div>

	{/if}


	<input id="autoloadFileSelect_{$id}"
		   field_id="{$id}"
		   field_action="upload_profile_video"
		   field_target="video_field_content_{$id}"
		   type="file"
		   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
		   class="inputVideo autouploadField {if $complexField}complexField{/if}"
		   {if $value.file_url}style="display:none;"{/if} />

	<input type="hidden" name="user_group_id" value="{if isset($GLOBALS.current_user.group.id)}{$GLOBALS.current_user.group.id}{else}{$user_group_info.id}{/if}" />

{else}

	<div id="video_field_content_{$id}">

	{if $value.file_url}
	<div id="user_video_{$id}">
		<script type="text/javascript" src="{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.12.min.js"></script>
		<a href="{$value.file_url|escape:'url'}" id="player_{$value.file_id}" class="player"></a>
		<script type="text/javascript">
			$f("player_{$value.file_id}", "{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.16.swf",  {
				clip: {
					url: "{$listing.video.file_url|escape:'url'}",
					autoPlay: false,
					autoBuffering: true,
					scaling: "fit"
				},
				plugins: {
					// default controls with the same background color as the page background
					controls:  {
						backgroundColor: '#1c1c1c',
						backgroundGradient: 'none',
						all:false,
						scrubber:true,
						fullscreen:true,
						play:true,
						volume:true,
						mute:true,
						height:30,
						progressColor: '#6d9e6b',
						bufferColor: '#333333',
						autoHide: false
					},
					// time display positioned into upper right corner
					time: {
						url: "{$GLOBALS.user_site_url}/files/video/flowplayer.controls-3.2.15.swf",
						top:0,
						backgroundGradient: 'none',
						backgroundColor: 'transparent',
						buttonColor: '#ffffff',
						all: false,
						time: true,
						height:20,
						right:0,
						width:100,
						autoHide: false
					}
				},
				// canvas coloring and custom gradient setting
				canvas: {
					backgroundColor:'#000000',
					backgroundGradient: [0.1, 0]
				}
			});
		</script>
		<div>
			<a class="delete_profile_video"
			   form_token="{$form_token}"
			   field_id="{$id}"
			   file_id="{$value.file_id}"
			   href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
		</div>
		<br />
	</div>
	{/if}


		<input id="autoloadFileSelect_{$id}"
			   field_id="{$id}"
			   field_action="upload_profile_video"
			   field_target="video_field_content_{$id}"
			   type="file"
			   name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			   class="inputVideo autouploadField {if $complexField}complexField{/if}"
			   {if $value.file_url}style="display:none;"{/if} />

		<input type="hidden" name="user_group_id" value="{if isset($GLOBALS.current_user.group.id)}{$GLOBALS.current_user.group.id}{else}{$user_group_info.id}{/if}" />

	</div>


	<script type="text/javascript">
		{literal}

		$(".delete_profile_video").live('click', function() {
			var fileId  = $(this).attr('file_id');
			var fieldId = $(this).attr('field_id');
			var formToken = $(this).attr('form_token');
			var url     = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
			var params = {
				'ajax_action': 'delete_profile_video',
				'field_id' : fieldId,
				'file_id' : fileId,
				'user_group_id' : '{/literal}{if isset($GLOBALS.current_user.group.id)}{$GLOBALS.current_user.group.id}{else}{$user_group_info.id}{/if}{literal}',
				'form_token' : formToken
			};

			var preloader = $(this).after( getPreloaderCodeForFieldId(fieldId) );
			$.get(url, params, function(data){
				if (data.result == 'success') {
					$("#autoloadFileSelect_" + fieldId).show();
					$("#user_video_" + fieldId).empty();
				}
				$(preloader).remove();
			}, 'json');
			// prevent link redirect
			return false;
		});

		{/literal}
	</script>
{/if}