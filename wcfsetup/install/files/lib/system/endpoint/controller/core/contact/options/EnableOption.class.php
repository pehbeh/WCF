<?php

namespace wcf\system\endpoint\controller\core\contact\options;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * API endpoint for enabling a contact option.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
#[PostRequest("/core/contact/options/{id:\d+}/enable")]
final class EnableOption implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $this->assertOptionCanBeEnabled();

        $option = Helper::fetchObjectFromRequestParameter($variables['id'], ContactOption::class);

        if ($option->isDisabled) {
            (new ContactOptionAction([$option], 'toggle'))->executeAction();
        }

        return new JsonResponse([]);
    }

    private function assertOptionCanBeEnabled(): void
    {
        if (!\MODULE_CONTACT_FORM) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(["admin.contact.canManageContactForm"]);
    }
}
