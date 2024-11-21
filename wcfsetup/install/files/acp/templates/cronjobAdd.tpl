{include file='header' pageTitle='wcf.acp.cronjob.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.cronjob.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='CronjobList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.cronjob.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<woltlab-core-notice type="info">{lang}wcf.acp.cronjob.intro{/lang}</woltlab-core-notice>

{unsafe:$form->getHtml()}

{include file='footer'}
