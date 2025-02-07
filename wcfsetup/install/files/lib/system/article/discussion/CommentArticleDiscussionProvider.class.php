<?php

namespace wcf\system\article\discussion;

use wcf\data\article\Article;
use wcf\system\comment\CommentHandler;
use wcf\system\WCF;

/**
 * The built-in discussion provider using the native comment system.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 */
class CommentArticleDiscussionProvider extends AbstractArticleDiscussionProvider
{
    /**
     * @inheritDoc
     */
    public function getDiscussionCount()
    {
        return $this->articleContent ? $this->articleContent->comments : $this->article->getArticleContent()->comments;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase()
    {
        return WCF::getLanguage()->getDynamicVariable('wcf.article.articleComments', [
            'articleContent' => $this->articleContent ?: $this->article->getArticleContent(),
            'article' => $this->article, // kept line for backward compatibility in 3rd party translations
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink()
    {
        return $this->articleContent->getLink() . '#comments';
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions()
    {
        $commentCanAdd = WCF::getSession()->getPermission('user.article.canAddComment');
        $commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('com.woltlab.wcf.articleComment');
        $commentManager = CommentHandler::getInstance()->getObjectType($commentObjectTypeID)->getProcessor();
        $commentList = CommentHandler::getInstance()->getCommentList(
            $commentManager,
            $commentObjectTypeID,
            $this->articleContent->articleContentID
        );

        return WCF::getTPL()->render('wcf', 'articleComments', [
            'commentCanAdd' => $commentCanAdd,
            'commentList' => $commentList,
            'commentObjectTypeID' => $commentObjectTypeID,
            'lastCommentTime' => $commentList->getMinCommentTime(),
            'likeData' => (MODULE_LIKE) ? $commentList->getLikeData() : [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Article $article)
    {
        return !!$article->enableComments;
    }
}
