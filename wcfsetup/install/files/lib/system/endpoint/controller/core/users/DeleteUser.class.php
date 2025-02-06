<?php

namespace wcf\system\endpoint\controller\core\users;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for deleting users.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[DeleteRequest("/core/users/{id:\d+}")]
class DeleteUser implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $user = UserProfileRuntimeCache::getInstance()->getObject($variables['id']);

        $this->assertUserCanBeDeleted($user);

        (new UserAction([$user->getDecoratedObject()], 'delete'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertUserCanBeDeleted(?UserProfile $user): void
    {
        if ($user === null) {
            throw new IllegalLinkException();
        }

        if (!$user->canDelete()) {
            throw new PermissionDeniedException();
        }
    }
}
