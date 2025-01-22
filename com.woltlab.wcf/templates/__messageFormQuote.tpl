<div id="quotes_{if $wysiwygSelector|isset}{$wysiwygSelector}{else}text{/if}"
	class="messageTabMenuContent messageTabMenuContent--quotes"></div>

<script data-relocate="true">
require(["WoltLabSuite/Core/Component/Quote/List"], ({ setup }) => {
	setup("{if $wysiwygSelector|isset}{$wysiwygSelector}{else}text{/if}");
});
</script>
