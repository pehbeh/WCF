<div class="popover__layout">
	{if $article->getTeaserImage()}
		<div class="popover__coverPhoto">
			<img class="popover__coverPhoto__image" src="{$article->getTeaserImage()->getThumbnailLink('medium')}" alt="">
		</div>
	{/if}
	
	<div class="popover__header">
		{event name='beforeHeader'}
		
		<div class="popover__avatar">
			{user object=$article->getUserProfile() type='avatar48' ariaHidden='true' tabindex='-1'}
		</div>
		<div class="popover__title">
			<a href="{$article->getLink()}">{$article->getTitle()}</a>
		</div>
		<div class="popover__time">
			{time time=$article->time}
		</div>

		{event name='afterHeader'}
	</div>

	{event name='beforeText'}

	<div class="popover__text htmlContent">
		{unsafe:$article->getFormattedTeaser()}
	</div>

	{event name='afterText'}
</div>
