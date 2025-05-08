{capture assign='pageTitle'}{lang}wcf.moderation.deletedContent.{@$objectType}{/lang}{/capture}

{capture assign='sidebarRight'}
	{unsafe:$deletedItemsBox->render()}
{/capture}

{capture assign='contentTitle'}{lang}wcf.moderation.deletedContent.{@$objectType}{/lang}{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='DeletedContentList' link="objectType=$objectType&pageNo=%d"}
{/capture}

{include file='header'}

{if $items}
	{include file=$resultListTemplateName application=$resultListApplication}
{else}
	<woltlab-core-notice type="info">{lang}wcf.global.noItems{/lang}</woltlab-core-notice>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
