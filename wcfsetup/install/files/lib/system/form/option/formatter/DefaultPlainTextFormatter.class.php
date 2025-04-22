<?php

namespace wcf\system\form\option\formatter;

/**
 * Default plain text formatter for form option values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class DefaultPlainTextFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configurationData): string
    {
        return $value;
    }
}
