<input type="hidden" {*
	*}id="{$field->getPrefixedId()}" {*
	*}name="{$field->getPrefixedId()}" {*
	*}value="{$field->getValue()}"{*
*}>

<script data-relocate="true">
	require(['WoltLabSuite/Core/Form/Builder/Field/SelectOptions'], ({ setup }) => {
		{jsphrase name='wcf.form.selectOptions.key'}
		{jsphrase name='wcf.form.selectOptions.value'}

		const availableLanguages = { {implode from=$availableLanguages key=languageID item=languageName}{$languageID}: '{unsafe:$languageName|encodeJS}'{/implode} };
			
		setup(document.getElementById('{unsafe:$field->getPrefixedId()|encodeJS}'), availableLanguages);
	});
</script>
