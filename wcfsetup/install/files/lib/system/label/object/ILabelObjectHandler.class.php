<?php

namespace wcf\system\label\object;

use wcf\data\label\group\ViewableLabelGroup;
use wcf\data\label\Label;

/**
 * Every label object handler has to implement this interface.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ILabelObjectHandler
{
    /**
     * Returns a list of label group ids.
     *
     * @param mixed[] $parameters
     * @return int[]
     */
    public function getLabelGroupIDs(array $parameters = []);

    /**
     * Returns a list of label groups.
     *
     * @param mixed[] $parameters
     * @return ViewableLabelGroup[]
     */
    public function getLabelGroups(array $parameters = []);

    /**
     * Returns true, if all given label ids are valid and accessible.
     *
     * @param int[] $labelIDs
     * @param string $optionName
     * @param bool $legacyReturnValue
     * @return mixed
     * @deprecated 6.2 Use `validateSelectedLabels()` instead
     */
    public function validateLabelIDs(array $labelIDs, $optionName = '', $legacyReturnValue = true);

    /**
     * Validates the list of label ids and returns a list of errors per label
     * group if any.
     *
     * @param list<int> $labelIDs
     * @param string $optionName
     * @return array<int, string>
     * @since 6.2
     */
    public function validateSelectedLabels(array $labelIDs, string $optionName = ''): array;

    /**
     * Assigns labels to an object.
     *
     * @param int[] $labelIDs
     * @param int $objectID
     * @param bool $validatePermissions
     * @return void
     * @see     \wcf\system\label\LabelHandler::setLabels()
     */
    public function setLabels(array $labelIDs, $objectID, $validatePermissions = true);

    /**
     * Removes all assigned labels.
     *
     * @param int $objectID
     * @param bool $validatePermissions
     * @return void
     * @see     \wcf\system\label\LabelHandler::removeLabels()
     */
    public function removeLabels($objectID, $validatePermissions = true);

    /**
     * Returns a list of assigned labels.
     *
     * @param int[] $objectIDs
     * @param bool $validatePermissions
     * @return Label[][]
     */
    public function getAssignedLabels(array $objectIDs, $validatePermissions = true);
}
