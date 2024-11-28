{include file='header' pageTitle='wcf.acp.user.option.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.user.option.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='UserOptionList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.user.option.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>

</header>

{unsafe:$form->getHtml()}

{include file='footer'}
