<?php

namespace wcf\system\interaction\user;

use wcf\data\article\Article;
use wcf\data\article\ViewableArticle;
use wcf\event\interaction\admin\ArticleInteractionCollecting;
use wcf\form\ArticleEditForm;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\LinkableObjectInteraction;
use wcf\system\interaction\RestoreInteraction;
use wcf\system\interaction\RpcInteraction;
use wcf\system\interaction\TrashInteraction;

/**
 * Interaction provider for articles.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ArticleInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new TrashInteraction('core/articles/%s/trash', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted !== 1;
            }),
            new RestoreInteraction('core/articles/%s/restore', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted === 1;
            }),
            new DeleteInteraction('core/articles/%s', function (ViewableArticle $article): bool {
                if (!$article->canDelete()) {
                    return false;
                }

                return $article->isDeleted === 1;
            }),
            new RpcInteraction(
                'publish',
                'core/articles/%s/publish',
                'wcf.article.button.publish',
                isAvailableCallback: static function (ViewableArticle $article): bool {
                    if (!$article->canPublish()) {
                        return false;
                    }

                    return $article->publicationStatus !== Article::PUBLISHED;
                }
            ),
            new RpcInteraction(
                'unpublish',
                'core/articles/%s/unpublish',
                'wcf.article.button.unpublish',
                isAvailableCallback: static function (ViewableArticle $article): bool {
                    if (!$article->canPublish()) {
                        return false;
                    }

                    return $article->publicationStatus === Article::PUBLISHED;
                }
            ),
            new Divider(),
            new EditInteraction(ArticleEditForm::class, function (ViewableArticle $article): bool {
                return $article->canEdit();
            })
        ]);

        /*EventHandler::getInstance()->fire(
            new ArticleInteractionCollecting($this)
        );*/
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Article::class;
    }
}
