{capture assign='pageTitle'}{lang}wcf.tagging.combinedTaggedObjects.{@$objectType}{/lang} {lang}wcf.tagging.combinedTaggedObjects{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">{lang}wcf.tagging.combinedTaggedObjects.{@$objectType}{/lang} {lang}wcf.tagging.combinedTaggedObjects{/lang}</h1>
		</div>
	</header>
{/capture}

{capture assign='linkParameters'}{implode from=$combinedTags item=tag glue='&'}tagIDs[]={@$tag->tagID}{/implode}{/capture}

{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='CombinedTagged'}{@$linkParameters}&objectType={@$objectType}&pageNo={@$pageNo+1}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='CombinedTagged'}{@$linkParameters}&objectType={@$objectType}{if $pageNo > 2}&pageNo={@$pageNo-1}{/if}{/link}">
	{/if}
	<link rel="canonical" href="{link controller='CombinedTagged'}{@$linkParameters}&objectType={@$objectType}{if $pageNo > 1}&pageNo={@$pageNo}{/if}{/link}">
{/capture}

{capture assign='sidebarRight'}
	<section class="box" data-static-box-identifier="com.woltlab.wcf.TaggedMenu">
		<h2 class="boxTitle">{lang}wcf.tagging.objectTypes{/lang}</h2>
		
		<nav class="boxContent">
			<ul class="boxMenu">
				{foreach from=$objectTypeLinks item=objectTypeLink}
					<li{if $objectType == $objectTypeLink[objectType]} class="active"{/if}>
						<a class="boxMenuLink" href="{$objectTypeLink[link]}">
							<span class="boxMenuLinkTitle">{$objectTypeLink[title]}</span>
							<span class="badge">{$objectTypeLink[items]}</span>
						</a>
					</li>
				{/foreach}
			</ul>
		</nav>
	</section>
	
	<section class="box" data-static-box-identifier="com.woltlab.wcf.TaggedTagCloud">
		<h2 class="boxTitle">{lang}wcf.tagging.tags{/lang}</h2>
		
		<div class="boxContent">
			{include file='tagCloudBox' taggableObjectType=$objectType}
		</div>
	</section>
{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='CombinedTagged' link="$linkParameters&objectType=$objectType&pageNo=%d"}
{/capture}

{capture assign='contentInteractionButtons'}
	<a href="{link controller='TagSearch'}{/link}" class="contentInteractionButton button small">{icon name='magnifying-glass'} <span>{lang}wcf.search.type.tags{/lang}</span></a>
{/capture}

{include file='header'}

{if $items}
	{include file=$resultListTemplateName application=$resultListApplication}
{else}
	<woltlab-core-notice type="info">{lang}wcf.tagging.taggedObjects.noResults{/lang}</woltlab-core-notice>
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
