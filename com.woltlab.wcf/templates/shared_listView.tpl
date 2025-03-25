<div class="listView">
	{if $view->isSortable() || $view->isFilterable()}
		<div class="listView__header">
			{if $view->isFilterable()}
				<div class="listView__filters" id="{$view->getID()}_filters">
					{foreach from=$view->getActiveFilters() item='value' key='key'}
						<button type="button" class="button small" data-filter="{$key}" data-filter-value="{$value}">
							{icon name='circle-xmark'}
							{$view->getFilterLabel($key)}
						</button>
					{/foreach}
				</div>
			{/if}
			<div class="listView__header__buttons">
				{if $view->isSortable()}
					<div class="listView__header__button dropdown">
						<button type="button" class="button small dropdownToggle">
							{icon name='arrow-down-short-wide'}
							<span>{lang}wcf.global.sorting{/lang}</span>
						</button>
						<ul class="dropdownMenu" id="{$view->getID()}_sorting">
							{foreach from=$view->getAvailableSortFields() item='sortField'}
								<li>
									<button type="button" class="listView__sorting__button" data-sort-id="{$sortField->id}">
										{unsafe:$sortField}
									</button>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
				{if $view->isFilterable()}
					<div class="listView__header__button">
						<button type="button" class="button small" id="{$view->getID()}_filterButton" data-endpoint="{$view->getFilterActionEndpoint()}">
							{icon name='filter'}
							{lang}wcf.global.filter{/lang}
						</button>
					</div>
				{/if}
			</div>
		</div>
	{/if}
	
	<div class="listView__itemContainer">
		<div class="listView__items {$view->getCssClassName()}" id="{$view->getID()}_items"{if !$view->countItems()} hidden{/if}>
			{unsafe:$view->renderItems()}
		</div>
	</div>

	<div class="listView__footer">
		{*if $view->hasBulkInteractions()}
			
		{/if*}

		<div class="listView__pagination">
		<woltlab-core-pagination id="{$view->getID()}_pagination" page="{$view->getPageNo()}" count="{$view->countPages()}"></woltlab-core-pagination>
	</div>
	</div>

	{*if $view->hasBulkInteractions()}
		<div id="{$view->getID()}_selectionBar" class="listView__selectionBar dropdown" hidden>
			<button type="button" id="{$view->getID()}_bulkInteractionButton" class="button listView__bulkInteractionButton dropdownToggle"></button>
			<ul class="dropdownMenu">
				<li class="disabled"><span>{lang}wcf.global.loading{/lang}</span></li>
				<li class="dropdownDivider"></li>
				<li>
					<button type="button" id="{$view->getID()}_resetSelectionButton">{lang}wcf.clipboard.item.unmarkAll{/lang}</button>
				</li>
			</ul>
		</div>
	{/if*}

	<woltlab-core-notice type="info" id="{$view->getID()}_noItemsNotice"{if $view->countItems()} hidden{/if}>{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
</div>

<script data-relocate="true">
	require(['WoltLabSuite/Core/Component/ListView'], ({ ListView }) => {
		WoltLabLanguage.registerPhrase("wcf.clipboard.button.numberOfSelectedItems", '{jslang __literal=true}wcf.clipboard.button.numberOfSelectedItems{/jslang}');
		
		new ListView(
			'{unsafe:$view->getID()|encodeJs}',
			'{unsafe:$view->getClassName()|encodeJS}',
			{$view->getPageNo()},
			'{unsafe:$view->getBaseUrl()|encodeJS}',
			'{unsafe:$view->getSortField()|encodeJS}',
			'{unsafe:$view->getSortOrder()|encodeJS}'
		);
	});
</script>
{if $view->hasInteractions()}
	{unsafe:$view->renderInteractionInitialization()}
{/if}
{*if $view->hasBulkInteractions()}
	{unsafe:$view->renderBulkInteractionInitialization()}
{/if*}
