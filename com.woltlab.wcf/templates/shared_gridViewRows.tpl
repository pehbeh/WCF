
{foreach from=$view->getRows() item='row'}
	<tr class="gridView__row" data-object-id="{$view->getObjectID($row)}">
		{foreach from=$view->getVisibleColumns() item='column'}
			<td class="gridView__column {$column->getClasses()}">
				{unsafe:$view->renderColumn($column, $row)}
			</td>
		{/foreach}
		{if $view->hasInteractions()}
			<td class="gridView__column gridView__actionColumn">
				<div class="gridView__actionColumn__buttons">
					{unsafe:$view->renderQuickInteractions($row)}
					{unsafe:$view->renderInteractionContextMenuButton($row)}
				</div>
			</td>
		{/if}
	</tr>
{/foreach}
