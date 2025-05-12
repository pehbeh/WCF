<?php

/**
 * Updates the database layout during the update from 6.1 to 6.2.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_user')
        ->columns([
            IntDatabaseTableColumn::create('avatarFileID')
                ->length(10)
                ->defaultValue(null),
            IntDatabaseTableColumn::create('coverPhotoFileID')
                ->length(10)
                ->defaultValue(null),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['avatarFileID'])
                ->referencedTable('wcf1_file')
                ->referencedColumns(['fileID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['coverPhotoFileID'])
                ->referencedTable('wcf1_file')
                ->referencedColumns(['fileID'])
                ->onDelete('SET NULL'),
        ]),
    PartialDatabaseTable::create('wcf1_unfurl_url_image')
        ->columns([
            IntDatabaseTableColumn::create('fileID')
                ->length(10)
                ->defaultValue(null),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['fileID'])
                ->referencedTable('wcf1_file')
                ->referencedColumns(['fileID'])
                ->onDelete('SET NULL'),
        ]),
    PartialDatabaseTable::create('wcf1_contact_option')
        ->columns([
            MediumtextDatabaseTableColumn::create('configuration'),
        ]),
    PartialDatabaseTable::create('wcf1_file')
        ->columns([
            IntDatabaseTableColumn::create('uploadTime'),
        ]),
    DatabaseTable::create('wcf1_user_rank_content')
        ->columns([
            ObjectIdDatabaseTableColumn::create('contentID'),
            IntDatabaseTableColumn::create('rankID')
                ->notNull(),
            IntDatabaseTableColumn::create('languageID'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['contentID']),
            DatabaseTableIndex::create('id')
                ->columns(['rankID', 'languageID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['rankID'])
                ->referencedTable('wcf1_user_rank')
                ->referencedColumns(['rankID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['languageID'])
                ->referencedTable('wcf1_language')
                ->referencedColumns(['languageID'])
                ->onDelete('CASCADE'),
        ]),
    DatabaseTable::create('wcf1_captcha_question_content')
        ->columns([
            ObjectIdDatabaseTableColumn::create('contentID'),
            IntDatabaseTableColumn::create('questionID')
                ->notNull(),
            IntDatabaseTableColumn::create('languageID'),
            NotNullVarchar255DatabaseTableColumn::create('question'),
            MediumtextDatabaseTableColumn::create('answers'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['contentID']),
            DatabaseTableIndex::create('id')
                ->columns(['questionID', 'languageID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['questionID'])
                ->referencedTable('wcf1_captcha_question')
                ->referencedColumns(['questionID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['languageID'])
                ->referencedTable('wcf1_language')
                ->referencedColumns(['languageID'])
                ->onDelete('CASCADE'),
        ]),
];
