<?php

namespace wcf\system\interaction\admin;

use wcf\data\attachment\AdministrativeAttachment;
use wcf\data\DatabaseObject;
use wcf\event\interaction\admin\AttachmentInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteraction;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Interaction provider for attachments.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class AttachmentInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('core/attachments/%s'),
            new class(
                'goToContent',
                static fn(AdministrativeAttachment $object) => $object->getContainerObject() !== null
            ) extends AbstractInteraction {
                #[\Override]
                public function render(DatabaseObject $object): string
                {
                    \assert($object instanceof AdministrativeAttachment);

                    return \sprintf(
                        '<a href="%s">%s</a>',
                        StringUtil::encodeHTML($object->getContainerObject()->getLink()),
                        WCF::getLanguage()->get('wcf.acp.attachment.button.goToContent')
                    );
                }
            }
        ]);

        EventHandler::getInstance()->fire(
            new AttachmentInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return AdministrativeAttachment::class;
    }
}
