<textarea {*
	*}id="{$field->getPrefixedId()}" {*
	*}name="{$field->getPrefixedId()}" {*
	*}class="wysiwygTextarea" {*
	*}data-disable-attachments="{if $field->supportsAttachments()}false{else}true{/if}" {*
	*}data-support-mention="{if $field->supportsMentions()}true{else}false{/if}"{*
	*}{if $field->getAutosaveId() !== null}{*
		*} data-autosave="{@$field->getAutosaveId()}"{*
		*}{if $field->getLastEditTime() !== 0}{*
			*} data-autosave-last-edit-time="{@$field->getLastEditTime()}"{*
		*}{/if}{*
	*}{/if}{*
	*}{foreach from=$field->getFieldAttributes() key='attributeName' item='attributeValue'} {$attributeName}="{$attributeValue}"{/foreach}{*
*}>{$field->getValue()}</textarea>

{include file='shared_wysiwyg' wysiwygSelector=$field->getPrefixedId()}
