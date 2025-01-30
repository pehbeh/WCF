<?php

namespace wcf\system\interaction\bulk\admin;

use wcf\data\article\AccessibleArticleList;
use wcf\data\article\ViewableArticle;
use wcf\event\interaction\bulk\admin\ArticleBulkInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\bulk\AbstractBulkInteractionProvider;
use wcf\system\interaction\bulk\BulkDeleteInteraction;
use wcf\system\interaction\bulk\BulkRestoreInteraction;
use wcf\system\interaction\bulk\BulkTrashInteraction;

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
                return $article->isDeleted !== 1;
            }),
            new BulkRestoreInteraction('core/articles/%s/restore', function (ViewableArticle $article): bool {
                return $article->isDeleted === 1;
            }),
            new BulkDeleteInteraction('core/articles/%s', function (ViewableArticle $article): bool {
                return $article->isDeleted === 1;
            }),
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
