
{foreach from=$view->getRows() item='row'}
	<tr class="gridView__row" data-object-id="{$view->getObjectID($row)}">
		{foreach from=$view->getColumns() item='column'}
			<td class="gridView__column {$column->getClasses()}">
				{unsafe:$view->renderColumn($column, $row)}
			</td>
		{/foreach}
		{if $view->hasActions()}
			<td class="gridView__column gridView__actionColumn">
				<div class="dropdown">
					<button type="button" class="gridViewActions button small dropdownToggle" aria-label="{lang}wcf.global.button.more{/lang}">{icon name='ellipsis-vertical'}</button>

					<ul class="dropdownMenu">
						{foreach from=$view->getActions() item='action'}
							<li>
								{unsafe:$view->renderAction($action, $row)}
							</li>
						{/foreach}
					</ul>
				</div>
			</td>
		{/if}
	</tr>
{/foreach}
