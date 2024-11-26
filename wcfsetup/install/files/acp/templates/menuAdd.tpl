{include file='header' pageTitle='wcf.acp.menu.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action == 'edit'}
				<li><a href="{link controller='MenuItemList' id=$formObject->menuID}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.item.list{/lang}</span></a></li>
			{/if}
			<li><a href="{link controller='MenuList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{unsafe:$form->getHtml()}

{include file='footer'}
