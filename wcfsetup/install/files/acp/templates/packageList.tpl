{include file='header' pageTitle='wcf.acp.package.list'}

<script data-relocate="true">
	$(function() {
		WCF.Language.addObject({
			'wcf.acp.package.searchForUpdates': '{jslang}wcf.acp.package.searchForUpdates{/jslang}',
			'wcf.acp.package.searchForUpdates.noResults': '{jslang}wcf.acp.package.searchForUpdates.noResults{/jslang}',
			'wcf.acp.package.uninstallation.title': '{jslang}wcf.acp.package.uninstallation.title{/jslang}',
		});
		
		{if $__wcf->session->getPermission('admin.configuration.package.canUninstallPackage')}
			new WCF.ACP.Package.Uninstallation($('.jsPackageRow .jsUninstallButton'));
		{/if}
		
		{if $__wcf->session->getPermission('admin.configuration.package.canUpdatePackage')}
			new WCF.ACP.Package.Update.Search(true);
		{/if}
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.package.list{/lang} <span class="badge badgeInverse">{#$items}</span></h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}
					{if $__wcf->session->getPermission('admin.configuration.package.canInstallPackage')}
						<li>
							<a href="{link controller='License'}{/link}" class="button">
								{icon name='cart-arrow-down'}
								<span>{lang}wcf.acp.license{/lang}</span>
							</a>
						</li>
					{/if}

					{if $__wcf->session->getPermission('admin.configuration.package.canUpdatePackage')}
						<li><button type="button" class="button jsButtonSearchForUpdates">{icon name='arrows-rotate'} <span>{lang}wcf.acp.package.searchForUpdates{/lang}</span></button></li>
					{/if}

					{if $__wcf->session->getPermission('admin.configuration.package.canInstallPackage')}
						<li><a href="{link controller='PackageStartInstall'}action=install{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.package.startInstall{/lang}</span></a></li>
					{/if}
					
					{event name='contentHeaderNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{if !(80100 <= PHP_VERSION_ID && PHP_VERSION_ID <= 80399)}
	<woltlab-core-notice type="error">{lang}wcf.global.incompatiblePhpVersion{/lang}</woltlab-core-notice>
{/if}
{foreach from=$taintedApplications item=$taintedApplication}
	<woltlab-core-notice type="error">{lang}wcf.acp.package.application.isTainted{/lang}</woltlab-core-notice>
{/foreach}

{if $recentlyDisabledCustomValues > 0}
	<woltlab-core-notice type="warning">{lang}wcf.acp.language.item.hasRecentlyDisabledCustomValues{/lang}</woltlab-core-notice>
{/if}

{if $__wcf->session->getPermission('admin.configuration.package.canUpdatePackage')}
	{if $availableUpgradeVersion !== null}
		{if $upgradeOverrideEnabled}
			<woltlab-core-notice type="info">{lang version=$availableUpgradeVersion}wcf.acp.package.upgradeOverrideEnabled{/lang}</woltlab-core-notice>
		{else}
			<woltlab-core-notice type="info">{lang version=$availableUpgradeVersion}wcf.acp.package.availableUpgradeVersion{/lang}</woltlab-core-notice>
		{/if}
	{/if}
{/if}

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller='PackageList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th colspan="2" class="columnID{if $sortField == 'packageID'} active {@$sortOrder}{/if}"><a href="{link controller='PackageList'}pageNo={@$pageNo}&sortField=packageID&sortOrder={if $sortField == 'packageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle{if $sortField == 'packageNameI18n'} active {@$sortOrder}{/if}"><a href="{link controller='PackageList'}pageNo={@$pageNo}&sortField=packageNameI18n&sortOrder={if $sortField == 'packageNameI18n' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.package.name{/lang}</a></th>
					<th class="columnText{if $sortField == 'author'} active {@$sortOrder}{/if}"><a href="{link controller='PackageList'}pageNo={@$pageNo}&sortField=author&sortOrder={if $sortField == 'author' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.package.author{/lang}</a></th>
					<th class="columnText">{lang}wcf.acp.package.version{/lang}</th>
					<th class="columnDate{if $sortField == 'updateDate'} active {@$sortOrder}{/if}"><a href="{link controller='PackageList'}pageNo={@$pageNo}&sortField=updateDate&sortOrder={if $sortField == 'updateDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.package.updateDate{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=$package}
					<tr class="jsPackageRow" data-package="{$package->package}">
						<td class="columnIcon">
							{if $package->canUninstall()}
								<button type="button" class="jsUninstallButton jsTooltip" title="{lang}wcf.acp.package.button.uninstall{/lang}" data-object-id="{@$package->packageID}" data-confirm-message="{lang __encode=true}wcf.acp.package.uninstallation.confirm{/lang}" data-is-required="{if $package->isRequired()}true{else}false{/if}" data-is-application="{if $package->isApplication}true{else}false{/if}">
									{icon name='xmark'}
								</button>
							{else}
								<span class="disabled" title="{lang}wcf.acp.package.button.uninstall{/lang}">
									{icon name='xmark'}
								</span>
							{/if}
							
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$package->packageID}</td>
						<td id="packageName{@$package->packageID}" class="columnTitle" title="{$package->getDescription()}">
							<a href="{link controller='Package' id=$package->packageID}{/link}"><span>{$package}</span></a>
							{if $taintedApplications[$package->packageID]|isset}
								<span class="jsTooltip" title="{lang taintedApplication=null}wcf.acp.package.application.isTainted{/lang}">
									{icon name='triangle-exclamation'}
								</span>
							{/if}
						</td>
						<td class="columnText">{if $package->authorURL}<a href="{$package->authorURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank" rel="noopener"{/if}>{$package->author}</a>{else}{$package->author}{/if}</td>
						<td class="columnText">{$package->packageVersion}</td>
						<td class="columnDate">{@$package->updateDate|time}</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
		
	</div>
	
	<footer class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{@$pagesLinks}{/content}
			</div>
		{/hascontent}
		
		{hascontent}
			<nav class="contentFooterNavigation">
				<ul>
					{content}
						{if $__wcf->session->getPermission('admin.configuration.package.canInstallPackage')}
							<li><a href="{link controller='PackageStartInstall'}action=install{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.package.startInstall{/lang}</span></a></li>
						{/if}
						
						{event name='contentFooterNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</footer>
{/if}

{include file='footer'}
