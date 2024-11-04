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

{include file='shared_formError'}

{if !$logFiles|empty}
	<form method="post" action="{link controller='ExceptionLogView'}{/link}">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.acp.exceptionLog.search{/lang}</h2>
			
			<div class="row rowColGap formGrid">
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="exceptionID" name="exceptionID" value="{$exceptionID}" placeholder="{lang}wcf.acp.exceptionLog.search.exceptionID{/lang}" autofocus class="long">
					</dd>
				</dl>
				
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<select id="logFile" name="logFile">
							<option value="">{lang}wcf.acp.exceptionLog.search.logFile{/lang}</option>
							{htmlOptions options=$logFiles selected=$logFile}
						</select>
					</dd>
				</dl>
			</div>
		</section>
		
		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		</div>
	</form>
{/if}

{unsafe:$gridView->render()}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Acp/Controller/ExceptionLog/View'], ({ setup }) => {
		{jsphrase name='wcf.acp.exceptionLog.exception.message'}
		setup();
	});
</script>

{include file='footer'}
