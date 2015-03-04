<div class="clr"></div>
<div class="quickSearchTop">[[Job Search]]</div>
<div class="quickSearch">
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
		<div style="text-align:center; margin-top:20px"></div>
		<fieldset>
			<div class="quickSearchInputField">[[Keywords]]<br/>{search property=keywords}</div>
			<div class="quickSearchInputField">[[Category]]<br/>{search property=JobCategory template="list.tpl"}</div>
		</fieldset>
		<fieldset>
			<div class="quickSearchInputField">[[City]]<br/>{search property=City parent=Location}</div>
			<div class="quickSearchInputField">[[State]]<br/>{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template="list.tpl"}</div>
		</fieldset>
		<fieldset>
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
<div class="quickSearchBottom"> </div>
<div class="InputStat">{module name="classifieds" function="count_listings"}</div>
<div class="clr"><br/></div>