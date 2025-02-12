{capture assign='pageTitle'}{lang}wcf.moderation.items{/lang}{if $gridView->getPageNo() > 1} - {lang pageNo=$gridView->getPageNo()}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentTitle'}{lang}wcf.moderation.items{/lang} <span class="badge">{#$gridView->countRows()}</span>{/capture}

{capture assign='sidebarRight'}
	{event name='sidebarBoxes'}
{/capture}

{capture assign='contentInteractionButtons'}
	<button type="button" class="markAllAsReadButton contentInteractionButton button small jsOnly">{icon name='check'} <span>{lang}wcf.global.button.markAllAsRead{/lang}</span></button>
	<a href="{link controller='DeletedContentList'}{/link}" class="contentInteractionButton button small">{icon name='trash-can'} <span>{lang}wcf.moderation.showDeletedContent{/lang}</span></a>
{/capture}

{include file='header'}

<div class="section">
	{unsafe:$gridView->render()}
</div>

<!-- TODO -->
<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/Moderation/MarkAsRead'], (MarkAsRead) => {
		MarkAsRead.setup();
	});
	require(['WoltLabSuite/Core/Ui/Moderation/MarkAllAsRead'], (MarkAllAsRead) => {
		MarkAllAsRead.setup();
	});
</script>

{include file='footer'}
