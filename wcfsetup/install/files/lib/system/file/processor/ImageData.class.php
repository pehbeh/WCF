<?php

namespace wcf\system\file\processor;

use wcf\util\StringUtil;

/**
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class ImageData
{
    public function __construct(
        public readonly string $src,
        public readonly int $width,
        public readonly int $height
    ) {}

    public function toHtml(): string
    {
        return \sprintf(
            '<img src="%s" alt="" width="%d" height="%d" loading="lazy">',
            StringUtil::encodeHTML($this->src),
            $this->width,
            $this->height
        );
    }
}
