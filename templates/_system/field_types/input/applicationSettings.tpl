<script type="text/javascript">

	function displayInput(disableValue, disableId) {
		$("[id^='ApplicationSettings']").attr("disabled", "disabled");
		var appSettingsDiv = document.getElementById(disableId);
		$("[id!=" + disableId + "][id^='ApplicationSettings']").val('');
		appSettingsDiv.disabled = disableValue;
	}

	function validateForm(formName) {
		var form = document.getElementById(formName);
		var appSettingsRadio		= form.elements['{$id}[add_parameter]'];
		var appSettingsEmailValue	= form.elements["{$id}_1"].value;
		var appSettingsWebValue		= form.elements["{$id}_2"].value;
		for(var i = 0; i < appSettingsRadio.length; i++) {
			if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 1)
				var exp = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
				if( (appSettingsEmailValue != '') && !(appSettingsEmailValue.match(exp)) ) {
					error('[["Application Settings" wrong Email format]]');
					return false;
				}
			else if(appSettingsRadio[i].checked && appSettingsRadio[i].value == 2) {
				if(appSettingsWebValue == '') {
					error('[["Application Settings" url is empty]]');
					return false;
				} else if( !( appSettingsWebValue.match(/https?:\/\//)) ) {
					form.elements["{$id}_2"].value = 'http://' + appSettingsWebValue;
					return true;
				}
			}
		}
		return true;
	}

	function error(error_text) {
		$("#dialog").dialog( 'destroy' ).html(error_text);
		$("#dialog").dialog({
			bgiframe: true,
			modal: true,
			title: '[[Error]]',
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
	}
	function getUrl(name) {
		var url = document.getElementById(name);
		if (url.value != '') {
			if (!(url.value.match(/https?:\/\//)) ) {
				url.value = 'http://' + url.value;
			}
			window.open(url.value, "target");
		} else {
			alert('[["Application Settings" url is empty]]');
		}
	}
</script>

<div id="dialog"></div>
<table id="application-settings">
	<tr>
		<td valign="top">
			<input  id="via-email" class="inputRadio {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}{$id}[add_parameter][{$complexStep}]{else}{$id}[add_parameter]{/if}" value="1" {if $value.add_parameter == 1 || $value.add_parameter == ''}checked="checked"{/if} onclick="displayInput(false, '{$id}_1');" type="radio" />
		</td>
		<td>
			<label for="via-email">
				[[Send applications online via web site]]<br/>
				[[Send applications to this e-mail]]:<br/>
			</label>
			<input value="{if $value.add_parameter == 1}{$value.value|escape:'html'}{/if}" class="inputString"  name="{$id}[value]" {if $value.add_parameter == 2}disabled="disabled"{/if} id="{$id}_1" type="text" />
		</td>
	</tr>
	<tr>
		<td valign="top">
			<input  id="via-site" class="inputRadio{if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}[add_parameter][{$complexStep}]{else}{$id}[add_parameter]{/if}" value="2" {if $value.add_parameter == 2}checked="checked"{/if} onclick="displayInput(false, '{$id}_2');" type="radio" />
		</td>
		<td>
			<label for="via-site">
				[[Redirect to this URL]]:<br/>
			</label>
			<input value="{if $value.add_parameter == 2}{$value.value|escape:'html'}{/if}" class="inputString {if $complexField}complexField{/if}" name="{if $complexField}{$complexField}[{$id}][{$complexStep}][value]{else}{$id}[value]{/if}" id="{$id}_2" {if $value.add_parameter != 2}disabled="disabled"{/if} type="text" /> &nbsp; <input type="button" name="browse" value="[[Test URL]]" onclick="getUrl('{$id}_2')" /><br /><span class="small">[[Use the following format:]] <i><strong>http://</strong>yoursite.com</i></span>
		</td>
	</tr>
</table>