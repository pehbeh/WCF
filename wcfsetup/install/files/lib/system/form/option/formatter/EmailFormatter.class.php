<?php

namespace wcf\system\form\option\formatter;

use wcf\util\StringUtil;

/**
 * Formatter for email values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class EmailFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configuration): string
    {
        $encodedValue = StringUtil::encodeHTML($value);

        return '<a href="mailto:' . $encodedValue . '">' . $encodedValue . '</a>';
    }
}
