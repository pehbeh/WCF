<?php

namespace wcf\system\language;

use wcf\system\WCF;

/**
 * Helper class for multilingual content tables `*_content`.
 * The content table requires the columns `languageID` and a column with the same name as the primary column of the associated object.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class MultilingualHelper
{
    /**
     * Returns a subquery which returns the column in the content table depending on the preferred language,
     * default language and if not available the content of the lowest languageID.
     */
    public static function subqueryForContentTable(
        string $selectColumn,
        string $contentTableName,
        string $objectIDColum,
        string $baseTable,
        ?int $preferredLanguageID = null
    ): string {
        if ($preferredLanguageID === null) {
            $preferredLanguageID = WCF::getLanguage()->languageID;
        }
        $defaultLanguageID = LanguageFactory::getInstance()->getDefaultLanguageID();

        return <<<SQL
        (
            SELECT   {$selectColumn}
            FROM     {$contentTableName}
            WHERE    {$objectIDColum} = {$baseTable}.{$objectIDColum}
            ORDER BY CASE
                WHEN languageID = {$preferredLanguageID} THEN -2
                WHEN languageID = {$defaultLanguageID} THEN -1
                ELSE languageID
            END ASC
            LIMIT    1
        )
        SQL;
    }
}
