<?php

namespace wcf\system\gridView\renderer;

/**
 * Formats the content of a column as a title.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class TitleColumnRenderer extends DefaultColumnRenderer
{
    #[\Override]
    public function getClasses(): string
    {
        return 'gridView__column--title';
    }
}
