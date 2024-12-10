<?php

namespace wcf\system\gridView\renderer;

use wcf\util\StringUtil;

/**
 * Truncates the content of a column to a length of 80 characters (default value).
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class TruncatedTextColumnRenderer extends DefaultColumnRenderer
{
    public function __construct(
        private readonly int $length = 80,
        private readonly string $etc = "\u{2026}"
    ) {}

    #[\Override]
    public function render(mixed $value, mixed $context = null): string
    {
        if (!$value) {
            return '';
        }

        $renderedValue = StringUtil::encodeHTML(StringUtil::truncate($value, $this->length, $this->etc));

        if (\mb_strlen($value) > $this->length) {
            $renderedValue = '<span title="' . StringUtil::encodeHTML($value) . '">' . $renderedValue . '</span>';
        }

        return $renderedValue;
    }
}
