<div class="gridView">
	{if $view->isFilterable()}
		<div class="gridView__filterBar">
			<div class="gridView__filters" id="{$view->getID()}_filters">
				{foreach from=$view->getActiveFilters() item='value' key='key'}
					<button type="button" class="button small" data-filter="{$key}" data-filter-value="{$value}">
						{icon name='circle-xmark'}
						{$view->getFilterLabel($key)}
					</button>
				{/foreach}
			</div>
			<div class="gridView__filterButton">
				<button type="button" class="button small" id="{$view->getID()}_filterButton" data-endpoint="{$view->getFilterActionEndpoint()}">
					{icon name='filter'}
					{lang}wcf.global.filter{/lang}
				</button>
			</div>
		</div>
	{/if}
	
	<div class="gridView__tableContainer">
		<table class="gridView__table" id="{$view->getID()}_table"{if !$view->countRows()} hidden{/if}>
			<thead>
				<tr class="gridView__headerRow">
					{foreach from=$view->getVisibleColumns() item='column'}
						<th
							class="gridView__headerColumn {$column->getClasses()}"
							data-id="{$column->getID()}"
							data-sortable="{$column->isSortable()}"
						>
							{if $column->isSortable()}
								<button type="button" class="gridView__headerColumn__button">
									{unsafe:$column->getLabel()}
								</button>
							{else}
								{unsafe:$column->getLabel()}
							{/if}
						</th>
					{/foreach}
					{if $view->hasInteractions()}
						<th class="gridView__headerColumn gridView__actionColumn"></th>
					{/if}
				</td>
			</thead>
			<tbody>
				{unsafe:$view->renderRows()}
			</tbody>
		</table>
	</div>

	<div class="gridView__pagination">
		<woltlab-core-pagination id="{$view->getID()}_pagination" page="{$view->getPageNo()}" count="{$view->countPages()}"></woltlab-core-pagination>
	</div>

	<woltlab-core-notice type="info" id="{$view->getID()}_noItemsNotice"{if $view->countRows()} hidden{/if}>{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
</div>

<script data-relocate="true">
	require(['WoltLabSuite/Core/Component/GridView'], ({ GridView }) => {
		new GridView(
			'{unsafe:$view->getID()|encodeJs}',
			'{unsafe:$view->getClassName()|encodeJS}',
			{$view->getPageNo()},
			'{unsafe:$view->getBaseUrl()|encodeJS}',
			'{unsafe:$view->getSortField()|encodeJS}',
			'{unsafe:$view->getSortOrder()|encodeJS}',
			new Map([
				{foreach from=$view->getParameters() key='name' item='value'}
					['{unsafe:$name|encodeJs}', '{unsafe:$value|encodeJs}'],
				{/foreach}
			])
		);
	});
</script>
{if $view->hasInteractions()}
	{unsafe:$view->renderInteractionInitialization()}
{/if}
