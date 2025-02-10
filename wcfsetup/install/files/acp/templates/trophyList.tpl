{include file='header' pageTitle='wcf.acp.menu.link.trophy.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.trophy.list{/lang} <span class="badge badgeInverse">{#$gridView->countRows()}</span></h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TrophyAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.menu.link.trophy.add{/lang}</span></a></li>

			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
