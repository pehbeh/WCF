<?php

namespace wcf\system\bbcode;

use wcf\data\article\ViewableArticle;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\view\ContentNotVisibleView;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Parses the [wsa] bbcode tag.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 */
final class WoltLabSuiteArticleBBCode extends AbstractBBCode
{
    /**
     * @inheritDoc
     */
    public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser): string
    {
        $articleID = 0;
        if (isset($openingTag['attributes'][0])) {
            $articleID = \intval($openingTag['attributes'][0]);
        }
        if (!$articleID) {
            return '';
        }

        $article = $this->getArticle($articleID);
        if ($article === null) {
            return ContentNotVisibleView::forNotAvailable();
        }

        if (!$article->canRead()) {
            return ContentNotVisibleView::forNoPermission();
        } elseif ($parser->getOutputType() == 'text/html') {
            return WCF::getTPL()->render('wcf', 'shared_bbcode_wsa', [
                'article' => $article,
                'articleID' => $article->articleID,
                'titleHash' => \substr(StringUtil::getRandomID(), 0, 8),
            ]);
        }

        return StringUtil::getAnchorTag($article->getLink(), $article->getTitle());
    }

    private function getArticle(int $articleID): ?ViewableArticle
    {
        $article = MessageEmbeddedObjectManager::getInstance()->getObject(
            'com.woltlab.wcf.article',
            $articleID
        );
        \assert($article === null || $article instanceof ViewableArticle);

        return $article;
    }
}
