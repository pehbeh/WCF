<?php

namespace wcf\system\form\option;

use wcf\system\form\option\formatter\DefaultFormatter;
use wcf\system\form\option\formatter\DefaultPlainTextFormatter;
use wcf\system\form\option\formatter\IFormOptionFormatter;

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

    #[\Override]
    public function getFormatter(): IFormOptionFormatter
    {
        return new DefaultFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): IFormOptionFormatter
    {
        return new DefaultPlainTextFormatter();
    }
}
