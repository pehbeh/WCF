<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use wcf\data\contact\option\ContactOption;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;

/**
 * Shows the contact option edit form.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.1
 */
class ContactOptionEditForm extends ContactOptionAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.contact.options';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        try {
            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $this->formObject = new ContactOption($queryParameters['id']);

        if (!$this->formObject->getObjectID()) {
            throw new IllegalLinkException();
        }
    }
}
