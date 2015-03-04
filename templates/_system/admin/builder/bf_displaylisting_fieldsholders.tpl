{if $display_layout eq "2cols-wide"}

	<div  class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div  class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>
	<div class="clr"></div>
	{$smarty.capture.middleFHContent}
	<div class="clr"></div>
	{module name="builder" function="get_fields" fieldsHolderID="col-wide"}

{elseif $display_layout eq "1col-2rows"}
	<div class="narrow-col narrow-1col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow"}
	</div>
	{$smarty.capture.middleFHContent}
	<div class="clr"></div>
	{module name="builder" function="get_fields" fieldsHolderID="col-wide"}

{elseif $display_layout eq "wide-2cols"}

	{module name="builder" function="get_fields" fieldsHolderID="col-wide"}
	<div class="clr"></div>
	{$smarty.capture.middleFHContent}
	<div class="clr"></div>
	<div class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>

{elseif $display_layout eq "2cols"}
	<div class="clr"></div>
	{$smarty.capture.middleFHContent}
	<div class="clr"></div>
	<div class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-left"}
	</div>
	<div class="narrow-col">
		{module name="builder" function="get_fields" fieldsHolderID="col-narrow-right"}
	</div>
	<div class="clr"></div>

{else}
	<div class="clr"></div>
	{$smarty.capture.middleFHContent}
	<div class="clr"></div>
	{module name="builder" function="get_fields" fieldsHolderID="col-wide"}

{/if}