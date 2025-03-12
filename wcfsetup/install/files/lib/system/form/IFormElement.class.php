<?php

namespace wcf\system\form;

/**
 * Interface for form elements.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IFormElement
{
    public function __construct(IFormElementContainer $parent);

    /**
     * Returns form element description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets form element description.
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description);

    /**
     * Returns label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Sets label.
     *
     * @param string $label
     * @return void
     */
    public function setLabel($label);

    /**
     * Returns element's parent container element.
     *
     * @return IFormElementContainer
     */
    public function getParent();

    /**
     * Returns HTML-representation of current form element.
     *
     * @param string $formName
     * @return string
     */
    public function getHTML($formName);

    /**
     * Sets localized error message.
     *
     * @param string $error
     * @return void
     */
    public function setError($error);

    /**
     * Returns localized error message, empty if no error occurred.
     *
     * @return string
     */
    public function getError();
}
