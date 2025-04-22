<?php

namespace wcf\system\form\option;

/**
 * Provides abstract implementations for form option types.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractFormOption implements IFormOption
{
    #[\Override]
    public function getConfigurationFormFields(): array
    {
        return [];
    }
}
