<?php

namespace wcf\data\label;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of labels.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends I18nDatabaseObjectList<Label>
 */
class I18nLabelList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['label' => 'labelI18n'];

    /**
     * @inheritDoc
     */
    public $className = Label::class;
}
