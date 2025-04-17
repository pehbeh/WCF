<ol class="containerBoxList trophyCategoryList tripleColumned">
	{foreach from=$boxUserTrophyList item=boxUserTrophy}
		<li class="box64">
			<div>{unsafe:$boxUserTrophy->getTrophy()->renderTrophy(64)}</div>

			<div class="sidebarItemTitle">
				<h3><a href="{$boxUserTrophy->getTrophy()->getLink()}">{$boxUserTrophy->getTrophy()->getTitle()}</a></h3>
				<small>{if !$boxUserTrophy->getDescription()|empty}<p>{unsafe:$boxUserTrophy->getDescription()}</p>{/if}<p>{user object=$boxUserTrophy->getUserProfile()} <span class="separatorLeft">{time time=$boxUserTrophy->time}</span></p></small>
			</div>
		</li>
	{/foreach}
</ol>
