<?php

namespace wcf\event\style;

use wcf\data\style\Style;
use wcf\event\IPsr14Event;

/**
 * Indicates that a style has been duplicated.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class StyleCopied implements IPsr14Event
{
    public function __construct(
        public readonly Style $source,
        public readonly Style $newStyle
    ) {}
}
