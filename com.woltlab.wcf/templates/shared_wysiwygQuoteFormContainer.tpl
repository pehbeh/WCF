<div id="{$container->getPrefixedId()|encodeJS}Container"{*
	*} class="messageTabMenuContent messageTabMenuContent--quotes"></div>

<script data-relocate="true">
	require(["WoltLabSuite/Core/Component/Quote/List"], ({ setup }) => {
		setup("{$container->getWysiwygId()|encodeJS}", "{$container->getPrefixedId()|encodeJS}Container");
	});
</script>

{include file='shared_formContainerDependencies'}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Form/Builder/Field/Dependency/Container/WysiwygTab'], ({ WysiwygTab }) => {
		new WysiwygTab('{$container->getPrefixedId()|encodeJS}Container', '{$container->getName()|encodeJS}', '{$container->getWysiwygId()|encodeJS}');
	});
</script>
