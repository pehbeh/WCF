<?php

namespace wcf\system\endpoint\controller\core\users;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for unbanning users.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest("/core/users/{id:\d+}/unban")]
final class UnbanUser implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $user = UserProfileRuntimeCache::getInstance()->getObject($variables['id']);

        $this->assertUserCanBeUnbanned($user);

        (new UserAction([$user->getDecoratedObject()], 'unban'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertUserCanBeUnbanned(?UserProfile $user): void
    {
        if ($user === null) {
            throw new IllegalLinkException();
        }

        if (!$user->canBan()) {
            throw new PermissionDeniedException();
        }

        if (!$user->banned) {
            throw new PermissionDeniedException();
        }
    }
}
