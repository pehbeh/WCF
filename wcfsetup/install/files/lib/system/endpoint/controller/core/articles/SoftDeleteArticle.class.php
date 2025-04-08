<?php

namespace wcf\system\endpoint\controller\core\articles;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\article\Article;
use wcf\data\article\ArticleAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for moving an article to the trash.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/articles/{id:\d+}/soft-delete')]
final class SoftDeleteArticle implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        if (!MODULE_ARTICLE) {
            throw new IllegalLinkException();
        }

        $article = Helper::fetchObjectFromRequestParameter($variables['id'], Article::class);
        if (!$article->canDelete()) {
            throw new PermissionDeniedException();
        }
        if ($article->isDeleted) {
            throw new IllegalLinkException();
        }

        $action = new ArticleAction([$article], 'trash');
        $action->executeAction();

        return new JsonResponse([]);
    }
}
