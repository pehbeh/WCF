{capture assign='miscInformation'}
	<dl>
		<dt>{lang}wcf.user.registrationDate{/lang}</dt>
		<dd>{time time=$user->registrationDate type='plainDate'}</dd>
	</dl>
	{if $user->getOldUsername()}
		<dl>
			<dt>{lang}wcf.user.oldUsername{/lang}</dt>
			<dd>{$user->getOldUsername()}</dd>
		</dl>
	{/if}
	{if $user->canViewOnlineStatus() && $user->getLastActivityTime()}
		<dl>
			<dt>{lang}wcf.user.usersOnline.lastActivity{/lang}</dt>
			<dd>
				{time time=$user->getLastActivityTime()}
				{if $user->getCurrentLocation()}<br>{unsafe:$user->getCurrentLocation()}{/if}
			</dd>
		</dl>
	{/if}
	{if $__wcf->session->getPermission('admin.user.canViewIpAddress') && $user->registrationIpAddress}
		<dl>
			<dt>{lang}wcf.user.registrationIpAddress{/lang}</dt>
			<dd>{unsafe:$user->getRegistrationIpAddress()|ipSearch}</dd>
		</dl>
	{/if}
{/capture}

{hascontent}
	{content}
		{event name='beforeUserOptions'}

		{foreach from=$options item=category}
			{foreach from=$category[categories] item=optionCategory}
				<section class="section">
					<h2 class="sectionTitle">{lang}wcf.user.option.category.{$optionCategory[object]->categoryName}{/lang}</h2>
					
					{foreach from=$optionCategory[options] item=userOption}
						<dl>
							<dt>{$userOption[object]->getTitle()}</dt>
							<dd>{unsafe:$userOption[object]->optionValue}</dd>
						</dl>
					{/foreach}
				</section>
			{/foreach}
		{/foreach}

		{if $miscInformation|trim}
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.user.profile.miscInformation{/lang}</h2>
				
				{unsafe:$miscInformation}
			</section>
		{/if}

		{event name='afterUserOptions'}
	{/content}
{hascontentelse}
	<div class="section">
		<woltlab-core-notice type="info">{lang}wcf.user.profile.content.about.noPublicData{/lang}</woltlab-core-notice>
	</div>
{/hascontent}
