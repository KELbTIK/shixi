<h1>[[Bulk job import from exl/csv file]]</h1>
<p class="smallh1">[[Please review the file examples below and ascertain that your file is up to sample]]</p>
<div class="clearfix"></div>
{if $error}
	<div class="error alert alert-danger">
		{if $error eq 'LISTINGS_NUMBER_LIMIT_EXCEEDED'}
			[[You've reached the limit of number of listings allowed by your product]]
			<a href="{$GLOBALS.site_url}/products/">[[Please choose new product]]</a>
		{elseif $error eq 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING'}
			[[You do not have permissions to post {$listing_type_id} listings. Please purchase a relevant product.]]
		{elseif $error eq 'DO_NOT_MATCH_IMPORT_THIS_TYPE_LISTING'}
			[[You do not have permissions to import listings.]]
		{elseif $error eq 'NOT_LOGGED_IN'}
			[[Please log in to place a new posting. If you do not have an account, please]] <a href="{$GLOBALS.site_url}/registration/">[[Register.]]</a>
			{module name="users" function="login"}
		{else}
			[[{$error}]]
		{/if}
	</div>
	<br/>
{else}
	{if $warning}
		<div class="error alert alert-danger">[[{$warning}]]</div>
	{/if}
	<form method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="contract_id" value="{$contract_id}" />
		<div class="table-responsive">
			<table class="formtable table table-condensed">
			<tr class="headrow">
				<td colspan="2">[[Data Import]]</td>
				<td align='right'>
					<a href="?action=example&type=exl">[[Job import EXL file example]]</a><br/>
					<a href="?action=example&type=csv">[[Job import CSV file example]]</a>
				</td>
			</tr>
			<tr class="oddrow">
				<td>[[Please choose Excel or csv file]]:</td>
				<td colspan="2"><input type="file" name="import_file" value="" class="text form-control"  /></td>
			</tr>
			<tr>
				<td>[[Encoding]]<br /><small>([[for CSV-file only]])</small></td>
				<td colspan="2">
					<select class="form-control"  name="encodingFromCharset" >
						<option value="UTF-8">[[Default]]</option>
						{foreach from=$charSets item=charSet}
							<option value="{$charSet}">{$charSet}</option>
						{/foreach}
					</select>
					<div class="commentSmall">[[Select appropriate encoding for your language  in case you have problems with import of certain symbols]]</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2" align="right"><input type="submit" name="action" value="Import" class="btn btn-default" /></td>
			</tr>
		</table>
		</div>
	</form>
{/if}