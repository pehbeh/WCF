<?php

/**
 * Updates the database layout during the update from 6.1 to 6.2.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\TextDatabaseTableColumn;
use wcf\system\database\table\column\TinyintDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_contact_option')
        ->columns([
            MediumtextDatabaseTableColumn::create('defaultValue')
                ->drop(),
            TextDatabaseTableColumn::create('validationPattern')
                ->drop(),
            MediumtextDatabaseTableColumn::create('selectOptions')
                ->drop(),
            TinyintDatabaseTableColumn::create('required')
                ->drop(),
        ]),
    PartialDatabaseTable::create('wcf1_captcha_question')
        ->columns([
            NotNullVarchar255DatabaseTableColumn::create('question')
                ->drop(),
            MediumtextDatabaseTableColumn::create('answers')
                ->drop(),
        ]),
];
