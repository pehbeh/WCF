<?php

namespace wcf\system\form\builder\data\processor;

use wcf\data\IStorableObject;
use wcf\data\language\Language;
use wcf\system\form\builder\IFormDocument;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Field data processor that processes the data for `MultilingualFormContainer`. All content is stored in the `content`
 * sub-array. The languageID is used as an index for the arrays, in which all fields are stored with their
 * corresponding value. The value `0` corresponds to monolingual content and should then be stored in the database as `null`.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class MultilingualFormDataProcessor extends AbstractFormDataProcessor
{
    public const ARRAY_INDEX = 'content';

    public function __construct(
        public readonly string $contentTableName,
        /**
         * Mapping of field id to database column name
         *
         * @var array<string, string>
         */
        public readonly array $fieldIds
    ) {
    }

    #[\Override]
    public function processObjectData(IFormDocument $document, array $data, IStorableObject $object)
    {
        if ($this->fieldIds === []) {
            return $data;
        }

        $indexName = $object::getDatabaseTableIndexName();
        $select = \implode(', ', \array_values($this->fieldIds));

        $sql = "SELECT languageID, {$select}
                FROM   {$this->contentTableName}
                WHERE  {$indexName} = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$object->{$indexName}]);

        $contents = [];
        while ($row = $statement->fetchArray()) {
            $languageCode = $row['languageID'] ? LanguageFactory::getInstance()->getLanguage($row['languageID'])->languageCode : "";

            $content = [];
            foreach ($this->fieldIds as $fieldId => $columnName) {
                $content[$fieldId] = $row[$columnName];
            }

            $contents[$languageCode] = $content;
        }

        if (\count($contents) > 1) {
            $data['isMultilingual'] = 1;

            foreach ($contents as $languageCode => $content) {
                foreach (\array_keys($this->fieldIds) as $fieldId) {
                    $data["{$fieldId}_{$languageCode}"] = $content[$fieldId];
                }
            }
        } else {
            $data['isMultilingual'] = 0;

            if ($contents !== []) {
                $content = \reset($contents);
                foreach (\array_keys($this->fieldIds) as $fieldId) {
                    $data[$fieldId] = $content[$fieldId];
                }
            }
        }

        return $data;
    }

    #[\Override]
    public function processFormData(IFormDocument $document, array $parameters)
    {
        $languages = LanguageFactory::getInstance()->getLanguages();
        $parameters[self::ARRAY_INDEX] = [];

        $isMultilingual = $parameters['data']['isMultilingual'] ?? false;
        $isMultilingual = $isMultilingual && \count($languages) > 1;

        $parameters['data']['isMultilingual'] = $isMultilingual ? 1 : 0;

        if ($isMultilingual) {
            $parameters = $this->removeMonolingualValues($parameters, $languages);
            $parameters = $this->processMultilingualValues($parameters, $languages);
        } else {
            $parameters = $this->removeMultilingualValues($parameters, $languages);
            $parameters = $this->processMonolingualValues($parameters);
        }

        return $parameters;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param Language[] $languages
     *
     * @return array<string, mixed>
     */
    private function removeMonolingualValues(array $parameters, array $languages): array
    {
        return \array_filter($parameters, function ($key) use ($languages) {
            foreach ($this->fieldIds as $fieldId) {
                if (!\str_starts_with($key, "{$fieldId}_")) {
                    continue;
                }

                foreach ($languages as $language) {
                    if (\str_starts_with($key, "{$fieldId}_{$language->languageCode}")) {
                        return true;
                    }
                }

                return false;
            }

            return true;
        }, \ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param Language[] $languages
     *
     * @return array<string, mixed>
     */
    private function processMultilingualValues(array $parameters, array $languages): array
    {
        foreach ($languages as $language) {
            foreach ($this->fieldIds as $fieldId) {
                $languageFieldId = "{$fieldId}_{$language->languageCode}";

                if (isset($parameters["data"][$languageFieldId])) {
                    $parameters[self::ARRAY_INDEX][$language->languageID][$fieldId] = $parameters["data"][$languageFieldId];
                    unset($parameters["data"][$languageFieldId]);
                }

                foreach ($parameters as $key => $value) {
                    if (\str_starts_with($key, "{$languageFieldId}_")) {
                        $index = \substr($key, \strlen($languageFieldId) + 1);

                        $parameters[self::ARRAY_INDEX][$language->languageID][$index] = $value;
                        unset($parameters[$key]);
                    }
                }
            }
        }

        return $parameters;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param Language[] $languages
     *
     * @return array<string, mixed>
     */
    private function removeMultilingualValues(array $parameters, array $languages): array
    {
        return \array_filter($parameters, function ($key) use ($languages) {
            foreach ($this->fieldIds as $fieldId) {
                if (!\str_starts_with($key, "{$fieldId}_")) {
                    continue;
                }

                foreach ($languages as $language) {
                    if (\str_starts_with($key, "{$fieldId}_{$language->languageCode}")) {
                        return false;
                    }
                }
            }

            return true;
        }, \ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    private function processMonolingualValues(array $parameters): array
    {
        $defaultLanguageID = LanguageFactory::getInstance()->getDefaultLanguageID();

        $parameters[self::ARRAY_INDEX][$defaultLanguageID] = [];
        foreach ($this->fieldIds as $fieldId) {
            if (isset($parameters["data"][$fieldId])) {
                $parameters[self::ARRAY_INDEX][$defaultLanguageID][$fieldId] = $parameters["data"][$fieldId];
                unset($parameters["data"][$fieldId]);
            }

            foreach ($parameters as $key => $value) {
                if (\str_starts_with($key, "{$fieldId}_")) {
                    $index = \substr($key, \strlen($fieldId) + 1);

                    $parameters[self::ARRAY_INDEX][$defaultLanguageID][$index] = $value;
                    unset($parameters[$key]);
                }
            }
        }

        return $parameters;
    }
}
