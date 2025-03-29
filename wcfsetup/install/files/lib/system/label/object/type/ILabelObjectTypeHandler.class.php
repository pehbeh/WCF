<?php

namespace wcf\system\label\object\type;

/**
 * Every label object type handler has to implement this interface.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ILabelObjectTypeHandler
{
    /**
     * Sets object type id.
     *
     * @return void
     */
    public function setObjectTypeID(int $objectTypeID);

    /**
     * Returns object type id.
     *
     * @return  int
     */
    public function getObjectTypeID();

    /**
     * Returns a label object type container.
     *
     * @return  LabelObjectTypeContainer
     */
    public function getContainer();

    /**
     * Performs save actions.
     *
     * @return void
     */
    public function save();

    /**
     * Returns true if this object type can handle multiple selected labels per
     * label group.
     */
    public function supportsMultipleSelection(): bool;
}
