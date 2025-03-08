<?php

namespace wcf\data;

use wcf\system\attachment\AttachmentHandler;

/**
 * Default interface for actions implementing quick reply with attachment support.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template TContainer of DatabaseObject
 * @template TMessage of DatabaseObject
 * @template TMessageList of DatabaseObjectList
 * @extends IMessageQuickReplyAction<TContainer, TMessage, TMessageList>
 */
interface IAttachmentMessageQuickReplyAction extends IMessageQuickReplyAction
{
    /**
     * Returns an attachment handler object.
     *
     * @param TContainer $container
     * @return AttachmentHandler
     */
    public function getAttachmentHandler(DatabaseObject $container);
}
