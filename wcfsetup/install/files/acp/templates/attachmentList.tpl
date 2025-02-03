{include file='header' pageTitle='wcf.acp.attachment.list'}

{include file='shared_imageViewer'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.attachment.list{/lang}</h1>
		<p class="contentHeaderDescription">{lang}wcf.acp.attachment.stats{/lang}</p>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}{event name='contentHeaderNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
