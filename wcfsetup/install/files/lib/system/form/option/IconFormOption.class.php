<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\IconFormField;
use wcf\system\form\option\formatter\IconFormatter;

/**
 * Implementation of a form field for icon values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class IconFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'icon';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): IconFormField
    {
        return IconFormField::create($id);
    }

    #[\Override]
    public function getFormatter(): IconFormatter
    {
        return new IconFormatter();
    }
}
