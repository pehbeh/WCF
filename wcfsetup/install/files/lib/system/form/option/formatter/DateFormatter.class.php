<?php

namespace wcf\system\form\option\formatter;

use wcf\system\language\LanguageFactory;

/**
 * Formatter for date values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class DateFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configurationData): string
    {
        $dateTime = new \DateTimeImmutable($value);
        $locale = LanguageFactory::getInstance()->getLanguage($languageID)->getLocale();

        return \IntlDateFormatter::formatObject(
            $dateTime,
            [
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::NONE,
            ],
            $locale
        );
    }
}
