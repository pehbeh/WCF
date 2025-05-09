<section class="box" data-static-box-identifier="com.woltlab.wcf.DeletedContentListMenu">
	<h2 class="boxTitle">{lang}wcf.moderation.deletedContent.objectTypes{/lang}</h2>
	
	<div class="boxContent">
		<nav>
			<ul class="boxMenu">
				{foreach from=$types item=type}
					<li{if $type->id === $activeId} class="active"{/if}>
						<a class="boxMenuLink" href="{$type->link}">{lang}{$type->languageItem}{/lang}</a>
					</li>
				{/foreach}
			</ul>
		</nav>
	</div>
</section>
