<?php

namespace wcf\system\gridView\renderer;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Formats the content of a column as a phrase.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class PhraseColumnRenderer extends DefaultColumnRenderer
{
    #[\Override]
    public function render(mixed $value, mixed $context = null): string
    {
        return WCF::getLanguage()->get($value);
    }
}
