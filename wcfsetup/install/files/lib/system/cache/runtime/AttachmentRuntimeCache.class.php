<?php

namespace wcf\system\cache\runtime;

use wcf\data\attachment\Attachment;
use wcf\data\attachment\AttachmentList;

/**
 * Runtime cache implementation for shared attachments.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 *
 * @extends AbstractRuntimeCache<Attachment>
 */
final class AttachmentRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = AttachmentList::class;
}
