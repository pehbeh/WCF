{include file="shared_multipleSelectionFormField"}

{if $field->getVisibleEverywhereFieldId() !== null}
	<script data-relocate="true">
		{
			const label = document.querySelector('label[for="{$field->getPrefixedId()}"]');

			document.querySelectorAll('input[name="{$field->getVisibleEverywhereFieldId()}"]').forEach((input) => {
				input.addEventListener("change", () => {
					setLabelText(input.value);
				});
			});

			function setLabelText (value) {
				label.innerHTML = parseInt(value) === 0 ? '{unsafe:$field->getLabel()|encodeJS}' : '{unsafe:$field->getInvertedLabel()|encodeJS}';
			}

			setLabelText(document.querySelector('input[name="{$field->getVisibleEverywhereFieldId()}"]:checked').value);
		}
	</script>
{/if}
