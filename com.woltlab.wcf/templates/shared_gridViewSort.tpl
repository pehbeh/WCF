<div class="gridView">
	<div class="gridView__tableContainer">
		<table class="gridView__table" id="{$view->getID()}_sortTable">
			<thead>
				<tr class="gridView__headerRow">
					{foreach from=$view->getVisibleColumns() item='column'}
						<th class="gridView__headerColumn {$column->getClasses()}" data-id="{$column->getID()}">
							{unsafe:$column->getLabel()}
						</th>
					{/foreach}
				</tr>
			</thead>
			<tbody class="gridView__sortBody">
				{unsafe:$view->renderRows(true)}
			</tbody>
		</table>
	</div>
</div>
