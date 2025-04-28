<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\option\formatter\EmailFormatter;

/**
 * Implementation of a form field for email addresses.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class EmailFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'email';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): AbstractFormField
    {
        return EmailFormField::create($id);
    }

    #[\Override]
    public function getFormatter(): EmailFormatter
    {
        return new EmailFormatter();
    }

    #[\Override]
    public function getPlainTextFormatter(): EmailFormatter
    {
        return $this->getFormatter();
    }
}
