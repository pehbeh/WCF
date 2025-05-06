<?php

namespace wcf\system\form\builder\data\processor;

use wcf\data\IStorableObject;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class I18nFormDataProcessor extends AbstractFormDataProcessor
{
    public function __construct(
        public readonly string $contentTableName,
        /**
         * Mapping of field id to database column name
         *
         * @var array<string, string>
         */
        public readonly array $fieldIds,
    ) {
    }

    #[\Override]
    public function processObjectData(IFormDocument $document, array $data, IStorableObject $object)
    {
        if ($this->fieldIds === []) {
            return $data;
        }

        $select = \implode(', ', \array_values($this->fieldIds));

        $sql = "SELECT languageID, {$select}
                FROM   {$this->contentTableName}
                WHERE  {$object::getDatabaseTableIndexName()} = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$object->{$object::getDatabaseTableIndexName()}]);

        foreach (\array_keys($this->fieldIds) as $fieldId) {
            $data[$fieldId] = [];
        }

        while ($row = $statement->fetchArray()) {
            foreach ($this->fieldIds as $fieldId => $columnName) {
                $data[$fieldId][$row['languageID']] = $row[$columnName];
            }
        }

        foreach (\array_keys($this->fieldIds) as $fieldId) {
            if (\count($data[$fieldId]) === 1) {
                // monolingual
                $data[$fieldId] = \reset($data[$fieldId]);
            } elseif ($data[$fieldId] === []) {
                $data[$fieldId] = '';
            }
        }

        return $data;
    }
}
