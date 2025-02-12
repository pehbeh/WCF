<?php

namespace wcf\action;

use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ModerationQueueEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\field\MultilineTextFormField;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;
use wcf\system\WCF;

/**
 * Dialog for removing content to a moderation queue entry.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ModerationReportQueueRemoveContentAction extends AbstractModerationReportAction
{
    protected function assertCanEditQueueEntry(ModerationQueue $queue): void
    {
        parent::assertCanEditQueueEntry($queue);
        if ($queue->isDone()) {
            throw new PermissionDeniedException();
        }

        $objectType = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID);
        /** @var IModerationQueueReportHandler $processor */
        $processor = $objectType->getProcessor();
        if (!$processor->canRemoveContent($queue)) {
            throw new PermissionDeniedException();
        }
    }

    protected function getForm(): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('wcf.moderation.report.removeContent')
        );
        $form->appendChildren([
            MultilineTextFormField::create("reason")
                ->label("wcf.dialog.confirmation.reason")
                ->rows(4)
        ]);

        $form->markRequiredFields(false);

        $form->build();

        return $form;
    }

    #[\Override]
    protected function performAction(ModerationQueue $queue, Psr15DialogForm $form): void
    {
        $data = $form->getData()['data'];

        $this->getManager($queue)->removeContent(
            $queue,
            $data['reason'] ?? ''
        );

        $editor = new ModerationQueueEditor($queue);
        $editor->markAsConfirmed();
    }

    private function getManager(ModerationQueue $queue): IModerationQueueReportHandler
    {
        $objectType = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID);
        return $objectType->getProcessor();
    }
}
