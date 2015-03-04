{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-banner-groups/">[[Banners]]</a> &#187; <a href="{$GLOBALS.site_url}/edit-banner-group/?groupSID={$bannerGroup.sid}">'{$bannerGroup.id}' [[Group]]</a> &#187; [[Add a New Banner]]{/breadcrumbs}
<h1>[[Add a New Banner]]</h1>
{foreach from=$errors key=key item=error}
	{if $key === 'UPLOAD_ERR_INI_SIZE'}
		<p class="error">[[File size exceeds system limit. Please check the file size limits on your hosting or upload another file.]]</p>
	{else}
		<p class="error">[[{$error}]]</p>
	{/if}
{/foreach}

<fieldset>
	<legend>[[Add a New Banner]]</legend>
	<form method="post" enctype="multipart/form-data">
		<div>
			<table>
				<tr>
					<td width="150px">[[Upload Banner File]]</td>
					<td><input type="radio" name="bannerType" value="file" {if $params.bannerType == 'file' || $params.bannerType == ''}checked="checked"{/if} onclick="chooseBannerType('file');" /></td>
				</tr>
				<tr>
					<td>[[Insert Banner Code]]</td>
					<td><input type="radio" name="bannerType" value="code"  {if $params.bannerType == 'code'}checked="checked"{/if} onclick="chooseBannerType('code');"/></td>
				</tr>
			</table>
		</div>
		<table>
			<input type="hidden" name="action" value="add" />
			{foreach from=$banner_fields item=form_field}
				<tr id="{$form_field.id}" {if (($params.bannerType == 'file' || $params.bannerType == '' || !$params.bannerType) && $form_field.id == 'code') || ($params.bannerType == 'code' && $form_field.id == 'image')}style="display:none;"{/if}>
					<td valign="top" width="200px">[[{$form_field.caption}]]</td>
					<td valign="top" colspan="2" width="1px">{if $form_field.id == 'link' || $form_field.id == 'title' || $form_field.id == 'image' || $form_field.id == 'code'}<span class="required">*</span>{/if}</td>
					<td>
						{if $form_field.type == 'boolean'}
							<input type="hidden" name="{$form_field.id}" value="0" />
							<input type="checkbox" name="{$form_field.id}" value="1" />
						{elseif $form_field.id == 'groupSID'}
							<select name="groupSID">
								{foreach from=$form_field.values item=elem}
								<option value="{$elem.sid}"{if $elem.sid == $bannerGroup.sid} selected="selected" {/if}>{$elem.id}</option>
								{/foreach}
							</select>
						{elseif $form_field.id == 'openBannerIn'}
							<select name="openBannerIn">
								{foreach from=$form_field.values item=elem}
								<option value="{$elem.id}"{if $elem.id == $params.openBannerIn} selected="selected"{/if}>[[{$elem.caption}]]</option>
								{/foreach}
							</select>
						{elseif $form_field.id == 'code'}
							<textarea class="banner-textarea" id="{$form_field.id}_field" name="{$form_field.id}">{$banner[$form_field.id]}</textarea>
						{else}
							<input type="{$form_field.type}" name="{$form_field.id}" value="{$form_field.value}" />
							{if $form_field.type eq 'file'}
								<small>([[max.]] {$uploadMaxFilesize} M)</small>
							{/if}
						{/if}
					</td>
				</tr>
				{if $form_field.comment}
					<tr id="{$form_field.id}_comment" {if (($params.bannerType == 'file' || $params.bannerType == '' || !$params.bannerType) && $form_field.id == 'code') || ($params.bannerType == 'code' && $form_field.id == 'image')}style="display:none;"{/if} >
						<td colspan="3"><small>[[{$form_field.comment}]]</small></td>
					</tr>
				{/if}
			{/foreach}
			<tr id="flash_param_field" style="display: none;">
				<td colspan="4" style="width: 300px;">
					<p class="note">
					<strong>[[To make the flash banner redirect users properly, for ActionScript 1-2 use getURL function as a link address in banner. To do this use the following code without modifications]]:</strong>
					{literal}
<br />on (release) {<br />
&nbsp;&nbsp;&nbsp;&nbsp;getURL(_root.sjb_banner_link, _root.sjb_banner_window);<br />
}<br /><br />
					{/literal}
					<strong>[[for ActionScript 3 use]]</strong><br />
					{literal}
this.addEventListener(MouseEvent.CLICK, sjb_banner_click);<br />
function sjb_banner_click(evt:MouseEvent):void {<br />
&nbsp;&nbsp;&nbsp;&nbsp;navigateToURL(new URLRequest(this.root.loaderInfo.parameters.sjb_banner_link),<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;this.root.loaderInfo.parameters.sjb_banner_window);<br />
						}<br />
					{/literal}
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div class="floatRight"><input type="submit" value="[[Add]]" class="grayButton" /></div>
				</td>
			</tr>
		</form>
	</table>
</fieldset>

<script type="text/javascript">
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
	
	$("#flash_param_field").hide();
	$("input[type=file]").change(function(){
		t = $("input[type=file]").val();
		ind = t.lastIndexOf(".");
		ext = t.substring(ind+1);
		ext = ext.toLowerCase();
		if (ext == "swf" || ext == "fla" ) {
			$("#flash_param_field").show();
		} else {
			$("#flash_param_field").hide();
		}
	});
</script>