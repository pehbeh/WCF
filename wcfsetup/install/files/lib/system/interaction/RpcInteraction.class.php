<?php

namespace wcf\system\interaction;

use wcf\action\ApiAction;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledObject;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents an interaction that call a rpc endpoint.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class RpcInteraction extends AbstractInteraction
{
    public function __construct(
        string $identifier,
        protected readonly string $endpoint,
        protected readonly string|\Closure $languageItem,
        protected readonly InteractionConfirmationType $confirmationType = InteractionConfirmationType::None,
        protected readonly string|\Closure $confirmationMessage = '',
        ?\Closure $isAvailableCallback = null,
        protected readonly bool $refreshAll = false,
    ) {
        parent::__construct($identifier, $isAvailableCallback);
    }

    #[\Override]
    public function render(DatabaseObject $object): string
    {
        $identifier = StringUtil::encodeJS($this->getIdentifier());

        if (\is_string($this->languageItem)) {
            $label = WCF::getLanguage()->get($this->languageItem);
        } else {
            $label = ($this->languageItem)($object);
        }

        if (\is_string($this->confirmationMessage)) {
            $confirmationMessage = WCF::getLanguage()->get($this->confirmationMessage);
        } else {
            $confirmationMessage = ($this->confirmationMessage)($object);
        }

        $endpoint = StringUtil::encodeHTML(
            LinkHandler::getInstance()->getControllerLink(ApiAction::class, ['id' => 'rpc']) .
                \sprintf($this->endpoint, $object->getObjectID())
        );

        if ($object instanceof ITitledObject) {
            $objectName = StringUtil::encodeHTML($object->getTitle());
        } else {
            $objectName = '';

            if ($object instanceof DatabaseObjectDecorator) {
                $baseObject = $object->getDecoratedObject();

                if ($baseObject instanceof ITitledObject) {
                    $objectName = StringUtil::encodeHTML($baseObject->getTitle());
                }
            }
        }

        return <<<HTML
            <button
                type="button"
                data-interaction="{$identifier}"
                data-object-name="{$objectName}"
                data-endpoint="{$endpoint}"
                data-confirmation-type="{$this->confirmationType->toString()}"
                data-confirmation-message="{$confirmationMessage}"
                data-refresh-all={$this->refreshAll}
            >
                {$label}
            </button>
            HTML;
    }

    #[\Override]
    public function renderInitialization(string $containerId): ?string
    {
        $identifier = StringUtil::encodeJS($this->getIdentifier());
        $containerId = StringUtil::encodeJS($containerId);

        return <<<HTML
            <script data-relocate="true">
                require(['WoltLabSuite/Core/Component/Interaction/Rpc'], ({ setup }) => {
                    setup('{$identifier}', document.getElementById('{$containerId}'));
                });
            </script>
            HTML;
    }
}
