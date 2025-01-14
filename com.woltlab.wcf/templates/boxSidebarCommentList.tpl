<ol class="sidebarList">
	{foreach from=$boxCommentList item=boxComment}
		<li class="sidebarListItem">
			<div class="sidebarListItem__avatar">
				{user object=$boxComment->getUserProfile() type='avatar32' ariaHidden='true' tabindex='-1'}
			</div>

			<div class="sidebarListItem__content">
				<h3 class="sidebarListItem__title">
					<a href="{$boxComment->getLink()}" class="sidebarListItem__link">{$boxComment->title}</a>
				</h3>
				
				<div class="sidebarListItem__description">
					{unsafe:$boxComment->getExcerpt(150)}
				</div>
			</div>

			<div class="sidebarListItem__meta">
				<div class="sidebarListItem__meta__author">
					{user object=$boxComment->getUserProfile() tabindex='-1'}
				</div>
				
				<div class="sidebarListItem__meta__time">
					{time time=$boxComment->time}
				</div>
			</div>
		</li>
	{/foreach}
</ol>
