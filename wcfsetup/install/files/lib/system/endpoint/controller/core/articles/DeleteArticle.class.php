<?php

namespace wcf\system\endpoint\controller\core\articles;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\article\Article;
use wcf\data\article\ArticleAction;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for deleting an article.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[DeleteRequest('/core/articles/{id:\d+}')]
final class DeleteArticle implements IController
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
        if (!$article->isDeleted) {
            throw new IllegalLinkException();
        }

        $action = new ArticleAction([$article], 'delete');
        $action->executeAction();

        return new JsonResponse([]);
    }
}
