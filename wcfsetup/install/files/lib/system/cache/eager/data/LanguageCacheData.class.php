<?php

namespace wcf\system\cache\eager\data;

use wcf\data\language\category\LanguageCategory;
use wcf\data\language\Language;

/**
 * Language cache data structure.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class LanguageCacheData
{
    public function __construct(
        /** @var array<string, int> */
        public readonly array $codes,
        /** @var array<int, string> */
        public readonly array $countryCodes,
        /** @var array<int, Language> */
        public readonly array $languages,
        public readonly int $default,
        /** @var array<string, LanguageCategory> */
        public readonly array $categories,
        /** @var array<int, string> */
        public readonly array $categoryIDs,
        public readonly bool $multilingualismEnabled
    ) {
    }

    /**
     * Returns the default language.
     */
    public function getDefaultLanguage(): Language
    {
        return $this->languages[$this->default];
    }

    /**
     * Returns the language with the given language id.
     */
    public function getLanguage(int $languageID): ?Language
    {
        return $this->languages[$languageID] ?? null;
    }

    /**
     * Returns the language category with the given category name.
     */
    public function getLanguageCategory(string $categoryName): ?LanguageCategory
    {
        return $this->categories[$categoryName] ?? null;
    }

    /**
     * Returns the language category with the given category id.
     */
    public function getLanguageCategoryByID(int $languageCategoryID): ?LanguageCategory
    {
        $categoryName = $this->categoryIDs[$languageCategoryID] ?? null;
        if ($categoryName === null) {
            return null;
        }

        return $this->getLanguageCategory($categoryName);
    }

    public function languageCategoryExists(string $categoryName): bool
    {
        return \array_key_exists($categoryName, $this->categories);
    }

    /**
     * Return all content languages.
     *
     * @return array<int, Language>
     */
    public function getContentLanguages(): array
    {
        return \array_filter(
            $this->languages,
            static fn(Language $language) => \boolval($language->hasContent)
        );
    }

    /**
     * Return all content languages IDs.
     *
     * @return list<int>
     */
    public function getContentLanguageIDs(): array
    {
        return \array_keys($this->getContentLanguages());
    }

    /**
     * Return all language codes.
     *
     * @return list<string>
     */
    public function getLanguageCodes(): array
    {
        return \array_keys($this->codes);
    }

    /**
     * Return language by given language code.
     */
    public function getLanguageByCode(string $languageCode): ?Language
    {
        $languageID = $this->codes[$languageCode] ?? null;
        if ($languageID === null) {
            return null;
        }

        return $this->getLanguage($languageID);
    }
}
