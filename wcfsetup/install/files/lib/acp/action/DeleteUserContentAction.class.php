<?php

namespace wcf\acp\action;

use CuyZ\Valinor\Mapper\MappingError;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\http\Helper;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Form dialog to delete user content.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class DeleteUserContentAction implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $parameters = Helper::mapQueryParameters(
                $request->getQueryParams(),
                <<<'EOT'
                array {
                    objectIDs: positive-int | array<positive-int>
                }
                EOT,
            );
        } catch (MappingError) {
            throw new IllegalLinkException();
        }

        $userIDs = \is_array($parameters['objectIDs']) ? $parameters['objectIDs'] : [$parameters['objectIDs']];
        $users = UserProfileRuntimeCache::getInstance()->getObjects($userIDs);
        if ($users === []) {
            throw new IllegalLinkException();
        }
        foreach ($users as $user) {
            if (!$user->canDelete()) {
                throw new PermissionDeniedException();
            }
        }

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            // TODO delete user content

            return new JsonResponse([]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }

    private function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            DeleteUserContentAction::class,
            WCF::getLanguage()->get('wcf.acp.content.removeContent')
        );
        $form->appendChildren([
            FormContainer::create('section')
                ->appendChildren([
                    // TODO
                ])
        ]);

        $form->build();

        return $form;
    }
}
