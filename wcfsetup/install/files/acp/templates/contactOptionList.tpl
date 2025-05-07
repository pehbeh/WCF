{include file='header' pageTitle='wcf.acp.menu.link.contact.options'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.contact.options{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='ContactOptionAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.contact.option.add{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{* TODO add sortable *}

{include file='footer'}
