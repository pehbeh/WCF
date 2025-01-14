<ol class="sidebarList">
	{foreach from=$eventList item=event}
		<li class="sidebarListItem{if $__wcf->getUserProfileHandler()->isIgnoredUser($event->getUserProfile()->userID, 2)} ignoredUserContent{/if}">
			<div class="sidebarListItem__avatar">
				{user object=$event->getUserProfile() type='avatar32' ariaHidden='true' tabindex='-1'}
			</div>

			<div class="sidebarListItem__content">
				<h3 class="sidebarListItem__title">
					{if $event->getLink()}
						<a href="{$event->getLink()}" class="sidebarListItem__link">{unsafe:$event->getTitle()}</a>
					{else}
						{unsafe:$event->getTitle()}
					{/if}
				</h3>
			</div>

			<div class="sidebarListItem__meta">
				<div class="sidebarListItem__meta__time">
					{time time=$event->time}
				</div>
			</div>
		</li>
	{/foreach}
</ol>
