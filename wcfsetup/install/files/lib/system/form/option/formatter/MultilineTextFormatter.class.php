<?php

namespace wcf\system\form\option\formatter;

use wcf\util\StringUtil;

/**
 * Formatter for multiline text values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class MultilineTextFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configurationData): string
    {
        return \nl2br(StringUtil::encodeHTML($value), false);
    }
}
