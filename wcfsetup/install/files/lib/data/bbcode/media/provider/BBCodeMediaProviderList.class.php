<?php

namespace wcf\data\bbcode\media\provider;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of BBCode media providers.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BBCodeMediaProvider>
 */
class BBCodeMediaProviderList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BBCodeMediaProvider::class;
}
