{if !$GLOBALS.current_user.group.id}
	{$params|parse_str:$params}
{/if}
{if $GLOBALS.is_ajax || $smarty.get.ajax_submit}
	{foreach from=$errors key=key item=error}
		{if $key == 'NOT_SUPPORTED_VIDEO_FORMAT' || $key == 'NOT_ACCEPTABLE_FILE_FORMAT'}
			<div class="error alert alert-danger">[[Not supported video format]]</div>
		{elseif $key === 'UPLOAD_ERR_INI_SIZE'}
			<div class="error alert alert-danger">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</div>
		{else}
			<div class="error alert alert-danger">{$key}</div>
		{/if}
	{/foreach}

	<input id="autoloadFileSelect_{$id}"
			field_id="{$id}"
			field_action="upload_profile_video"
			field_target="video_field_content_{$id}"
			type="file"
			name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
			class="inputVideo autouploadField {if $complexField}complexField{/if}"
			{if $value.file_url}style="display:none;"{/if} />

	{if $value.file_url}
		<div id="user_video_{$id}" style="float:left;">
			<script type="text/javascript" src="{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.12.min.js"></script>
			<a href="{$value.file_url|escape:'url'}" id="player_{$value.file_id}" class="player"></a>
			<div>
				<script language="JavaScript">
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
				<br />
				<a class="delete_profile_video"
					field_id="{$id}"
					file_id="{$value.file_id}"
					user_sid="{$user_info.sid}"
					href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}">[[Remove]]</a>
			</div>
			<br />
		</div>
		<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
	{else}
		<small>([[max.]] {$uploadMaxFilesize} M)</small>
	{/if}

	<input type="hidden" name="user_group_id" value="{if isset($GLOBALS.current_user.group.id)}{$GLOBALS.current_user.group.id}{else}{$params.user_group}{/if}" />
{else}
	<div id="video_field_content_{$id}">
		<input id="autoloadFileSelect_{$id}"
				field_id="{$id}"
				field_action="upload_profile_video"
				field_target="video_field_content_{$id}"
				type="file"
				name="{if $complexField}{$complexField}[{$id}][{$complexStep}]{else}{$id}{/if}"
				class="inputVideo autouploadField {if $complexField}complexField{/if}"
				{if $value.file_url}style="display:none;"{/if} />

		<input type="hidden" name="user_group_id" value="{if isset($GLOBALS.current_user.group.id)}{$GLOBALS.current_user.group.id}{else}{$params.user_group}{/if}" />

		{if $value.file_url}
			<div id="user_video_{$id}" style="float:left;">
				<script type="text/javascript" src="{$GLOBALS.user_site_url}/files/video/flowplayer-3.2.12.min.js"></script>
				<a href="{$value.file_url|escape:'url'}" id="player_{$value.file_id}" class="player"></a>
				<div>
					<script language="JavaScript">
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
					<br />
					<a class="delete_profile_video"
						field_id="{$id}"
						file_id="{$value.file_id}"
						user_sid="{$user_info.sid}"
						href="{$GLOBALS.site_url}/users/delete-uploaded-file/?field_id={$id}&form_token={$form_token}">[[Remove]]</a>
				</div>
				<br />
			</div>
			<span id="extra_field_info_{$id}" style="display:none;"><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
		{else}
			<small>([[max.]] {$uploadMaxFilesize} M)</small>
		{/if}
	</div>
{/if}