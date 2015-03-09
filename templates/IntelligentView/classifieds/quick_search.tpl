<div class="plan stripped" style="margin-bottom:0 !important;">
		<div class="header">
			<h3>[[Job Search]]</h3>
		</div>
</div>
<div class="quickSearch" style="width:100%;">
	<form action="{$GLOBALS.site_url}/search-results-jobs/" id="quickSearchForm">
		<input type="hidden" name="action" value="search" />
		<input type="hidden" name="listing_type[equal]" value="Job" />
        <div class="row">
            <div class="col-sm-6">
                <div class="quickSearchInputField form-group"><label>[[Keywords]]</label>{search property=keywords}</div>
            </div>
            <div class="col-sm-6">
                <div class="quickSearchInputField form-group"><label>[[Category]]</label>{search property=JobCategory template="list.tpl"}</div>
            </div>
            <div class="col-sm-6">
                <div class="quickSearchInputField form-group"><label>[[City]]</label>{search property=City parent=Location}</div>
            </div>
            <div class="col-sm-6">
                <div class="quickSearchInputField form-group"><label>[[State]]</label>{assign var="name" value='State.Name'}{search property=$name parent=Location country=$GLOBALS.settings.default_country template="list.tpl"}</div>
            </div>
            <div class="col-sm-6">
                <div class="quickSearchInputName"><input type="submit" id="btn-search" class="button btn btn-default" value="[[Search]]"/></div>
            </div>
            <div class="col-sm-6 form-group">
                <div class="quickSearchInputName">
                    <a href="{$GLOBALS.site_url}/find-jobs/" class="btn btn-warning">[[Advanced search]]</a>
                    {if $acl->isAllowed('open_search_by_company_form')}
                        &nbsp; <a href="{$GLOBALS.site_url}/browse-by-company/" class="btn btn-info">[[Search by Company]]</a>
                    {/if}
                </div>
            </div>
        </div>
	</form>
</div>
<div class="alert alert-info">{module name="classifieds" function="count_listings"}</div>
<div class="clearfix"></div>