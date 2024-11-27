{if !$previewMessageFieldID|isset}{assign var=previewMessageFieldID value='text'}{/if}
{if !$previewButtonID|isset}{assign var=previewButtonID value='buttonMessagePreview'}{/if}
{if !$previewMessageObjectType|isset}{assign var=previewMessageObjectType value=''}{/if}
{if !$previewMessageObjectID|isset}{assign var=previewMessageObjectID value=0}{/if}

<button type="button" id="{$previewButtonID}" class="button jsOnly">{lang}wcf.global.button.preview{/lang}</button>

<script data-relocate="true">
	require(["WoltLabSuite/Core/Component/Message/Preview"], ({ setup }) => {
		{jsphrase name='wcf.global.preview'}
		setup(
			'{unsafe:$previewMessageFieldID|encodeJS}',
			'{unsafe:$previewButtonID|encodeJS}',
			'{unsafe:$previewMessageObjectType|encodeJS}',
			'{unsafe:$previewMessageObjectID|encodeJS}'
		);
	});
</script>
