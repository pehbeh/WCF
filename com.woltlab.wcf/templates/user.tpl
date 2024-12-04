{capture assign='pageTitle'}{$user->username} - {lang}wcf.user.members{/lang}{/capture}

{capture assign='headContent'}
	{event name='javascriptInclude'}
	<script data-relocate="true">
		{if $__wcf->getUser()->userID && $__wcf->getUser()->userID != $user->userID}
			require(['Language', 'WoltLabSuite/Core/Ui/User/Editor', 'WoltLabSuite/Core/Ui/User/Profile/Menu/Item/Ignore', 'WoltLabSuite/Core/Ui/User/Profile/Menu/Item/Follow'], function(Language, UiUserEditor, UiUserProfileMenuItemIgnore, UiUserProfileMenuItemFollow) {
				Language.addObject({
					'wcf.acp.user.disable': '{jslang}wcf.acp.user.disable{/jslang}',
					'wcf.acp.user.enable': '{jslang}wcf.acp.user.enable{/jslang}',
					'wcf.user.ban': '{jslang}wcf.user.ban{/jslang}',
					'wcf.user.banned': '{jslang}wcf.user.banned{/jslang}',
					'wcf.user.ban.confirmMessage': '{jslang}wcf.user.ban.confirmMessage{/jslang}',
					'wcf.user.ban.expires': '{jslang}wcf.user.ban.expires{/jslang}',
					'wcf.user.ban.expires.description': '{jslang}wcf.user.ban.expires.description{/jslang}',
					'wcf.user.ban.neverExpires': '{jslang}wcf.user.ban.neverExpires{/jslang}',
					'wcf.user.ban.reason.description': '{jslang}wcf.user.ban.reason.description{/jslang}',
					'wcf.user.button.follow': '{jslang}wcf.user.button.follow{/jslang}',
					'wcf.user.button.unfollow': '{jslang}wcf.user.button.unfollow{/jslang}',
					'wcf.user.button.ignore': '{jslang}wcf.user.button.ignore{/jslang}',
					'wcf.user.button.unignore': '{jslang}wcf.user.button.unignore{/jslang}',
					'wcf.user.disableAvatar': '{jslang}wcf.user.disableAvatar{/jslang}',
					'wcf.user.disableAvatar.confirmMessage': '{jslang}wcf.user.disableAvatar.confirmMessage{/jslang}',
					'wcf.user.disableAvatar.expires': '{jslang}wcf.user.disableAvatar.expires{/jslang}',
					'wcf.user.disableAvatar.expires.description': '{jslang}wcf.user.disableAvatar.expires.description{/jslang}',
					'wcf.user.disableAvatar.neverExpires': '{jslang}wcf.user.disableAvatar.neverExpires{/jslang}',
					'wcf.user.disableCoverPhoto': '{jslang}wcf.user.disableCoverPhoto{/jslang}',
					'wcf.user.disableCoverPhoto.confirmMessage': '{jslang}wcf.user.disableCoverPhoto.confirmMessage{/jslang}',
					'wcf.user.disableCoverPhoto.expires': '{jslang}wcf.user.disableCoverPhoto.expires{/jslang}',
					'wcf.user.disableCoverPhoto.expires.description': '{jslang}wcf.user.disableCoverPhoto.expires.description{/jslang}',
					'wcf.user.disableCoverPhoto.neverExpires': '{jslang}wcf.user.disableCoverPhoto.neverExpires{/jslang}',
					'wcf.user.disableSignature': '{jslang}wcf.user.disableSignature{/jslang}',
					'wcf.user.disableSignature.confirmMessage': '{jslang}wcf.user.disableSignature.confirmMessage{/jslang}',
					'wcf.user.disableSignature.expires': '{jslang}wcf.user.disableSignature.expires{/jslang}',
					'wcf.user.disableSignature.expires.description': '{jslang}wcf.user.disableSignature.expires.description{/jslang}',
					'wcf.user.disableSignature.neverExpires': '{jslang}wcf.user.disableSignature.neverExpires{/jslang}',
					'wcf.user.edit': '{jslang}wcf.user.edit{/jslang}',
					'wcf.user.enableAvatar': '{jslang}wcf.user.enableAvatar{/jslang}',
					'wcf.user.enableCoverPhoto': '{jslang}wcf.user.enableCoverPhoto{/jslang}',
					'wcf.user.enableSignature': '{jslang}wcf.user.enableSignature{/jslang}',
					'wcf.user.unban': '{jslang}wcf.user.unban{/jslang}'
				});
				
				{if $isAccessible}
					UiUserEditor.init();
				{/if}
				
				{if !$user->getPermission('user.profile.cannotBeIgnored') || $__wcf->getUserProfileHandler()->isIgnoredUser($user->userID)}
					new UiUserProfileMenuItemIgnore({@$user->userID}, {if $__wcf->getUserProfileHandler()->isIgnoredUser($user->userID)}true{else}false{/if});
				{/if}
				
				{if !$user->isIgnoredUser($__wcf->user->userID)}
					new UiUserProfileMenuItemFollow({@$user->userID}, {if $__wcf->getUserProfileHandler()->isFollowing($user->userID)}true{else}false{/if});
				{/if}
			});
		{/if}

		$(function() {
			{if $__wcf->getUser()->userID && $__wcf->getUser()->userID != $user->userID}
				WCF.Language.addObject({
					'wcf.user.activityPoint': '{jslang}wcf.user.activityPoint{/jslang}'
				});
			{/if}
			
			new WCF.User.Profile.TabMenu({@$user->userID});
			
			{if $user->canEdit() || ($__wcf->getUser()->userID == $user->userID && $user->canEditOwnProfile())}
				WCF.Language.addObject({
					'wcf.user.editProfile': '{jslang}wcf.user.editProfile{/jslang}'
				});
				
				new WCF.User.Profile.Editor({@$user->userID}, {if $editOnInit}true{else}false{/if});
			{/if}
			
			{event name='javascriptInit'}
		});

		require(['WoltLabSuite/Core/Controller/User/Profile'], ({ setup }) => {
			setup({$user->userID});
		});
	</script>
	
	<noscript>
		<style type="text/css">
			#profileContent > .tabMenu > ul > li:not(:first-child) {
				display: none !important;
			}
			
			#profileContent > .tabMenuContent:not(:first-of-type) {
				display: none !important;
			}
		</style>
	</noscript>
{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader userProfileUser userProfileUserWithCoverPhoto" data-object-id="{@$user->userID}"{if $isAccessible}
		{if $__wcf->session->getPermission('admin.user.canBanUser')}
			data-banned="{@$user->banned}"
		{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableAvatar')}
			data-disable-avatar="{@$user->disableAvatar}"
		{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableSignature')}
			data-disable-signature="{@$user->disableSignature}"
		{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableCoverPhoto')}
			data-disable-cover-photo="{@$user->disableCoverPhoto}"
		{/if}
		{if $__wcf->session->getPermission('admin.user.canEnableUser')}
			data-is-disabled="{if $user->activationCode}true{else}false{/if}"
		{/if}
		{/if}>
		<div class="userProfileCoverPhoto" style="background-image: url({$user->getCoverPhoto()->getURL()})">
			{if $user->canEditCoverPhoto()}
				<ul class="userProfileManageCoverPhoto buttonGroup buttonList smallButtons">
					<li>
						<button type="button" data-edit-cover-photo="{link controller="UserCoverPhoto" id=$user->userID}{/link}" data-default-cover-photo="{$__wcf->styleHandler->getStyle()->getCoverPhotoUrl()}" class="button small">
							{icon name='pencil'} {lang}wcf.user.coverPhoto.management{/lang}
						</button>
					</li>
				</ul>
			{/if}
		</div>
		<div class="contentHeaderIcon">
			{if $user->userID == $__wcf->user->userID}
				<a href="{link controller='AvatarEdit'}{/link}" class="jsTooltip" title="{lang}wcf.user.avatar.edit{/lang}">{@$user->getAvatar()->getImageTag(128)}</a>
			{else}
				<span>{@$user->getAvatar()->getImageTag(128)}</span>
			{/if}
			{if $user->isOnline()}<span class="badge green badgeOnline">{lang}wcf.user.online{/lang}</span>{/if}
		</div>
		
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">
				<span class="userProfileUsername">{$user->username}</span>
				{if $user->banned}
					<span class="jsTooltip jsUserBanned" title="{lang}wcf.user.banned{/lang}">
						{icon size=24 name='lock'}
					</span>
				{/if}
				{if MODULE_USER_RANK}
					{if $user->getUserTitle()}
						<span class="badge userTitleBadge{if $user->getRank() && $user->getRank()->cssClassName} {@$user->getRank()->cssClassName}{/if}">{$user->getUserTitle()}</span>
					{/if}
					{if $user->getRank() && $user->getRank()->rankImage}
						<span class="userRankImage">{@$user->getRank()->getImage()}</span>
					{/if}
				{/if}
				
				{event name='afterContentTitle'}
			</h1>
			
			<div class="contentHeaderDescription">
				{if MODULE_TROPHY && $__wcf->session->getPermission('user.profile.trophy.canSeeTrophies') && ($user->isAccessible('canViewTrophies') || $user->userID == $__wcf->session->userID) && $specialTrophyCount}
					<div class="specialTrophyUserContainer">
						<ul>
							{foreach from=$user->getSpecialTrophies() item=trophy}
								<li><a href="{@$trophy->getLink()}">{@$trophy->renderTrophy(32, true)}</a></li>
							{/foreach}
							{if $user->trophyPoints > $specialTrophyCount}
								<li><a href="#" class="jsTooltip userTrophyOverlayList" data-user-id="{$user->userID}" title="{lang}wcf.user.trophy.showTrophies{/lang}" role="button">{lang trophyCount=$user->trophyPoints-$specialTrophyCount}wcf.user.trophy.showMoreTrophies{/lang}</a></li>
							{/if}
						</ul>
					</div>
				{/if}
				<ul class="inlineList commaSeparated">
					{if !$user->isProtected()}
						{if $user->isVisibleOption('gender') && $user->gender}<li>{$user->getFormattedUserOption('gender')}</li>{/if}
						{if $user->isVisibleOption('birthday') && $user->getAge()}<li>{@$user->getAge()}</li>{/if}
						{if $user->isVisibleOption('location') && $user->location}<li>{lang}wcf.user.membersList.location{/lang}</li>{/if}
					{/if}
					{if $user->getOldUsername()}<li>{lang}wcf.user.profile.oldUsername{/lang}</li>{/if}
					<li>{lang}wcf.user.membersList.registrationDate{/lang}</li>
					{event name='userDataRow1'}
				</ul>
				
				{hascontent}
					<ul class="inlineList commaSeparated">
						{content}
							{if $user->canViewOnlineStatus() && $user->getLastActivityTime()}
								<li>{lang}wcf.user.usersOnline.lastActivity{/lang}: {@$user->getLastActivityTime()|time}</li>
								{if $user->getCurrentLocation()}<li>{@$user->getCurrentLocation()}</li>{/if}
							{/if}
							{if $__wcf->session->getPermission('admin.user.canViewIpAddress') && $user->registrationIpAddress}
								<li>{lang}wcf.user.registrationIpAddress{/lang}: <span class="userRegistrationIpAddress">{@$user->getRegistrationIpAddress()|ipSearch}</span></li>
							{/if}
						{/content}
					</ul>
				{/hascontent}
				
				<dl class="plain inlineDataList">
					{include file='userInformationStatistics'}
					
					{if $user->profileHits}
						<dt{if $user->getProfileAge() > 1} title="{lang}wcf.user.profileHits.hitsPerDay{/lang}"{/if}>{lang}wcf.user.profileHits{/lang}</dt>
						<dd>{#$user->profileHits}</dd>
					{/if}
				</dl>
			</div>
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul class="userProfileButtonContainer">
					{content}
						{if $user->canEdit() || ($__wcf->getUser()->userID == $user->userID && $user->canEditOwnProfile())}
							<li><a href="#" class="jsButtonEditProfile button buttonPrimary">{icon name='pencil'} <span>{lang}wcf.user.editProfile{/lang}</span></a></li>
						{/if}
						
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{include file='userSidebar' assign='sidebarRight'}

{capture assign='__menuSearch'}
	{event name='menuSearch'}
	{* DEPRECATED *}{event name='quickSearchItems'}
{/capture}
{assign var='__menuSearch' value=$__menuSearch|trim}

{capture assign='__menuManagement'}
	{event name='menuManagement'}
	{if $isAccessible && $__wcf->user->userID != $user->userID && ($__wcf->session->getPermission('admin.user.canBanUser') || $__wcf->session->getPermission('admin.user.canDisableAvatar') || $__wcf->session->getPermission('admin.user.canDisableSignature') || $__wcf->session->getPermission('admin.user.canEnableUser') || ($__wcf->session->getPermission('admin.general.canUseAcp') && $__wcf->session->getPermission('admin.user.canEditUser')){event name='moderationDropdownPermissions'})}
		{if $__wcf->session->getPermission('admin.user.canBanUser')}<li><a href="#" class="jsButtonUserBan">{lang}wcf.user.{if $user->banned}un{/if}ban{/lang}</a></li>{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableAvatar')}<li><a href="#" class="jsButtonUserDisableAvatar">{lang}wcf.user.{if $user->disableAvatar}enable{else}disable{/if}Avatar{/lang}</a></li>{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableSignature')}<li><a href="#" class="jsButtonUserDisableSignature">{lang}wcf.user.{if $user->disableSignature}enable{else}disable{/if}Signature{/lang}</a></li>{/if}
		{if $__wcf->session->getPermission('admin.user.canDisableCoverPhoto')}<li><a href="#" class="jsButtonUserDisableCoverPhoto">{lang}wcf.user.{if $user->disableCoverPhoto}enable{else}disable{/if}CoverPhoto{/lang}</a></li>{/if}
		{if $__wcf->session->getPermission('admin.user.canEnableUser')}<li><a href="#" class="jsButtonUserEnable">{lang}wcf.acp.user.{if $user->pendingActivation()}enable{else}disable{/if}{/lang}</a></li>{/if}
		
		{if $__wcf->session->getPermission('admin.general.canUseAcp') && $__wcf->session->getPermission('admin.user.canEditUser')}<li><a href="{link controller='UserEdit' object=$user isACP=true}{/link}" class="jsUserInlineEditor">{lang}wcf.user.edit{/lang}</a></li>{/if}
	{/if}
{/capture}
{assign var='__menuManagement' value=$__menuManagement|trim}

{capture assign='contentInteractionButtons'}
	{if $__menuSearch}
		<div class="contentInteractionButton dropdown jsOnly">
			<button type="button" class="button small dropdownToggle">{icon name='magnifying-glass'} <span>{lang}wcf.user.searchUserContent{/lang}</span></button>
			<ul class="dropdownMenu userProfileButtonMenu" data-menu="search">
				{@$__menuSearch}
			</ul>
		</div>
	{/if}
	{if $__menuManagement}
		<div class="contentInteractionButton dropdown jsOnly">
			<button type="button" class="button small dropdownToggle">{icon name='gear'} <span>{lang}wcf.user.profile.management{/lang}</span></button>
			<ul class="dropdownMenu userProfileButtonMenu" data-menu="management">
				{@$__menuManagement}
			</ul>
		</div>
	{/if}
{/capture}

{capture assign='contentInteractionDropdownItems'}
	{* DEPRECATED *}{event name='menuCustomization'}
	{event name='menuInteraction'}
								
	{if $user->userID != $__wcf->user->userID}
		{if $user->isAccessible('canViewEmailAddress') || $__wcf->session->getPermission('admin.user.canEditMailAddress')}
			<li><a href="mailto:{@$user->getEncodedEmail()}">{lang}wcf.user.button.mail{/lang}</a></li>
		{/if}
	{/if}
	
	{if $user->userID != $__wcf->user->userID && $__wcf->session->getPermission('user.profile.canReportContent')}
		<li>
			<a
				href="#"
				role="button"
				data-report-content="com.woltlab.wcf.user"
				data-object-id="{$user->userID}"
			>
				{lang}wcf.user.profile.report{/lang}
			</a>
		</li>
	{/if}
{/capture}

{include file='header'}

{if !$user->isProtected()}
	<div id="profileContent" class="section tabMenuContainer userProfileContent" data-active="{$__wcf->getUserProfileMenu()->getActiveMenuItem($userID)->getIdentifier()}">
		<nav class="tabMenu">
			<ul>
				{foreach from=$__wcf->getUserProfileMenu()->getMenuItems() item=menuItem}
					{if $menuItem->getContentManager()->isVisible($userID)}
						<li><a href="#{$menuItem->getIdentifier()|rawurlencode}">{$menuItem}</a></li>
					{/if}
				{/foreach}
			</ul>
		</nav>
		
		{foreach from=$__wcf->getUserProfileMenu()->getMenuItems() item=menuItem}
			{if $menuItem->getContentManager()->isVisible($userID)}
				<div id="{$menuItem->getIdentifier()}" class="tabMenuContent" data-menu-item="{$menuItem->menuItem}">
					{if $menuItem === $__wcf->getUserProfileMenu()->getActiveMenuItem($userID)}
						{@$profileContent}
					{/if}
				</div>
			{/if}
		{/foreach}
	</div>
{else}
	<woltlab-core-notice type="info">{lang}wcf.user.profile.protected{/lang}</woltlab-core-notice>
{/if}

{include file='footer'}
