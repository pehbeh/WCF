<?php

namespace wcf\system\cache\persistent;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\data\DatabaseObject;
use wcf\data\language\category\LanguageCategoryList;
use wcf\data\language\LanguageList;
use wcf\system\cache\persistent\data\LanguageCacheData;

/**
 * Persistent cache implementation for languages.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractPersistentCache<LanguageCacheData>
 */
final class LanguageCache extends AbstractPersistentCache
{
    #[\Override]
    public function __invoke(ItemInterface $item): LanguageCacheData
    {
        $item->tag('language');

        $languageList = new LanguageList();
        $languageList->getConditionBuilder()->add('language.isDisabled = ?', [0]);
        $languageList->readObjects();

        $languages = $languageList->getObjects();
        $default = 0;
        $multilingualismEnabled = false;
        $codes = [];
        $countryCodes = [];

        foreach ($languageList->getObjects() as $language) {
            if ($language->isDefault) {
                $default = $language->languageID;
            }

            if ($language->hasContent) {
                $multilingualismEnabled = true;
            }

            $codes[$language->languageCode] = $language->languageID;
            $countryCodes[$language->languageID] = $language->countryCode;
        }

        DatabaseObject::sort($languages, 'languageName');

        $languageCategoryList = new LanguageCategoryList();
        $languageCategoryList->readObjects();

        $categories = [];
        $categoryIDs = [];
        foreach ($languageCategoryList->getObjects() as $languageCategory) {
            $categories[$languageCategory->languageCategory] = $languageCategory;
            $categoryIDs[$languageCategory->languageCategoryID] = $languageCategory->languageCategory;
        }

        if (!isset($languages[$default])) {
            throw new \RuntimeException('No default language defined!');
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
