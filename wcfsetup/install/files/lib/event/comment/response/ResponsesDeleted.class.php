<?php

namespace wcf\event\comment\response;

use wcf\data\comment\response\CommentResponse;
use wcf\event\IPsr14Event;

/**
 * Indicates that multiple responses have been deleted.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
final class ResponsesDeleted implements IPsr14Event
{
    /**
     * @param CommentResponse[] $responses
     */
    public function __construct(
        public readonly array $responses,
    ) {}
}
