<?php

namespace wcf\system\gridView\renderer;

use wcf\data\DatabaseObject;
use wcf\util\FileUtil;

/**
 * Renders a human-readable filesize.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends AbstractColumnRenderer<DatabaseObject>
 */
class FilesizeColumnRenderer extends AbstractColumnRenderer
{
    #[\Override]
    public function render(mixed $value, DatabaseObject $row): string
    {
        $filesize = \intval($value);
        if (!$filesize) {
            return '';
        }

        return FileUtil::formatFilesize($filesize);
    }

    #[\Override]
    public function getClasses(): string
    {
        return 'gridView__column--digits';
    }
}
