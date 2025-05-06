<?php

namespace wcf\system\form\option\formatter;

use wcf\system\html\output\HtmlOutputProcessor;

/**
 * Plain text version of the formatter for wysiwyg form options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class WysiwygPlainTextFormatter implements IFormOptionFormatter
{
    #[\Override]
    public function format(string $value, int $languageID, array $configuration): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/plain');
        $processor->process($value, 'com.woltlab.wcf.genericFormOption', 0, true, $languageID);

        return $processor->getHtml();
    }
}
