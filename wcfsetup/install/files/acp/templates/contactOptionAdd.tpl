{assign var='pageTitle' value='wcf.acp.contact.option.'|concat:$action}

{include file='header'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}{$pageTitle}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='ContactSettings'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.contact.settings{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{unsafe:$form->getHtml()}

{include file='footer'}
