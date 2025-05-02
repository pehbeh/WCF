<?php

namespace wcf\system\form\option\formatter;

use wcf\system\html\output\HtmlOutputProcessor;

/**
 * Formatter for wysiwyg form options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class WysiwygFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configurationData): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->process($value, 'com.woltlab.wcf.genericFormOption', 0, true, $languageID);

        return $processor->getHtml();
    }
}
