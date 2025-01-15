<div id="{$container->getPrefixedId()}Container"{*
	*}{if !$container->getClasses()|empty} class="{implode from=$container->getClasses() item='class' glue=' '}{$class}{/implode}"{/if}{*
	*}{foreach from=$container->getAttributes() key='attributeName' item='attributeValue'} {$attributeName}="{$attributeValue}"{/foreach}{*
	*}{if !$container->checkDependencies()} hidden{/if}{*
*}>
	{include file='shared_formContainerChildren'}
</div>

{include file='shared_formContainerDependencies'}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Form/Builder/Field/Dependency/Container/WysiwygTab'], ({ WysiwygTab }) => {
		new WysiwygTab('{$container->getPrefixedId()|encodeJS}Container', '{$container->getName()|encodeJS}', '{$container->getWysiwygId()|encodeJS}');
	});
</script>
