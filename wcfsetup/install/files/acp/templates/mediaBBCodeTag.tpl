{if !$removeLinks|isset}{assign var='removeLinks' value=false}{/if}
<span class="mediaBBCode{if $float != 'none'} messageFloatObject{$float|ucfirst}{/if}">
	{if $thumbnailSize != 'original'}
		{if !$removeLinks}<a href="{$mediaLink}" class="embeddedAttachmentLink jsImageViewer">{/if}<img src="{$thumbnailLink}" alt="{$media->altText}" title="{$media->title}" width="{@$media->getThumbnailWidth($thumbnailSize)}" height="{@$media->getThumbnailHeight($thumbnailSize)}" loading="lazy">{if !$removeLinks}</a>{/if}
	{else}
		<img src="{$mediaLink}" alt="{$media->altText}" title="{$media->title}" width="{@$media->width}" height="{@$media->height}" loading="lazy">
	{/if}
	
	{if $media->caption}
		<span class="mediaBBCodeCaption">
			<span class="mediaBBCodeCaptionAlignment">
				{if $media->captionEnableHtml}
					{@$media->caption}
				{else}
					{$media->caption}
				{/if}
			</span>
		</span>
	{/if}
</span>
