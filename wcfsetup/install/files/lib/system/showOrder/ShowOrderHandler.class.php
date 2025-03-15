<?php

namespace wcf\system\showOrder;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use wcf\http\Helper;

/**
 * Handles the change of the show order of elements.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ShowOrderHandler
{
    /**
     * @var ShowOrderItem[]
     */
    private readonly array $items;

    /**
     * @param ShowOrderItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = \array_values($items);
    }

    public function toJsonResponse(): JsonResponse
    {
        return new JsonResponse($this->items);
    }

    /**
     * @return ShowOrderItem[]
     */
    public function getSortedItemsFromRequest(ServerRequestInterface $request): array
    {
        $result = Helper::mapRequestBody(
            $request->getParsedBody(),
            <<<'VALUES'
                array{
                    values: list<positive-int|string>
                }
                VALUES,
        );

        /** @var list<positive-int> $values */
        $values = \array_unique($result['values']);

        $values = \array_filter($values, function (int $value) {
            return \array_find($this->items, static fn(ShowOrderItem $item) => $item->id === $value) !== null;
        });

        return \array_map(function (int $value) {
            return \array_find($this->items, static fn(ShowOrderItem $item) => $item->id === $value);
        }, $values);
    }
}
