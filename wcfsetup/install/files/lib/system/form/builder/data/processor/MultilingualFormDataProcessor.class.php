<?php

namespace wcf\system\form\builder\data\processor;

use wcf\data\language\Language;
use wcf\system\form\builder\IFormDocument;
use wcf\system\language\LanguageFactory;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
abstract class MultilingualFormDataProcessor extends AbstractFormDataProcessor
{
    public function __construct(
        public readonly string $arrayIndex,
        /** @var string[] */
        public readonly array $fieldIds
    ) {
    }

    #[\Override]
    public function processFormData(IFormDocument $document, array $parameters)
    {
        $languages = LanguageFactory::getInstance()->getLanguages();
        $parameters[$this->arrayIndex] = [];

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
                    $parameters[$this->arrayIndex][$language->languageID][$fieldId] = $parameters["data"][$languageFieldId];
                    unset($parameters["data"][$languageFieldId]);
                }

                foreach ($parameters as $key => $value) {
                    if (\str_starts_with($key, "{$languageFieldId}_")) {
                        $index = \substr($key, \strlen($languageFieldId) + 1);

                        $parameters[$this->arrayIndex][$language->languageID][$index] = $value;
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
        $parameters[$this->arrayIndex][0] = [];
        foreach ($this->fieldIds as $fieldId) {
            if (isset($parameters["data"][$fieldId])) {
                $parameters[$this->arrayIndex][0][$fieldId] = $parameters["data"][$fieldId];
                unset($parameters["data"][$fieldId]);
            }

            foreach ($parameters as $key => $value) {
                if (\str_starts_with($key, "{$fieldId}_")) {
                    $index = \substr($key, \strlen($fieldId) + 1);

                    $parameters[$this->arrayIndex][0][$index] = $value;
                    unset($parameters[$key]);
                }
            }
        }

        return $parameters;
    }
}
