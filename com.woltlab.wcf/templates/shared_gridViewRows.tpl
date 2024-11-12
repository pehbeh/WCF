
{foreach from=$view->getRows() item='row'}
	<tr class="gridView__row" data-object-id="{$view->getObjectID($row)}">
		{foreach from=$view->getVisibleColumns() item='column'}
			<td class="gridView__column {$column->getClasses()}">
				{unsafe:$view->renderColumn($column, $row)}
			</td>
		{/foreach}
		{if $view->hasActions()}
			<td class="gridView__column gridView__actionColumn">
				<div class="gridView__actionColumn__buttons">
					{foreach from=$view->getQuickActions() item='action'}
						{unsafe:$view->renderAction($action, $row)}
					{/foreach}

					{if $view->hasDropdownActions()}
						{hascontent}
							<div class="dropdown">
								<button type="button" class="gridViewActions button small dropdownToggle" aria-label="{lang}wcf.global.button.more{/lang}">
									{icon name='ellipsis-vertical'}
								</button>

								<ul class="dropdownMenu">
									{content}
										{foreach from=$view->getDropdownActions() item='action'}
											{if $action->isAvailable($row)}
												<li>
													{unsafe:$view->renderAction($action, $row)}
												</li>
											{/if}
										{/foreach}
									{/content}
								</ul>
							</div>
						{hascontentelse}
							<button type="button" disabled class="button small" aria-label="{lang}wcf.global.button.more{/lang}">
								{icon name='ellipsis-vertical'}
							</button>
						{/hascontent}
					{/if}
				</div>
			</td>
		{/if}
	</tr>
{/foreach}
