<?php

namespace wcf\page;

use wcf\system\listView\user\ArticleListView;

/**
 * Shows a list of articles in watched categories.
 *
 * @author      Joshua Ruesweg
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 */
class WatchedArticleListPage extends ArticleListPage
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $templateName = 'articleList';

    #[\Override]
    protected function createListView(): ArticleListView
    {
        $listView = parent::createListView();
        $listView->setActiveFilters(['watched' => 1]);

        return $listView;
    }
}
