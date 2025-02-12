<?php

namespace wcf\system\gridView;

use wcf\action\GridViewSortAction;
use wcf\system\gridView\renderer\IColumnRenderer;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 *
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class GridViewSortButton
{
    public function __construct(
        public readonly string $sortOrderColumnId,
        private readonly string $saveEndpoint,
        public readonly ?IColumnRenderer $titleColumnRenderer = null,
        /** @var string[] */
        public readonly array $filterColumns = []
    ) {
    }

    /**
     * Renders the sort button.
     */
    public function renderButton(AbstractGridView $view): string
    {
        $title = WCF::getLanguage()->get("wcf.global.sort");
        $saveEndpoint = StringUtil::encodeHTML($this->saveEndpoint);
        if ($this->filterColumns !== []) {
            $endpoint = StringUtil::encodeHTML(
                LinkHandler::getInstance()->getControllerLink(GridViewSortAction::class, [
                    'gridView' => $view->getClassName(),
                    'gridViewParameters' => $view->getParameters()
                ])
            );
        } else {
            $endpoint = "";
        }

        return <<<HTML
            <button type="button" class="gridView__sortButton button small" id="{$view->getID()}_sortButton"
                    data-endpoint="{$endpoint}"
                    data-save-endpoint="{$saveEndpoint}">
                <fa-icon name="sort"></fa-icon>
                {$title}
            </button>
        HTML;
    }
}
