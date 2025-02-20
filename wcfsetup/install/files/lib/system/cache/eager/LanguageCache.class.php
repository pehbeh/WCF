<?php

namespace wcf\system\cache\eager;

use wcf\data\DatabaseObject;
use wcf\data\language\category\LanguageCategoryList;
use wcf\data\language\LanguageList;
use wcf\system\cache\eager\data\LanguageCacheData;

/**
 * Eager cache implementation for languages.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends AbstractEagerCache<LanguageCacheData>
 */
final class LanguageCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): LanguageCacheData
    {
        $languageList = new LanguageList();
        $languageList->getConditionBuilder()->add('language.isDisabled = ?', [0]);
        $languageList->readObjects();

        $languages = $languageList->getObjects();
        $default = 0;
        $multilingualismEnabled = false;
        $codes = [];
        $countryCodes = [];

        foreach ($languageList->getObjects() as $language) {
            // default language
            if ($language->isDefault) {
                $default = $language->languageID;
            }

            // multilingualism
            if ($language->hasContent) {
                $multilingualismEnabled = true;
            }

            // language code to language id
            $codes[$language->languageCode] = $language->languageID;

            // country code to language id
            $countryCodes[$language->languageID] = $language->countryCode;
        }

        DatabaseObject::sort($languages, 'languageName');

        // get language categories
        $languageCategoryList = new LanguageCategoryList();
        $languageCategoryList->readObjects();

        $categories = [];
        $categoryIDs = [];
        foreach ($languageCategoryList->getObjects() as $languageCategory) {
            $categories[$languageCategory->languageCategory] = $languageCategory;
            $categoryIDs[$languageCategory->languageCategoryID] = $languageCategory->languageCategory;
        }

        return new LanguageCacheData(
            $codes,
            $countryCodes,
            $languages,
            $default,
            $categories,
            $categoryIDs,
            $multilingualismEnabled
        );
    }
}
