<?php

namespace wcf\system\cache\eager\data;

use wcf\data\language\category\LanguageCategory;
use wcf\data\language\Language;

/**
 * Language cache data structure.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
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
        \assert($this->default > 0);
        \assert(\array_key_exists($this->default, $this->languages));
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
    public function getCategoryByID(int $languageCategoryID): ?LanguageCategory
    {
        return $this->categories[$this->categoryIDs[$languageCategoryID] ?? null] ?? null;
    }
}
