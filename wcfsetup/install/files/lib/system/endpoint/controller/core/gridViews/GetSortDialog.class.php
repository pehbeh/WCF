<?php

namespace wcf\system\endpoint\controller\core\gridViews;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\gridView\AbstractGridView;
use wcf\system\WCF;

/**
 * Retrieves the rows to sort a grid view.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[GetRequest('/core/grid-views/sort')]
final class GetSortDialog implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, GetSortDialogParameters::class);

        if (!\is_subclass_of($parameters->gridView, AbstractGridView::class)) {
            throw new UserInputException('gridView', 'invalid');
        }

        $view = new $parameters->gridView(...$parameters->gridViewParameters);
        \assert($view instanceof AbstractGridView);

        if (!$view->isAccessible()) {
            throw new PermissionDeniedException();
        }

        $sortButton = $view->getSortButton();
        if ($sortButton === null) {
            throw new IllegalLinkException();
        }

        $view->setSortField($sortButton->sortOrderColumnId);
        $view->setSortOrder("ASC");
        $view->setRowsPerPage(0);

        if ($sortButton->filterColumns !== []) {
            $view->setActiveFilters(
                \array_intersect_key($parameters->filters, \array_flip($sortButton->filterColumns))
            );
        }

        if ($view->countRows() < 1) {
            return new JsonResponse([]);
        }

        $titleColumn = \array_filter($view->getColumns(), fn($column) => $column->isTitleColumn());
        $titleColumn = \reset($titleColumn);
        if ($sortButton->titleColumnRenderer !== null) {
            $titleColumn->renderer($sortButton->titleColumnRenderer);
        }

        foreach ($view->getVisibleColumns() as $column) {
            if ($column->isTitleColumn() || $column->getID() === $sortButton->sortOrderColumnId) {
                continue;
            }

            $column->hidden();
        }

        return new JsonResponse([
            'template' => WCF::getTPL()->render("wcf", "shared_gridViewSort", [
                'view' => $view,
            ])
        ]);
    }
}

/** @internal */
final class GetSortDialogParameters
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $gridView,
        /** @var string[] */
        public readonly array $gridViewParameters,
        /** @var string[] */
        public readonly array $filters,
    ) {
    }
}
