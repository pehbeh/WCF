<?php

namespace wcf\system\gridView\action;

use Closure;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents an action that executes a dbo action.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 * @deprecated  6.2 DBO actions are considered outdated and should be migrated to RPC endpoints.
 */
class LegacyDboAction extends AbstractAction
{
    public function __construct(
        protected readonly string $className,
        protected readonly string $actionName,
        protected readonly string|Closure $languageItem,
        protected readonly ActionConfirmationType $confirmationType = ActionConfirmationType::None,
        protected readonly string|Closure $confirmationMessage = '',
        ?Closure $isAvailableCallback = null
    ) {
        parent::__construct($isAvailableCallback);
    }

    #[\Override]
    public function render(mixed $row): string
    {
        \assert($row instanceof DatabaseObject);

        if (\is_string($this->languageItem)) {
            $label = WCF::getLanguage()->get($this->languageItem);
        } else {
            $label = ($this->languageItem)($row);
        }

        if (\is_string($this->confirmationMessage)) {
            $confirmationMessage = WCF::getLanguage()->get($this->confirmationMessage);
        } else {
            $confirmationMessage = ($this->confirmationMessage)($row);
        }

        if ($row instanceof ITitledObject) {
            $objectName = StringUtil::encodeHTML($row->getTitle());
        } else {
            $objectName = '';
        }

        $className = StringUtil::encodeHTML($this->className);
        $actionName = StringUtil::encodeHTML($this->actionName);

        return <<<HTML
            <button
                type="button"
                data-action="legacy-dbo-action"
                data-object-name="{$objectName}"
                data-class-name="{$className}"
                data-action-name="{$actionName}"
                data-confirmation-type="{$this->confirmationType->toString()}"
                data-confirmation-message="{$confirmationMessage}"
            >
                {$label}
            </button>
            HTML;
    }

    #[\Override]
    public function renderInitialization(AbstractGridView $gridView): ?string
    {
        $id = StringUtil::encodeJS($gridView->getID());

        return <<<HTML
            <script data-relocate="true">
                require(['WoltLabSuite/Core/Component/GridView/Action/LegacyDboAction'], ({ setup }) => {
                    setup(document.getElementById('{$id}_table'));
                });
            </script>
            HTML;
    }
}
