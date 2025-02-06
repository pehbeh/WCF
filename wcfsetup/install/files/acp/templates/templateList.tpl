{include file='header' pageTitle="wcf.acp.template.list"}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.template.list{/lang} <span class="badge badgeInverse">{#$gridView->countRows()}</span></h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TemplateAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.template.add{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
