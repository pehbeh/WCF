<?php

namespace wcf\event\comment;

use wcf\data\comment\Comment;
use wcf\event\IPsr14Event;

/**
 * Indicates that multiple comments have been deleted.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
final class CommentsDeleted implements IPsr14Event
{
    /**
     * @param Comment[] $comments
     */
    public function __construct(
        public readonly array $comments,
    ) {
    }
}
