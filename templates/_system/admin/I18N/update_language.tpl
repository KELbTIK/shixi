{breadcrumbs}<a href="{$GLOBALS.site_url}/manage-languages/">[[Manage Languages]]</a> &#187; [[Edit language]]{/breadcrumbs}
<h1><img src="{image}/icons/exchange32.png" border="0" alt="" class="titleicon"/>[[Edit language]] {$lang.caption}</h1>
<p><a href="{$GLOBALS.site_url}/manage-phrases/?language={$lang.id}&action=search_phrases" class="grayButton">[[Translate Phrases]]</a> <a href="{$GLOBALS.site_url}/import-language/" class="grayButton">[[Import translations]]</a> <a href="{$GLOBALS.site_url}/export-language/" class="grayButton">[[Export translations]]</a></p>

{if $errors}
	{include file="errors.tpl" errors=$errors}
{/if}

<p>[[Fields marked with an asterisk (<span class="required">*</span>) are mandatory]]</p>

<fieldset>
	<legend>[[Edit Language]] <b>{$lang.caption}</b></legend>
	<form method="post" enctype="multipart/form-data">
		<table>
			<tr>
				<td colspan="2">[[Language Caption]]</td>
				<td><input type="text" name="caption" value="{$lang.caption}" /></td>
			</tr>
			<tr>
				<td colspan="2">[[Active for Frontend]]</td>
				<td>
					<input type="hidden" name="activeFrontend" value="0" />
					<input type="checkbox" name="activeFrontend"{if $lang.activeFrontend} checked="checked" {/if} value="1"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Active for Backend]]</td>
				<td>
					<input type="hidden" name="activeBackend" value="0" />
					<input type="checkbox" name="activeBackend"{if $lang.activeBackend} checked="checked" {/if} value="1"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Date Format]]</td>
				<td>
					<input type="text" name="date_format" value="{$lang.date_format}" /><br />
					<small>
						[[default format symbols that are supported]]: <br />%Y, %m, %d (%Y - [[year]], %m - [[month]], %d - [[day]])<br /><br />
						[[example of March,9 2008]]:<br />
						&nbsp;&nbsp;%Y-%m-%d => 2008-03-09<br />
						&nbsp;&nbsp;%m/%d/%Y => 03/09/2008<br />
						&nbsp;&nbsp;%d.%m.%Y => 09.03.2008<br /><br />
					</small>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Decimal Separator]]</td>
				<td>
					<select name="decimal_separator">
						<option value="."{if $lang.decimal_separator == '.'} selected {/if}>[[dot]]</option>
						<option value=","{if $lang.decimal_separator == ','} selected {/if}>[[comma]]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Thousands Separator]]</td>
				<td>
					<select name="thousands_separator">
						<option value=".">[[dot]]</option>
						<option value=","{if $lang.thousands_separator == ','} selected {/if}>[[comma]]</option>
						<option value=" "{if $lang.thousands_separator == ' '} selected {/if}>[[space]]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Decimals]]</td>
				<td>
					<select name="decimals">
						<option value="0">0</option>
						<option value="1"{if $lang.decimals == '1'} selected {/if}>1</option>
						<option value="2"{if $lang.decimals == '2'} selected {/if}>2</option>
						<option value="3"{if $lang.decimals == '3'} selected {/if}>3</option>
						<option value="4"{if $lang.decimals == '4'} selected {/if}>4</option>
						<option value="5"{if $lang.decimals == '5'} selected {/if}>5</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Right to Left Layout]]</td>
				<td>
					<input type="hidden" name="rightToLeft" value="0" />
					<input type="checkbox" name="rightToLeft"{if $lang.rightToLeft} checked="checked" {/if} value="1" />
				</td>
			</tr>
			<tr>
				<td colspan="2">[[Display Currency Sign]]</td>
				<td>
					<select name="currencySignLocation">
						<option value="0"{if $lang.currencySignLocation == '0'} selected {/if}>[[before amount]]</option>
						<option value="1"{if $lang.currencySignLocation == '1'} selected {/if}>[[after amount]]</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="hidden" name="languageId" value="{$lang.id}" />
					<input type="hidden" name="action" value="update_language" />
					<input type="hidden" id="submit" name="submit" value="save" />

					<div class="floatRight">
						<input type="submit" id="apply" value="[[Apply]]" class="grayButton"/>
						<input type="submit" value="[[Save]]" class="grayButton" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<script>
	$('#apply').click(
		function() {
			$('#submit').attr('value', 'apply');
		}
	);
</script>