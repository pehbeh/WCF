{include file='header' pageTitle='wcf.acp.template.group.'|concat:$action}

{if $action === 'edit'}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Acp/Ui/Template/Group/Copy'], (AcpUiTemplateGroupCopy) => {
			AcpUiTemplateGroupCopy.init();
		});
	</script>
{/if}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.template.group.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action === 'edit'}<li><button type="button" class="jsButtonCopy button" data-endpoint="{link controller="TemplateGroupCopy" id=$formObject->templateGroupID}{/link}">{icon name='copy'} <span>{lang}wcf.acp.template.group.copy{/lang}</span></button></li>{/if}
			<li><a href="{link controller='TemplateGroupList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.template.group.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{unsafe:$form->getHtml()}

{include file='footer'}
