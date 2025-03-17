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
     * @param int $objectTypeID
     * @return void
     */
    public function setObjectTypeID($objectTypeID);

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
}
