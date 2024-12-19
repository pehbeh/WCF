<?php

namespace wcf\system\endpoint\controller\core\messages;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\IMessage;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\html\input\HtmlInputProcessor;

/**
 * Renders a quote message.
 *
 * @author    Olaf Braun
 * @copyright 2001-2024 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
#[GetRequest('/core/messages/renderquote')]
final class RenderQuote implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, GetRenderQuoteParameters::class);

        return new JsonResponse(
            $this->renderFullQuote($parameters),
            200,
        );
    }

    private function renderFullQuote(GetRenderQuoteParameters $parameters): string
    {
        // TODO load object
        /** @var $object IMessage */
        // TODO load embedded objects?

        $htmlInputProcessor = new HtmlInputProcessor();
        $htmlInputProcessor->processIntermediate($object->getMessage());

        if (MESSAGE_MAX_QUOTE_DEPTH) {
            $htmlInputProcessor->enforceQuoteDepth(MESSAGE_MAX_QUOTE_DEPTH - 1, true);
        }

        return $htmlInputProcessor->getHtml();
    }
}

/** @internal */
final class GetRenderQuoteParameters
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $objectType,
        /** @var positive-int */
        public readonly int $objectID,
    ) {
    }
}
