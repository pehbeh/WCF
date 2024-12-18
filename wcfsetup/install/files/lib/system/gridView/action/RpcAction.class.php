<?php

namespace wcf\system\gridView\action;

use Closure;
use wcf\action\ApiAction;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents an action that call a rpc endpoint.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class RpcAction extends AbstractAction
{
    public function __construct(
        protected readonly string $endpoint,
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

        $endpoint = StringUtil::encodeHTML(
            LinkHandler::getInstance()->getControllerLink(ApiAction::class, ['id' => 'rpc']) .
                \sprintf($this->endpoint, $row->getObjectID())
        );

        if ($row instanceof ITitledObject) {
            $objectName = StringUtil::encodeHTML($row->getTitle());
        } else {
            $objectName = '';
        }

        return <<<HTML
            <button
                type="button"
                data-action="rpc"
                data-object-name="{$objectName}"
                data-endpoint="{$endpoint}"
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
                require(['WoltLabSuite/Core/Component/GridView/Action/Rpc'], ({ setup }) => {
                    setup(document.getElementById('{$id}_table'));
                });
            </script>
            HTML;
    }
}
