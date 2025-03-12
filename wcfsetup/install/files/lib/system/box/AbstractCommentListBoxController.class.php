<?php

namespace wcf\system\box;

use wcf\data\comment\ViewableCommentList;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\ignore\UserIgnore;
use wcf\system\exception\InvalidObjectTypeException;
use wcf\system\user\UserProfileHandler;
use wcf\system\WCF;

/**
 * Abstract box controller implementation for a list of comments for a certain type of objects.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 *
 * @extends AbstractDatabaseObjectListBoxController<ViewableCommentList>
 */
abstract class AbstractCommentListBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    /**
     * name of the commentable object type the listed comments belong to
     * @var string
     */
    protected $objectTypeName = '';

    /**
     * commentable object type the listed comments belong to
     * @var ObjectType
     */
    public $objectType;

    /**
     * @inheritDoc
     */
    protected $sortFieldLanguageItemPrefix = 'wcf.comment.sortField';

    /**
     * @inheritDoc
     */
    protected static $supportedPositions = [
        'sidebarLeft',
        'sidebarRight',
    ];

    /**
     * @inheritDoc
     */
    public $validSortFields = ['time'];

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName(
            'com.woltlab.wcf.comment.commentableContent',
            $this->objectTypeName
        );
        if ($this->objectType === null) {
            throw new InvalidObjectTypeException($this->objectTypeName, 'com.woltlab.wcf.comment.commentableContent');
        }

        if (!empty($this->validSortFields) && MODULE_LIKE) {
            $this->validSortFields[] = 'cumulativeLikes';
        }

        parent::__construct();
    }

    /**
     * Applies object type-specific filters to the comments.
     *
     * @return void
     */
    abstract protected function applyObjectTypeFilters(ViewableCommentList $commentList);

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        $commentList = new ViewableCommentList();
        $commentList->getConditionBuilder()->add('comment.isDisabled = ?', [0]);
        $commentList->getConditionBuilder()->add('comment.objectTypeID = ?', [$this->objectType->objectTypeID]);

        $this->applyObjectTypeFilters($commentList);

        if (!empty(UserProfileHandler::getInstance()->getIgnoredUsers(UserIgnore::TYPE_HIDE_MESSAGES))) {
            $commentList->getConditionBuilder()->add(
                "(comment.userID IS NULL OR comment.userID NOT IN (?))",
                [UserProfileHandler::getInstance()->getIgnoredUsers(UserIgnore::TYPE_HIDE_MESSAGES)]
            );
        }

        return $commentList;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        return WCF::getTPL()->render('wcf', 'boxSidebarCommentList', [
            'boxCommentList' => $this->objectList,
            'boxSortField' => $this->sortField,
        ]);
    }
}
