<?php

namespace wcf\system\endpoint\controller\core\listViews;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\listView\AbstractListView;

/**
 * Retrieves a single item for a list view.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[GetRequest('/core/list-views/item')]
final class GetItem implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, GetItemParameters::class);

        if (!\is_subclass_of($parameters->listView, AbstractListView::class)) {
            throw new UserInputException('listView', 'invalid');
        }

        $view = new $parameters->listView(...$parameters->listViewParameters);
        // @phpstan-ignore function.alreadyNarrowedType, instanceof.alwaysTrue
        \assert($view instanceof AbstractListView);

        if (!$view->isAccessible()) {
            throw new PermissionDeniedException();
        }

        $view->setObjectIDFilter($parameters->objectID);

        return new JsonResponse([
            'template' => $view->renderItems(),
        ]);
    }
}

/** @internal */
final class GetItemParameters
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $listView,
        public readonly string|int $objectID,
        /** @var array<string, string|string[]> */
        public readonly array $listViewParameters,
    ) {}
}
