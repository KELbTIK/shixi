{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-banner-groups/">[[Banners]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-banner-group/?groupSID={$banner.groupSID}">'{$banner.groupID}' [[Group]]</a> &#187; [[Edit Banner]]{/breadcrumbs}
<h1><img src="{image}/icons/slide32.png" border="0" alt="" class="titleicon"/>[[Edit Banner]]</h1>

{foreach from=$errors key=key item=error}
	{if $key === 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}

<fieldset>
	<legend>[[Edit Banner]]</legend>
	<form method="post" enctype="multipart/form-data">
		<div>
			<table>
				<tr>
					<td width="150px">[[Upload Banner File]]</td>
					<td><input type="radio" name="bannerType" value="file" {if $banner.bannerType == 'file' || $banner.bannerType == ''}checked="checked"{/if} onclick="chooseBannerType('file');" /></td>
				</tr>
				<tr>
					<td>[[Insert Banner Code]]</td>
					<td><input type="radio" name="bannerType" value="code"  {if $banner.bannerType == 'code'}checked="checked"{/if} onclick="chooseBannerType('code');"/></td>
				</tr>
			</table>
		</div>
		<table>
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" id="submit" name="submit" value="save_banner"/>
			{foreach from=$banner_fields item=form_field}
			<tr id="{$form_field.id}" {if (($banner.bannerType == 'file' || $banner.bannerType == '') && $form_field.id == 'code') || ($banner.bannerType == 'code' && $form_field.id == 'image')}style="display:none;"{/if}>
				<td valign="top" width="200px">{$form_field.caption}</td>
				<td valign="top" colspan="2" width="1px">{if $form_field.id == 'link' OR $form_field.id == 'title' OR $form_field.id == 'image' OR $form_field.id == 'code'}<span class="required">*</span>{/if}</td>
				{if $form_field.id == "active"}
					<td>
						<input type="hidden" name="{$form_field.id}" value="0" />
						<input type="checkbox" name="{$form_field.id}" value="1" {if $banner.active == '1'}checked{/if} />
					</td>
				{elseif $form_field.id == 'groupSID'}
					<td>
						<select name="groupSID">
							{foreach from=$form_field.values item=elem}
							<option value="{$elem.sid}"{if $elem.sid == $banner.groupSID} selected{/if}>{$elem.id}</option>
							{/foreach}
						</select>
					</td>
				{elseif $form_field.id == 'openBannerIn'}
					<td>
						<select name="openBannerIn">
							{foreach from=$form_field.values item=elem}
							<option value="{$elem.id}"{if $elem.id == $banner.openBannerIn} selected{/if}>[[{$elem.caption}]]</option>
							{/foreach}
						</select>
					</td>
				{elseif $form_field.id == 'code'}
					<td><textarea class="banner-textarea" id="{$form_field.id}_field" name="{$form_field.id}">{$banner[$form_field.id]}</textarea></td>
				{elseif $form_field.type eq 'file'}
					<td>
						<input type="file" name="{$form_field.id}" id="{$form_field.id}_field" {if $banner.image_path != ''}style="display:none;"{/if} />
						<span {if $banner.image_path != ''}style="display:none;"{/if}><small>([[max.]] {$uploadMaxFilesize} M)</small></span>
						{if $banner.image_path != ''}
							<div class="left">
								<img src="{$bannersPath}{$banner.image_path}" width="{$banner.width}" height="{$banner.height}">
								| <a class="removeFile" href="?bannerId={$banner.id}">[[Remove]]</a>
							</div>
						{/if}
						<div id="showProcess" style="display:none;">
							<img src="{$GLOBALS.user_site_url}/templates/_system/main/images/ajax_preloader_circular_16.gif" alt="[[Please wait ...]]" /> [[Please wait ...]]
						</div>
						<input type="hidden" name="imagePath" id="imagePath" value="{$banner.image_path}">
					</td>
				{else}
					<td>
						<input id="{$form_field.id}_field" type="{$form_field.type}" name="{$form_field.id}" value="{$banner[$form_field.id]}" />
					</td>
				{/if}
			</tr>
			{/foreach}
			<tr id="flash_param_field" style="display: none;">
				<td colspan="4" style="color: #00f; width: 300px;">
				[[To make the flash banner redirect users properly, use GetURL function as a link address in banner. To do this use the following code without modifications]]:
					{literal}
						<pre>
on (release) 
{ 
  getURL(banner_link, "_blank");
}
						</pre>
					{/literal}
				</td>
			</tr>
			<tr>
				<td colspan="4">
					{if $banner.type == "application/x-shockwave-flash"}
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0" ID="banner"" WIDTH="{$banner.width}" HEIGHT="{$banner.height}">
						<param name="movie" value="{$GLOBALS.site_url}{$banner.image_path}">
						<param name="quality" value="high">
						<param name="loop" value="false">
						<embed src="{$bannersPath}{$banner.image_path}" loop="false" quality="high" WIDTH="{$banner.width}" HEIGHT="{$banner.height}" TYPE="application/x-shockwave-flash"  PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
						</embed>
						</object>
					{elseif $banner.bannerType == 'code'}
						{$banner.code}
					{/if}
				</td>
			<tr>
				<td colspan="4" align="right">
					<input type="hidden" name="bannerId" value="{$banner.id}">
					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
						<input type="submit" value="[[Save]]" class="grayButton"/>
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>


<script type="text/javascript">
	{if $banner.type == "application/x-shockwave-flash"}
		$("#flash_param_field").show(); 
	{else}
		$("#flash_param_field").hide();
	{/if}

	function chooseBannerType(type)
	{
		var ids = {
			file: ['code'],
			code: ['image', 'image_comment', 'link', 'width', 'height', 'openBannerIn']
		};

		if (type == 'file') {
			$.each(ids.file, function() {
				$("#" + this).hide();
			});
			$.each(ids.code, function() {
				$("#" + this).show();
			})
		} else {
			$.each(ids.file, function() {
				$("#" + this).show();
			});
			$.each(ids.code, function() {
				$("#" + this).hide();
			})
		}
	}
	chooseBannerType($("[name='bannerType']:checked").val());
	
	$("input[type=file]").change(function(){
		t = $("input[type=file]").val();
		ind = t.lastIndexOf(".");
		ext = t.substring(ind+1);
		if (ext == "swf" || ext == "fla" ) {
			$("#flash_param_field").show();
		} else {
			$("#flash_param_field").hide();
		}
	});

	$("a.removeFile").click(function(event){
		event.preventDefault();
		var element = this;
		$.ajax({
			url: $(element).attr('href'),
			beforeSend: function(){
				$(element).parent('div').hide();
				$("#showProcess").show();
			},
			success: function(data){
				data = $.parseJSON(data);
				if (data.success) {
					$(element).parent('div').hide();
					$(element).parent('div').prevAll().show();
					$("#imagePath").val("");
				} else {
					alert(data.error);
					$(element).parent('div').show();
				}
				$("#showProcess").hide();
			}
		});
	});

	$('#apply').click(
		function(){
			$('#submit').attr('value', 'apply_banner');
		}
	);
</script>