<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ModerationQueueList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\Psr15DialogForm;

/**
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class AbstractModerationReportAction implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    id?: positive-int,
                    objectIDs?: positive-int[]
                }
                EOT
        );

        if (!isset($parameters['id']) && !isset($parameters['objectIDs'])) {
            throw new IllegalLinkException();
        }

        $objectIDs = $parameters['objectIDs'] ?? [$parameters['id']];
        $moderationList = new ModerationQueueList();
        $moderationList->setObjectIDs($objectIDs);
        $moderationList->readObjects();

        if ($moderationList->count() === 0) {
            throw new IllegalLinkException();
        }

        foreach ($moderationList as $queue) {
            $this->assertCanEditQueueEntry($queue);
        }

        $form = $this->getForm();

        if ($request->getMethod() === 'GET') {
            return $form->toResponse();
        } elseif ($request->getMethod() === 'POST') {
            $response = $form->validateRequest($request);
            if ($response !== null) {
                return $response;
            }

            foreach ($moderationList as $queue) {
                $this->performAction($queue, $form);
            }

            return new JsonResponse([]);
        } else {
            throw new \LogicException('Unreachable');
        }
    }

    protected function assertCanEditQueueEntry(ModerationQueue $queue): void
    {
        $definition = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID)->getDefinition();
        if ($definition->definitionName !== 'com.woltlab.wcf.moderation.report') {
            throw new PermissionDeniedException();
        }

        if (!$queue->canEdit()) {
            throw new PermissionDeniedException();
        }
    }

    abstract protected function getForm(): Psr15DialogForm;

    abstract protected function performAction(ModerationQueue $queue, Psr15DialogForm $form): void;
}
