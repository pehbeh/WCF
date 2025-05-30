{if !$tags|empty}
	<section class="entry_tags" aria-label="{lang}wcf.tagging.tags{/lang}">
		<ul class="tagList">
			{foreach from=$tags item=tag}
				<li>
					<a href="{link controller='Tagged' object=$tag objectType=$objectType}{/link}" class="tag">
						{icon name='tag'}
						{$tag->name}
					</a>
				</li>
			{/foreach}
		</ul>
	</section>
{/if}
