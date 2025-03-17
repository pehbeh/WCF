<?php

namespace wcf\action;

use wcf\system\exception\InvalidSecurityTokenException;
use wcf\system\WCF;

/**
 * Extends AbstractAction by a function to validate a given security token.
 * A missing or invalid token will be result in a throw of a IllegalLinkException.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractSecureAction extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $this->checkSecurityToken();
    }

    /**
     * Validates the security token.
     *
     * @return void
     */
    protected function checkSecurityToken()
    {
        if (!isset($_REQUEST['t']) || !WCF::getSession()->checkSecurityToken($_REQUEST['t'])) {
            throw new InvalidSecurityTokenException();
        }
    }
}
