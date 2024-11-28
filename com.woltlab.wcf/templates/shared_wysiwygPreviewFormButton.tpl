<button type="button" id="{$button->getPrefixedId()}"{*
	*} class="button{if !$button->getClasses()|empty} {implode from=$button->getClasses() item='class' glue=' '}{$class}{/implode}{/if}"{*
	*}{foreach from=$button->getAttributes() key='attributeName' item='attributeValue'} {$attributeName}="{$attributeValue}"{/foreach}{*
	*}{if $button->getAccessKey()} accesskey="{$button->getAccessKey()}"{/if}{*
*}>{$button->getLabel()}</button>

<script data-relocate="true">
	require(["WoltLabSuite/Core/Component/Message/Preview"], ({ setup }) => {
		{jsphrase name='wcf.global.preview'}
		setup(
			'{unsafe:$button->getPrefixedWysiwygId()|encodeJS}',
			'{unsafe:$button->getPrefixedId()|encodeJS}',
			'{unsafe:$button->getObjectType()->objectType|encodeJS}',
			'{unsafe:$button->getObjectId()|encodeJS}'
		);
	});
</script>
