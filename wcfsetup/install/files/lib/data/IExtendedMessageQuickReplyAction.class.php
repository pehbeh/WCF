<?php

namespace wcf\data;

/**
 * @template TContainer of DatabaseObject
 * @template TMessage of DatabaseObject
 * @template TMessageList of DatabaseObjectList
 * @extends IMessageQuickReplyAction<TContainer, TMessage, TMessageList>
 * @deprecated 5.5 The concept of starting a message in a simple editor and then migrating to an extended editor no longer exists.
 */
interface IExtendedMessageQuickReplyAction extends IMessageQuickReplyAction
{
    /**
     * Saves message and jumps to extended mode.
     *
     * @return mixed[]
     */
    public function jumpToExtended();

    /**
     * Validates parameters to jump to extended mode.
     *
     * @return void
     */
    public function validateJumpToExtended();
}
