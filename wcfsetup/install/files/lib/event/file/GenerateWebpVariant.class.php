<?php

namespace wcf\event\file;

use wcf\data\file\File;
use wcf\event\IPsr14Event;

/**
 * Requests the generation of a WebP variant of the provided file.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 */
final class GenerateWebpVariant implements IPsr14Event
{
    /**
     * The absolute path to the generated WebP image.
     */
    public ?string $filename = null;

    public function __construct(
        public readonly File $file
    ) {}
}
