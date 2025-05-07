{include file='header' pageTitle='wcf.acp.menu.link.contact.recipients'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.contact.recipients{/lang}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='ContactRecipientAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.contact.recipient.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
    {unsafe:$gridView->render()}
</div>

{* TODO add sortable *}

{include file='footer'}
