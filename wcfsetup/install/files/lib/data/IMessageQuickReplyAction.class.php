<?php

namespace wcf\data;

use wcf\system\html\input\HtmlInputProcessor;

/**
 * Default interface for actions implementing quick reply.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template TContainer of DatabaseObject
 * @template TMessage of DatabaseObject
 * @template TMessageList of DatabaseObjectList
 */
interface IMessageQuickReplyAction
{
    /**
     * Creates a new message object.
     *
     * @return TMessage
     */
    public function create();

    /**
     * Returns the current html input processor or a new one if `$message` is not null.
     *
     * @param ?string $message source message
     * @return HtmlInputProcessor
     */
    public function getHtmlInputProcessor(?string $message = null);

    /**
     * Returns a message list object.
     *
     * @param TContainer $container
     * @return TMessageList
     */
    public function getMessageList(DatabaseObject $container, int $lastMessageTime);

    /**
     * Returns page no for given container object.
     *
     * @param TContainer $container
     * @return array{0: int, 1: int}
     */
    public function getPageNo(DatabaseObject $container);

    /**
     * Returns the redirect url.
     *
     * @param TContainer $container
     * @param TMessage $message
     * @return string
     */
    public function getRedirectUrl(DatabaseObject $container, DatabaseObject $message);

    /**
     * Validates the message.
     *
     * @param TContainer $container
     * @return void
     */
    public function validateMessage(DatabaseObject $container, HtmlInputProcessor $htmlInputProcessor);

    /**
     * Creates a new message and returns it.
     *
     * @return mixed[]
     */
    public function quickReply();

    /**
     * Validates the container object for quick reply.
     *
     * @param TContainer $container
     * @return void
     */
    public function validateContainer(DatabaseObject $container);

    /**
     * Validates parameters for quick reply.
     *
     * @return void
     */
    public function validateQuickReply();
}
