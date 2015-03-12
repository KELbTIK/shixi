{if $display_layout eq "2cols-wide"}

	<div  class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div  class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>
	<div class="clearfix"></div>
	{$smarty.capture.middleFHContent}
	<div class="clearfix"></div>
    <div class="col-sm-12">
        {module name="builder" function="get_fields" fieldsHolderID="col-wide"}
    </div>

{elseif $display_layout eq "1col-2rows"}
	<div class="col-sm-6 narrow-1col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow"}
	</div>
	{$smarty.capture.middleFHContent}
	<div class="clearfix"></div>
    <div class="col-sm-12">
	    {module name="builder" function="get_fields" fieldsHolderID="col-wide"}
    </div>

{elseif $display_layout eq "wide-2cols"}
    <div class="col-sm-12">
	{module name="builder" function="get_fields" fieldsHolderID="col-wide"}
    </div>
	<div class="clearfix"></div>
	{$smarty.capture.middleFHContent}
	<div class="clearfix"></div>
	<div class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>

{elseif $display_layout eq "2cols"}
	<div class="clearfix"></div>
	{$smarty.capture.middleFHContent}
	<div class="clearfix"></div>
	<div class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div class="col-sm-6">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>
	<div class="clearfix"></div>

{else}
	<div class="clearfix"></div>
	{$smarty.capture.middleFHContent}
	<div class="clearfix"></div>
    <div class="col-sm-12">
	    {module name="builder" function="get_fields" fieldsHolderID="col-wide"}
    </div>

{/if}