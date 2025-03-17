<?php

namespace wcf\action;

use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ModerationQueueEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\Psr15DialogForm;
use wcf\system\WCF;

/**
 * Dialog for closing a moderation queue entry.
 *
 * @author    Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
final class ModerationReportQueueCloseAction extends AbstractModerationAction
{
    #[\Override]
    protected function assertCanEditQueueEntry(ModerationQueue $queue): void
    {
        parent::assertCanEditQueueEntry($queue);

        $definition = ObjectTypeCache::getInstance()->getObjectType($queue->objectTypeID)->getDefinition();
        if ($definition->definitionName !== 'com.woltlab.wcf.moderation.report') {
            throw new PermissionDeniedException();
        }

        if ($queue->isDone()) {
            throw new PermissionDeniedException();
        }
    }

    #[\Override]
    protected function getForm(array $moderationQueues): Psr15DialogForm
    {
        $form = new Psr15DialogForm(
            static::class,
            WCF::getLanguage()->get('wcf.moderation.report.removeReport')
        );
        $form->appendChildren([
            BooleanFormField::create('markAsJustified')
                ->label("wcf.moderation.report.removeReport.markAsJustified")
                ->value(false)
        ]);

        $form->markRequiredFields(false);

        $form->build();

        return $form;
    }

    /**
     * @return array{}
     */
    #[\Override]
    protected function performAction(ModerationQueue $queue, Psr15DialogForm $form): array
    {
        $data = $form->getData()['data'];

        $editor = new ModerationQueueEditor($queue);
        $editor->markAsRejected(\boolval($data['markAsJustified']));

        return [];
    }
}
