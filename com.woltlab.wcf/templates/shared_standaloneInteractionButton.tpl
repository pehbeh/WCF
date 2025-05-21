<div class="dropdown {$cssClassName}" id="{$containerID}">
	<button 
		type="button"
		class="button dropdownToggle {$buttonCssClassName}"
		{if !$label}aria-label="{lang}wcf.global.button.more{/lang}"{/if}
	>
		{icon name=$icon}
		{if $label}
			<span>{$label}</span>
		{/if}
	</button>

	<ul class="dropdownMenu">
		{unsafe:$contextMenuOptions}
	</ul>
</div>

<script data-relocate="true">
	require(['WoltLabSuite/Core/Component/Interaction/StandaloneButton'], ({ StandaloneButton }) => {
		new StandaloneButton(
			document.getElementById('{unsafe:$containerID|encodeJS}'),
			'{unsafe:$providerClassName|encodeJS}',
			'{unsafe:$objectID|encodeJS}',
			'{unsafe:$redirectUrl|encodeJS}'
		);
	});
</script>

{unsafe:$initializationCode}
