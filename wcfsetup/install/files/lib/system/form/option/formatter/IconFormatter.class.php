<?php

namespace wcf\system\form\option\formatter;

use wcf\system\style\FontAwesomeIcon;

/**
 * Formatter for icon values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class IconFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configuration): string
    {
        return FontAwesomeIcon::fromString($value)->toHtml();
    }
}
