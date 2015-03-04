{capture name="urlParams"}{if is_array($paginationInfo.uniqueUrlParams)}{foreach from=$paginationInfo.uniqueUrlParams key=id item=param}&amp;{$id}={if $param.escape}{$param.value|escape:"{$param.escape}"}{else}{$param.value}{/if}{/foreach}{else}&amp;{$paginationInfo.uniqueUrlParams}{/if}
{/capture}
<div class="items-count"><strong>{$paginationInfo.itemsCount}</strong> [[{$paginationInfo.item|escape}]]</div>
{if $paginationInfo.actionsForSelect}
	{if count($paginationInfo.actionsForSelect) == 1}
		<div class="actionWithSelected">
			{foreach from=$paginationInfo.actionsForSelect key=value item=action}
				<input type="button" value="[[{$action}]]" class="{if $value == 'delete'}deletebutton{else}editbutton{/if}" onclick="{if $paginationInfo.popUp == true}isPopUp('{$paginationInfo.translatedText.chooseItem|escape}', '{$paginationInfo.translatedText.delete|escape}');{else}goSingleButton('{$value}', '{$paginationInfo.translatedText.chooseItem|escape}', '{$paginationInfo.translatedText.delete|escape}');{/if}" />
			{/foreach}
		</div>
	{else}
		<div class="actionSelected">
			<select id="selectedAction_{$layout}" name="selectedAction_{$layout}">
				<option value="">[[Select action]]</option>
				{foreach from=$paginationInfo.actionsForSelect key=value item=actionInfo}
					{if $actionInfo.isVisible == true}
						<option value="{$value}">[[{$actionInfo.name}]]</option>
					{/if}
				{/foreach}
			</select>
			<input type="button" value="[[Go]]" class="grayButton" onclick="{if $paginationInfo.popUp == true}isPopUp('{$layout}','{$paginationInfo.translatedText.chooseAction|escape:"javascript"}', '{$paginationInfo.translatedText.chooseItem|escape:"javascript"}', '{$paginationInfo.translatedText.delete|escape:"javascript"}');{else}go('{$layout}', '{$paginationInfo.translatedText.delete|escape}', '{$paginationInfo.translatedText.chooseAction|escape}', '{$paginationInfo.translatedText.chooseItem|escape}');{/if}"/>
		</div>
	{/if}
{/if}

{if count($paginationInfo.pages) != 1}
	<div class="pagination">
		{if $paginationInfo.currentPage == 1}
			<a class="none"><img src="{image}arrow-left-no-active.png" /></a>
		{else}
			&nbsp;<a class="arrow-left"  href="?{if $paginationInfo.restore == 1}restore=1{/if}&amp;page={$paginationInfo.currentPage-1}&amp;itemsPerPage={$paginationInfo.itemsPerPage}{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}"><img src="{image}arrow-left.png" /></a>
		{/if}
		{foreach from=$paginationInfo.pages item=page}
			{if $page == $paginationInfo.currentPage}
				<span id="currentButton" class="current" onclick="showInputPage('{$layout}');">{$page}&nbsp;<img src="{image}arrow-down.png" /></span>

				<div id="pageField_{$layout}" class="page-field" style="display: none;">
					<div class="title-current">[[go to page]]:</div>
					<div class="content-current">
						<div class="page-input">
							<input id="pageInput_{$layout}" type="text" autocomplete="off" onkeypress="$(this).keyup(function(event) { checkEnteredPage(event.keyCode, '{$layout}', '{$paginationInfo.totalPages}', '?{if $paginationInfo.restore == 1}restore=1{/if}{if $paginationInfo.sortingField ne null}&amp;sortingField={$paginationInfo.sortingField}{/if}{if $paginationInfo.sortingOrder ne null}&amp;sortingOrder={$paginationInfo.sortingOrder}{/if}&amp;itemsPerPage={$paginationInfo.itemsPerPage}{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}') });" onblur="hideInputPage('{$layout}');" />
						</div>
						<div class="page-enter-button">
							<input id="enterButton_{$layout}" type="button" disabled="disabled" onclick="goToPage('{$layout}', '?{if $paginationInfo.restore == 1}restore=1{/if}{if $paginationInfo.sortingField ne null}&amp;sortingField={$paginationInfo.sortingField}{/if}{if $paginationInfo.sortingOrder ne null}&amp;sortingOrder={$paginationInfo.sortingOrder}{/if}&amp;itemsPerPage={$paginationInfo.itemsPerPage}{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}')" />
						</div>
					</div>
				</div>
			{else}
				{if $page == $paginationInfo.totalPages && $paginationInfo.currentPage < $paginationInfo.totalPages-3} ... {/if}
				<a href="?page={$page}{if $paginationInfo.restore == 1}&amp;restore=1{/if}{if $paginationInfo.sortingField ne null}&amp;sortingField={$paginationInfo.sortingField}{/if}{if $paginationInfo.sortingOrder ne null}&amp;sortingOrder={$paginationInfo.sortingOrder}{/if}&amp;itemsPerPage={$paginationInfo.itemsPerPage}{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}">{$page}</a>
				{if $page == 1 && $paginationInfo.currentPage > 4} ... {/if}
			{/if}
		{/foreach}


		{if $paginationInfo.currentPage == $paginationInfo.totalPages}
			<a class="none"><img src="{image}arrow-right-no-active.png" /></a>&nbsp;
		{else}
			<a class="arrow-right" href="?{if $paginationInfo.restore == 1}restore=1{/if}&amp;page={$paginationInfo.currentPage + 1}&amp;itemsPerPage={$paginationInfo.itemsPerPage}{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}" ><img src="{image}arrow-right.png" /></a>&nbsp;
		{/if}
	</div>
{/if}

<div class="numberPerPage">
	[[per page]]:
	<select id="itemsPerPage" name="itemsPerPage" onchange="window.location = '?{if $paginationInfo.restore == 1}restore=1{/if}{if $paginationInfo.sortingField ne null}&amp;sortingField={$paginationInfo.sortingField}{/if}{if $paginationInfo.sortingOrder ne null}&amp;sortingOrder={$paginationInfo.sortingOrder}{/if}&amp;page=1{if $paginationInfo.uniqueUrlParams}{$smarty.capture.urlParams}{/if}&amp;itemsPerPage=' + this.value;" class="perPage">
		{foreach from=$paginationInfo.numberOfElementsPageSelect item=numberOfElement}
			<option value="{$numberOfElement}" {if $paginationInfo.itemsPerPage == $numberOfElement}selected{/if}>{$numberOfElement}</option>
		{/foreach}
	</select>
</div>

{if $layout == 'header'}
	<div id="actionWarning" style="display: none"></div>
{/if}
