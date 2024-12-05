<?php

namespace wcf\system\file\processor;

/**
 * @author Marcel Werk
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
interface IImageDataProvider
{
    public function getImageData(?int $minWidth = null, ?int $minHeight = null): ?ImageData;
}
