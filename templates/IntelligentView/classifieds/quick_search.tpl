<div class="plan stripped" style="margin-bottom:0 !important;">
		<div class="header">
			<h3>[[Job Search]]</h3>
		</div>
</div>
<div class="quickSearch" style="width:100%;">
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<fieldset style="background-color:transparent;">
			<div class="quickSearchInputField">[[Keywords]]<br/>{search property=keywords}</div>
			<div class="quickSearchInputField">[[Category]]<br/>{search property=JobCategory template="list.tpl"}</div>
		</fieldset>
		<fieldset style="background-color:transparent;">
			<div class="quickSearchInputField">[[City]]<br/>{search property=City parent=Location}</div>
			<div class="quickSearchInputField">[[State]]<br/>{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template="list.tpl"}</div>
		</fieldset>
		<fieldset style="background-color:transparent;">
			<div class="quickSearchInputName"><br/><input type="submit" id="btn-search" class="button" value="[[Search]]"/></div>
			<div class="quickSearchInputName">
				<br/><a href="{$GLOBALS.site_url}/find-jobs/">[[Advanced search]]</a>
				{if $acl->isAllowed('open_search_by_company_form')}
					<br/><a href="{$GLOBALS.site_url}/browse-by-company/">[[Search by Company]]</a>
				{/if}
			</div>
		</fieldset>
	</form>
</div>
<div class="InputStat">{module name="classifieds" function="count_listings"}</div>
<div class="clr"><br/></div>