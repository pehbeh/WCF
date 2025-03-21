<?php

namespace wcf\system\listView\user;

use wcf\data\article\category\ArticleCategory;
use wcf\data\article\CategoryArticleList;
use wcf\system\WCF;

class CategoryArticleListView extends ArticleListView
{
    public function __construct(public readonly ArticleCategory $category)
    {
        parent::__construct();
    }

    #[\Override]
    protected function createObjectList(): CategoryArticleList
    {
        $list = new CategoryArticleList($this->category->categoryID, true);
        if ($list->sqlSelects !== '') {
            $list->sqlSelects .= ',';
        }
        $list->sqlSelects .= "(
            SELECT  title
            FROM    wcf1_article_content
            WHERE   articleID = article.articleID
                AND (
                        languageID IS NULL
                     OR languageID = " . WCF::getLanguage()->languageID . "
                    )
            LIMIT   1
        ) AS title";

        return $list;
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return parent::isAccessible() && $this->category->isAccessible();
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('wcf', 'articleListItems', ['view' => $this]);
    }

    #[\Override]
    protected function getAccessibleLabelGroups(): array
    {
        return $this->category->getLabelGroups('canViewLabel');
    }
}
