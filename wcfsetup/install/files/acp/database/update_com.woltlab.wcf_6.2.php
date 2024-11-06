<?php

/**
 * Updates the database layout during the update from 6.1 to 6.2.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_user')
        ->columns([
            IntDatabaseTableColumn::create('avatarFileID')
                ->length(10)
                ->defaultValue(null),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['avatarFileID'])
                ->referencedTable('wcf1_file')
                ->referencedColumns(['fileID'])
                ->onDelete('SET NULL'),
        ]),
];
