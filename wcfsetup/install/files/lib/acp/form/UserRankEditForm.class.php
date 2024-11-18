<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use wcf\data\user\rank\UserRank;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;

/**
 * Shows the user rank edit form.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserRankEditForm extends UserRankAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.rank.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
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
            $this->formObject = new UserRank($queryParameters['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }
}
