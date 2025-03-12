<?php

namespace wcf\data\template\listener;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes template listener-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<TemplateListener, TemplateListenerEditor>
 */
class TemplateListenerAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = TemplateListenerEditor::class;
}
