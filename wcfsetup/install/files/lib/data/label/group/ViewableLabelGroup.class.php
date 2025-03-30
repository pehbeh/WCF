<?php

namespace wcf\data\label\group;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITraversableObject;
use wcf\data\label\Label;
use wcf\data\user\User;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * Represents a viewable label group.
 *
 * @author  Alexander Ebert, Joshua Ruesweg
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin   LabelGroup
 * @extends DatabaseObjectDecorator<LabelGroup>
 * @implements ITraversableObject<Label>
 */
class ViewableLabelGroup extends DatabaseObjectDecorator implements \Countable, ITraversableObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = LabelGroup::class;

    /**
     * list of labels
     * @var array<int, Label>
     */
    protected $labels = [];

    /**
     * list of permissions by type
     * @var int[][]
     */
    protected $permissions = [
        'group' => [],
        'user' => [],
    ];

    /**
     * current iterator index
     * @var int
     */
    protected $index = 0;

    /**
     * list of index to object relation
     * @var int[]
     */
    protected $indexToObject;

    /**
     * Adds a label.
     *
     * @return void
     */
    public function addLabel(Label $label)
    {
        $this->labels[$label->labelID] = $label;
        $this->indexToObject[] = $label->labelID;
    }

    /**
     * Sets group permissions.
     *
     * @param mixed[] $permissions
     * @return void
     */
    public function setGroupPermissions(array $permissions)
    {
        $this->permissions['group'] = $permissions;
    }

    /**
     * Sets user permissions.
     *
     * @param mixed[] $permissions
     * @return void
     */
    public function setUserPermissions(array $permissions)
    {
        $this->permissions['user'] = $permissions;
    }

    /**
     * Returns true, if label is known.
     *
     * @return bool
     */
    public function isValid(int $labelID)
    {
        return isset($this->labels[$labelID]);
    }

    /**
     * Returns true, if the given user fulfils option id permissions.
     * If the user parameter is null, the method checks the current user.
     *
     * @return bool
     */
    public function getPermission(int $optionID, ?User $user = null)
    {
        if ($user === null) {
            $user = WCF::getUser();
        }

        // validate by user id
        if ($user->userID) {
            if (
                isset($this->permissions['user'][$user->userID])
                && isset($this->permissions['user'][$user->userID][$optionID])
            ) {
                if ($this->permissions['user'][$user->userID][$optionID] == 1) {
                    return true;
                }
            }
        }

        // validate by group id
        foreach ($user->getGroupIDs() as $groupID) {
            if (
                isset($this->permissions['group'][$groupID])
                && isset($this->permissions['group'][$groupID][$optionID])
            ) {
                if ($this->permissions['group'][$groupID][$optionID] == 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns a list of label ids.
     *
     * @return int[]
     */
    public function getLabelIDs()
    {
        return \array_keys($this->labels);
    }

    /**
     * Returns a list of labels.
     *
     * @return Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Returns a label by id.
     *
     * @return ?Label
     */
    public function getLabel(int $labelID)
    {
        return $this->labels[$labelID] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return \count($this->labels);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $objectID = $this->indexToObject[$this->index];

        return $this->labels[$objectID];
    }

    /**
     * CAUTION: This methods does not return the current iterator index,
     * rather than the object key which maps to that index.
     *
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->indexToObject[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->indexToObject[$this->index]);
    }

    /**
     * @inheritDoc
     */
    public function seek($offset): void
    {
        $this->index = $offset;

        if (!$this->valid()) {
            throw new \OutOfBoundsException();
        }
    }

    /**
     * @inheritDoc
     */
    public function seekTo($objectID)
    {
        $this->index = \array_search($objectID, $this->indexToObject);

        if ($this->index === false) {
            throw new SystemException("object id '" . $objectID . "' is invalid");
        }
    }

    /**
     * @inheritDoc
     */
    public function search($objectID)
    {
        try {
            $this->seekTo($objectID);

            return $this->current();
        } catch (SystemException $e) {
            return null;
        }
    }

    /**
     * Returns true if any permissions have been set for this label group.
     *
     * @return bool
     */
    public function hasPermissions()
    {
        return !empty($this->permissions['group']) || !empty($this->permissions['user']);
    }

    /**
     * @param int[] $selectedLabelIDs
     */
    public function toHtml(array $selectedLabelIDs = []): string
    {
        $labels = [];
        foreach ($this->labels as $label) {
            $labels[] = [$label->labelID, $label->render()];
        }

        $selected = \array_find(
            $this->labels,
            static fn(Label $label, int $labelID) => \in_array($labelID, $selectedLabelIDs, true)
        );

        return \sprintf(
            '<woltlab-core-label-picker labels="%s" value="%d" %s></woltlab-core-label-picker>',
            StringUtil::encodeHTML(JSON::encode($labels)),
            $selected ? $selected->labelID : 0,
            $this->forceSelection ? "required" : "",
        );
    }
}
