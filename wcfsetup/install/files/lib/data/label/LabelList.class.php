<?php

namespace wcf\data\label;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of labels.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Label>
 */
class LabelList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Label::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'label.showOrder ASC, label.labelID ASC';
}
