{include file='header' pageTitle='wcf.acp.exceptionLog'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.exceptionLog{/lang}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}{event name='contentHeaderNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{unsafe:$gridView->render()}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Acp/Controller/ExceptionLog/View'], ({ setup }) => {
		{jsphrase name='wcf.acp.exceptionLog.exception.message'}
		setup();
	});
</script>

{include file='footer'}
