<?php

namespace wcf\data\file\thumbnail;

use wcf\data\DatabaseObjectList;

/**
 * @author Alexander Ebert
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.1
 *
 * @extends DatabaseObjectList<FileThumbnail>
 */
class FileThumbnailList extends DatabaseObjectList
{
    public $className = FileThumbnail::class;
}
