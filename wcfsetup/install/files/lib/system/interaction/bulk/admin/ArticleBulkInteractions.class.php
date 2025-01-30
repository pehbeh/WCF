<?php

namespace wcf\system\interaction\bulk\admin;

use wcf\data\article\AccessibleArticleList;
use wcf\data\article\Article;
use wcf\data\article\ViewableArticle;
use wcf\event\interaction\bulk\admin\ArticleBulkInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\bulk\AbstractBulkInteractionProvider;
use wcf\system\interaction\bulk\BulkDeleteInteraction;
use wcf\system\interaction\bulk\BulkRestoreInteraction;
use wcf\system\interaction\bulk\BulkRpcInteraction;
use wcf\system\interaction\bulk\BulkTrashInteraction;
use wcf\system\interaction\InteractionConfirmationType;

/**
 * Bulk interaction provider for articles.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ArticleBulkInteractions extends AbstractBulkInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new BulkTrashInteraction('core/articles/%s/trash', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted !== 1;
            }),
            new BulkRestoreInteraction('core/articles/%s/restore', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted === 1;
            }),
            new BulkDeleteInteraction('core/articles/%s', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted === 1;
            }),
            new BulkRpcInteraction(
                'publish',
                'core/articles/%s/publish',
                'wcf.article.button.publish',
                InteractionConfirmationType::None,
                '',
                function (ViewableArticle $article): bool {
                    if (!$article->canPublish()) {
                        return false;
                    }

                    return $article->publicationStatus !== Article::PUBLISHED;
                }
            ),
            new BulkRpcInteraction(
                'unPublish',
                'core/articles/%s/unpublish',
                'wcf.article.button.unpublish',
                InteractionConfirmationType::None,
                '',
                function (ViewableArticle $article): bool {
                    if (!$article->canPublish()) {
                        return false;
                    }

                    return $article->publicationStatus === Article::PUBLISHED;
                }
            ),
        ]);

        EventHandler::getInstance()->fire(
            new ArticleBulkInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectListClassName(): string
    {
        return AccessibleArticleList::class;
    }
}
