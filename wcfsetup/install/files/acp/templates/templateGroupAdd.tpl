{include file='header' pageTitle='wcf.acp.template.group.'|concat:$action}

{if $action === 'edit'}
	<script data-relocate="true">
		require(['Language', 'WoltLabSuite/Core/Acp/Ui/Template/Group/Copy'], function (Language, AcpUiTemplateGroupCopy) {
			Language.addObject({
				'wcf.acp.template.group.copy': '{jslang}wcf.acp.template.group.copy{/jslang}',
				'wcf.acp.template.group.name.error.notUnique': '{jslang}wcf.acp.template.group.name.error.notUnique{/jslang}',
				'wcf.acp.template.group.folderName': '{jslang}wcf.acp.template.group.folderName{/jslang}',
				'wcf.acp.template.group.folderName.error.invalid': '{jslang}wcf.acp.template.group.folderName.error.invalid{/jslang}',
				'wcf.acp.template.group.folderName.error.notUnique': '{jslang}wcf.acp.template.group.folderName.error.notUnique{/jslang}',
				'wcf.global.name': '{jslang}wcf.global.name{/jslang}'
			});

			AcpUiTemplateGroupCopy.init({$formObject->templateGroupID});
		});
	</script>
{/if}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.template.group.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action === 'edit'}<li><a href="#" class="jsButtonCopy button">{icon name='copy'} <span>{lang}wcf.acp.template.group.copy{/lang}</span></a></li>{/if}
			<li><a href="{link controller='TemplateGroupList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.template.group.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{unsafe:$form->getHtml()}

{include file='footer'}
