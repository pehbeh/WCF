<?php

namespace wcf\page;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\event\moderation\DeletedItemsCollecting;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\moderation\DeletedItemsBoxComponent;
use wcf\system\WCF;

/**
 * List of deleted content.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends MultipleLinkPage<DatabaseObjectList<DatabaseObject>>
 */
class DeletedContentListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['mod.general.canUseModeration'];

    /**
     * object type object
     * @var \wcf\data\object\type\ObjectType
     */
    public $objectType;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['objectType'])) {
            $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName(
                'com.woltlab.wcf.deletedContent',
                $_REQUEST['objectType']
            );

            if ($this->objectType === null) {
                throw new IllegalLinkException();
            }
        } else {
            $link = $this->getFirstTypeLink();
            if ($link === null) {
                throw new IllegalLinkException();
            }

            return new RedirectResponse($link);
        }
    }

    private function getFirstTypeLink(): ?string
    {
        $event = new DeletedItemsCollecting();
        EventHandler::getInstance()->fire($event);
        $types = $event->getTypes();

        if ($types === []) {
            return null;
        }

        return reset($types)->link;
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        $this->objectList = $this->objectType->getProcessor()->getObjectList();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'deletedItemsBox' => new DeletedItemsBoxComponent($this->objectType->objectType),
            'objectType' => $this->objectType->objectType,
            'resultListTemplateName' => $this->objectType->getProcessor()->getTemplateName(),
            'resultListApplication' => $this->objectType->getProcessor()->getApplication(),
        ]);
    }
}
