<?php

namespace wcf\system\interaction\user;

use wcf\action\ModerationQueueAssignUserAction;
use wcf\action\ModerationReportQueueCloseAction;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\data\object\type\ObjectTypeCache;
use wcf\event\interaction\user\ModerationQueueInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\FormBuilderDialogInteraction;
use wcf\system\interaction\InteractionConfirmationType;
use wcf\system\interaction\RpcInteraction;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;
use wcf\system\request\LinkHandler;

/**
 * Interaction provider for moderation queue entries.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ModerationQueueInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new FormBuilderDialogInteraction(
                "assign-user",
                LinkHandler::getInstance()->getControllerLink(ModerationQueueAssignUserAction::class, ["id" => "%s"]),
                "wcf.moderation.assignedUser.change",
                static fn(ViewableModerationQueue $queue) => $queue->canEdit()
            ),
            new FormBuilderDialogInteraction(
                "close",
                LinkHandler::getInstance()->getControllerLink(ModerationReportQueueCloseAction::class, ["id" => "%s"]),
                "wcf.moderation.report.removeReport",
                isAvailableCallback: static function (ViewableModerationQueue $queue) {
                    return self::isReportQueue($queue)
                        && $queue->canEdit()
                        && !$queue->isDone();
                }
            ),
            new RpcInteraction(
                "remove-content",
                "core/moderation-queues/%s/delete-content",
                "wcf.moderation.report.removeContent",
                InteractionConfirmationType::SoftDeleteWithReason,
                isAvailableCallback: static function (ViewableModerationQueue $queue) {
                    $objectType = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID);
                    /** @var IModerationQueueReportHandler $processor */
                    $processor = $objectType->getProcessor();

                    return self::isReportQueue($queue)
                        && $queue->canEdit()
                        && !$queue->isDone()
                        && $processor->canRemoveContent($queue->getDecoratedObject());
                }
            )
        ]);

        EventHandler::getInstance()->fire(
            new ModerationQueueInteractionCollecting($this)
        );
    }

    private static function isReportQueue(ViewableModerationQueue $queue): bool
    {
        $definition = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID)->getDefinition();

        return $definition->definitionName === 'com.woltlab.wcf.moderation.report';
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return ViewableModerationQueue::class;
    }
}
