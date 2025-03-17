<?php

namespace wcf\data\comment\response;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of comment responses.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<CommentResponse>
 */
class CommentResponseList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = CommentResponse::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'comment_response.time, comment_response.responseID';
}
