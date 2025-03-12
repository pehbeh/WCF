<?php

namespace wcf\data\package\update\version;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of package update versions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<PackageUpdateVersion>
 */
class PackageUpdateVersionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PackageUpdateVersion::class;
}
