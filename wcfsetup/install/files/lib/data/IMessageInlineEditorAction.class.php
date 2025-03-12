<?php

namespace wcf\data;

/**
 * Default interface for actions implementing message inline editing.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IMessageInlineEditorAction
{
    /**
     * Provides WYSIWYG editor for message inline editing.
     *
     * @return array{actionName: 'beginEdit', template: string}
     */
    public function beginEdit();

    /**
     * Saves changes made to a message.
     *
     * @return array{actionName: 'save', message: string}
     */
    public function save();

    /**
     * Validates parameters to begin message inline editing.
     *
     * @return void
     */
    public function validateBeginEdit();

    /**
     * Validates parameters to save changes made to a message.
     *
     * @return void
     */
    public function validateSave();
}
