<?php

namespace wcf\system\form\builder\field;

/**
 * Implementation of a form field for disabling an object.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 */
class IsDisabledFormField extends BooleanFormField
{
    use TDefaultIdFormField;

    /**
     * @inheritDoc
     * @return string
     */
    protected static function getDefaultId()
    {
        return 'isDisabled';
    }
}
