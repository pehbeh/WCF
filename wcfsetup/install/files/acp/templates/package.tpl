{include file='header' pageTitle=$package->getName()}

<script data-relocate="true">
	$(function() {
		WCF.Language.addObject({
			'wcf.acp.package.uninstallation.title': '{jslang}wcf.acp.package.uninstallation.title{/jslang}'
		});
		
		new WCF.ACP.Package.Uninstallation($('.jsUninstallButton'));
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{$package->getName()}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}
					{if $package->canUninstall()}
						<li><button type="button" class="button jsUninstallButton" data-object-id="{@$package->packageID}" data-confirm-message="{lang __encode=true}wcf.acp.package.uninstallation.confirm{/lang}" data-is-required="{if $package->isRequired()}true{else}false{/if}" data-is-application="{if $package->isApplication}true{else}false{/if}">{icon name='xmark'} <span>{lang}wcf.acp.package.button.uninstall{/lang}</span></button></li>
					{/if}

					<li><a href="{link controller='PackageList'}{/link}" class="button">{icon name='list'} <span>{lang}wcf.acp.menu.link.package.list{/lang}</span></a></li>

					{event name='contentHeaderNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

<div class="section tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="#information">{lang}wcf.acp.package.information.title{/lang}</a></li>
			{if $package->getRequiredPackages()|count || $package->getDependentPackages()|count}
				<li><a href="#dependencies">{lang}wcf.acp.package.dependencies.title{/lang}</a></li>
			{/if}
			
			{event name='tabMenuTabs'}
		</ul>
	</nav>
	
	<div id="information" class="hidden tabMenuContent">
		<div class="section">
			{if $package->getDescription()}
				<dl>
					<dt>{lang}wcf.acp.package.description{/lang}</dt>
					<dd>{$package->getDescription()}</dd>
				</dl>
			{/if}
			
			<dl>
				<dt>{lang}wcf.acp.package.identifier{/lang}</dt>
				<dd>{$package->package}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.acp.package.version{/lang}</dt>
				<dd>{$package->packageVersion}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.acp.package.packageDate{/lang}</dt>
				<dd>{@$package->packageDate|date}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.acp.package.installDate{/lang}</dt>
				<dd>{@$package->installDate|time}</dd>
			</dl>
			<dl>
				<dt>{lang}wcf.acp.package.updateDate{/lang}</dt>
				<dd>{@$package->updateDate|time}</dd>
			</dl>
			{if $package->packageURL != ''}
				<dl>
					<dt>{lang}wcf.acp.package.url{/lang}</dt>
					<dd><a href="{$package->packageURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{$package->packageURL}</a></dd>
				</dl>
			{/if}
			<dl>
				<dt>{lang}wcf.acp.package.author{/lang}</dt>
				<dd>{if $package->authorURL}<a href="{$package->authorURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{$package->author}</a>{else}{$package->author}{/if}</dd>
			</dl>
			{if $pluginStoreFileID}
				<dl>
					<dt>{lang}wcf.acp.package.pluginStore.file{/lang}</dt>
					<dd><a href="https://pluginstore.woltlab.com/file/{$pluginStoreFileID}/" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{lang}wcf.acp.package.pluginStore.file.link{/lang}</a></dd>
				</dl>
			{/if}
			
			{event name='propertyFields'}
		</div>
		
		{event name='informationFieldsets'}
	</div>
	
	{if $package->getRequiredPackages()|count || $package->getDependentPackages()|count}
		<div id="dependencies" class="tabMenuContainer tabMenuContent">
			<nav class="menu">
				<ul>
					{if $package->getRequiredPackages()|count}
						<li><a href="#dependencies-required">{lang}wcf.acp.package.dependencies.required{/lang}</a></li>
					{/if}
					{if $package->getDependentPackages()|count}
						<li><a href="#dependencies-dependent">{lang}wcf.acp.package.dependencies.dependent{/lang}</a></li>
					{/if}
					
					{event name='dependenciesSubTabMenuTabs'}
				</ul>
			</nav>
			
			{hascontent}
				<div id="dependencies-required" class="tabMenuContent tabularBox hidden">
					<table class="table">
						<thead>
							<tr>
								<th colspan="2" class="columnID">{lang}wcf.global.objectID{/lang}</th>
								<th class="columnTitle">{lang}wcf.acp.package.name{/lang}</th>
								<th class="columnText">{lang}wcf.acp.package.author{/lang}</th>
								<th class="columnText">{lang}wcf.acp.package.version{/lang}</th>
								<th class="columnDigits">{lang}wcf.acp.package.packageDate{/lang}</th>
								
								{event name='requirementColumnHeads'}
							</tr>
						</thead>
						
						<tbody>
							{content}
								{foreach from=$package->getRequiredPackages() item=requiredPackage}
									<tr class="jsPackageRow">
										<td class="columnIcon">
											{if $requiredPackage->canUninstall()}
												<button type="button" class="jsTooltip jsUninstallButton" title="{lang}wcf.acp.package.button.uninstall{/lang}" data-object-id="{@$requiredPackage->packageID}" data-confirm-message="{lang __encode=true package=$requiredPackage}wcf.acp.package.uninstallation.confirm{/lang}" data-is-required="{if $requiredPackage->isRequired()}true{else}false{/if}" data-is-application="{if $requiredPackage->isApplication}true{else}false{/if}">
													{icon name='xmark'}
												</button>
											{else}
												<span class="disabled" title="{lang}wcf.acp.package.button.uninstall{/lang}">
													{icon name='xmark'}
												</span>
											{/if}
										</td>
										<td class="columnID">{@$requiredPackage->packageID}</td>
										<td class="columnTitle" title="{$requiredPackage->getDescription()}"><a href="{link controller='Package' id=$requiredPackage->packageID}{/link}">{$requiredPackage}</a></td>
										<td class="columnText">{if $requiredPackage->authorURL}<a href="{$requiredPackage->authorURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{$requiredPackage->author}</a>{else}{$requiredPackage->author}{/if}</td>
										<td class="columnText">{$requiredPackage->packageVersion}</td>
										<td class="columnDate">{@$requiredPackage->packageDate|date}</td>
										
										{event name='requirementColumns'}
									</tr>
								{/foreach}
							{/content}
						</tbody>
					</table>
				</div>
			{/hascontent}
			
			{hascontent}
				<div id="dependencies-dependent" class="tabMenuContent tabularBox hidden">
					<table class="table">
						<thead>
							<tr>
								<th colspan="2" class="columnID">{lang}wcf.global.objectID{/lang}</th>
								<th class="columnTitle">{lang}wcf.acp.package.name{/lang}</th>
								<th class="columnText">{lang}wcf.acp.package.author{/lang}</th>
								<th class="columnText">{lang}wcf.acp.package.version{/lang}</th>
								<th class="columnDigits">{lang}wcf.acp.package.packageDate{/lang}</th>
								
								{event name='dependencyColumnHeads'}
							</tr>
						</thead>
						
						<tbody>
							{content}
								{foreach from=$package->getDependentPackages() item=dependentPackage}
									<tr class="jsPackageRow">
										<td class="columnIcon">
											{if $dependentPackage->canUninstall()}
												<button type="button" class="jsTooltip jsUninstallButton" title="{lang}wcf.acp.package.button.uninstall{/lang}" data-object-id="{@$dependentPackage->packageID}" data-confirm-message="{lang __encode=true package=$dependentPackage}wcf.acp.package.uninstallation.confirm{/lang}" data-is-required="{if $dependentPackage->isRequired()}true{else}false{/if}" data-is-application="{if $dependentPackage->isApplication}true{else}false{/if}">
													{icon name='xmark'}
												</button>
											{else}
												<span class="disabled" title="{lang}wcf.acp.package.button.uninstall{/lang}">
													{icon name='xmark'}
												</span>
											{/if}
										</td>
										<td class="columnID">{@$dependentPackage->packageID}</td>
										<td class="columnTitle" title="{$dependentPackage->getDescription()}"><a href="{link controller='Package' id=$dependentPackage->packageID}{/link}">{$dependentPackage}</a></td>
										<td class="columnText">{if $dependentPackage->authorURL}<a href="{$dependentPackage->authorURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{$dependentPackage->author}</a>{else}{$dependentPackage->author}{/if}</td>
										<td class="columnText">{$dependentPackage->packageVersion}</td>
										<td class="columnDate">{@$dependentPackage->packageDate|date}</td>
										
										{event name='dependencyColumns'}
									</tr>
								{/foreach}
							{/content}
						</tbody>
					</table>
				</div>
			{/hascontent}
			
			{event name='dependenciesSubTabMenuContents'}
		</div>
	{/if}
	
	{event name='tabMenuContents'}
</div>

{hascontent}
	<footer class="contentFooter">
		<nav class="contentFooterNavigation">
			<ul>
				{content}
					{event name='contentFooterNavigation'}
				{/content}
			</ul>
		</nav>
	</footer>
{/hascontent}

{include file='footer'}
