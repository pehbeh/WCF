<?php

namespace wcf\system\form\option;

use wcf\system\form\builder\field\RatingFormField;
use wcf\system\form\option\formatter\RatingFormatter;

/**
 * Implementation of a form field for rating values.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class RatingFormOption extends AbstractFormOption
{
    #[\Override]
    public function getId(): string
    {
        return 'rating';
    }

    #[\Override]
    public function getFormField(string $id, array $configurationData = []): RatingFormField
    {
        return RatingFormField::create($id);
    }

    #[\Override]
    public function getFormatter(): RatingFormatter
    {
        return new RatingFormatter();
    }
}
