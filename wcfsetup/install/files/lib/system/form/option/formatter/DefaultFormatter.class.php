<?php

namespace wcf\system\form\option\formatter;

use wcf\util\StringUtil;

/**
 * Default formatter for form option values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class DefaultFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configurationData): string
    {
        return StringUtil::encodeHTML($value);
    }
}
