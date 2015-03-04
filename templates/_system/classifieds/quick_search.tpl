<div class="searchForm">
	<div style="float: left; width: 108px; height:135px; background-image:url('{image}zoomer.png');"></div>
	<div style="text-align: center;">
		<span style="font-size:28pt;color:#6e6e6e;font-weight:bold; text-align:center; font-family: Arial;">[[Job Search]]</span>
	</div>
	<form action="{$GLOBALS.site_url}/search-results-jobs/">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<table cellpadding="0" cellspacing="0" width="80%">
			<tr style="height:40px">
				<td>&nbsp; [[Keywords]]</td>
				<td>&nbsp; {search property=keywords}</td>
				<td>&nbsp; [[Category]]</td>
				<td>&nbsp; {search property=JobCategory  template='list.tpl'}</td>
				<td>&nbsp; 
					<input type="submit" class="button" style="width:100px;" value="[[Find]]" />
				</td>
			</tr>
			<tr>
				<td>&nbsp; [[Location]]</td>
				<td>&nbsp; {search property=Location searchWithin=true fields=$locationFields template="location.like.tpl"}</td>
				<td>&nbsp; 
					<a style="font-weight:normal;" href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
				</td>
			</tr>
			<tr>
				<td colspan='4'>&nbsp;</td>
				<td>&nbsp; {if $acl->isAllowed('open_search_by_company_form')}
					<a style="font-weight:normal;"  href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
					{/if}
				</td>
			</tr>
		</table>
	</form>
</div>