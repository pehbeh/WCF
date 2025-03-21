<?php

namespace wcf\page;

use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\listView\AbstractListView;
use wcf\system\WCF;

/**
 * Abstract implementation of a page that is rendering a list view.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractListViewPage extends AbstractPage
{
    protected AbstractListView $listView;
    protected int $pageNo = 1;
    protected string $sortField = '';
    protected string $sortOrder = '';
    protected array $filters = [];

    #[\Override]
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['pageNo'])) {
            $this->pageNo = \intval($_REQUEST['pageNo']);
        }
        if (isset($_REQUEST['sortField'])) {
            $this->sortField = $_REQUEST['sortField'];
        }
        if (isset($_REQUEST['sortOrder']) && ($_REQUEST['sortOrder'] === 'ASC' || $_REQUEST['sortOrder'] === 'DESC')) {
            $this->sortOrder = $_REQUEST['sortOrder'];
        }
        if (isset($_REQUEST['filters']) && \is_array($_REQUEST['filters'])) {
            $this->filters = $_REQUEST['filters'];
        }
    }

    #[\Override]
    public function readData()
    {
        parent::readData();

        $this->initListView();
    }

    #[\Override]
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'listView' => $this->listView,
        ]);
    }

    protected function initListView(): void
    {
        $this->listView = $this->createListView();
        if (!$this->listView->isAccessible()) {
            throw new PermissionDeniedException();
        }

        if ($this->sortField) {
            $this->listView->setSortField($this->sortField);
        }
        if ($this->sortOrder) {
            $this->listView->setSortOrder($this->sortOrder);
        }
        if ($this->filters !== []) {
            $this->listView->setActiveFilters($this->filters);
        }
        if ($this->pageNo != 1) {
            $this->listView->setPageNo($this->pageNo);
        }
        $this->listView->setBaseUrl(LinkHandler::getInstance()->getControllerLink(static::class));
    }

    /**
     * Returns the list view instance for the rendering of this page.
     */
    protected abstract function createListView(): AbstractListView;
}
