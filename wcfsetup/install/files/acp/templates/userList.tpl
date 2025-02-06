{include file='header' pageTitle='wcf.acp.user.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.user.list{/lang} <span class="badge badgeInverse">{#$gridView->countRows()}</span></h1>
	</div>

	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}
					{if $__wcf->session->getPermission('admin.user.canAddUser')}
						<li><a href="{link controller='UserAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.user.add{/lang}</span></a></li>
					{/if}

					{event name='contentHeaderNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
