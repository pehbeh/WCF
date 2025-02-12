<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\gridView\AbstractGridView;
use wcf\system\WCF;

/**
 * Handels the dialog for filtering the grid view to sort this later.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class GridViewSortAction implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    gridView: string,
                    gridViewParameters: string[]
                }
                EOT
        );

        if (!\is_subclass_of($parameters['gridView'], AbstractGridView::class)) {
            throw new UserInputException('gridView', 'invalid');
        }

        $view = new $parameters['gridView'](...$parameters['gridViewParameters']);
        \assert($view instanceof AbstractGridView);

        if (!$view->isAccessible()) {
            throw new PermissionDeniedException();
        }

        if (!$view->isFilterable()) {
            throw new IllegalLinkException();
        }

        if ($view->getSortButton() === null) {
            throw new IllegalLinkException();
        }
        if ($view->getSortButton()->filterColumns === []) {
            throw new IllegalLinkException();
        }

        $form = $this->getForm($view);

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            $data = $form->getData()['data'];

            return new JsonResponse([
                'result' => $data
            ]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }

    private function getForm(AbstractGridView $gridView): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('wcf.global.filter')
        );

        $sortableButton = $gridView->getSortButton();
        $columns = \array_filter($gridView->getFilterableColumns(), static function ($column) use ($sortableButton) {
            return \in_array($column->getID(), $sortableButton->filterColumns);
        });

        foreach ($columns as $column) {
            $form->appendChild(
                $column->getFilterFormField()
                    ->required()
            );
        }

        $form->markRequiredFields(false);
        $form->build();

        return $form;
    }
}
