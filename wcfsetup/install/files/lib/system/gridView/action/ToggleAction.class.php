<?php

namespace wcf\system\gridView\action;

use Closure;
use wcf\action\ApiAction;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

class ToggleAction extends AbstractAction
{
    public function __construct(
        private readonly string $enableEndpoint,
        private readonly string $disableEndpoint,
        private readonly string $propertyName = 'isDisabled',
        ?Closure $isAvailableCallback = null
    ) {
        parent::__construct($isAvailableCallback);
    }

    #[\Override]
    public function render(mixed $row): string
    {
        \assert($row instanceof DatabaseObject);

        $enableEndpoint = StringUtil::encodeHTML(
            LinkHandler::getInstance()->getControllerLink(ApiAction::class, ['id' => 'rpc']) .
                \sprintf($this->enableEndpoint, $row->getObjectID())
        );
        $disableEndpoint = StringUtil::encodeHTML(
            LinkHandler::getInstance()->getControllerLink(ApiAction::class, ['id' => 'rpc']) .
                \sprintf($this->disableEndpoint, $row->getObjectID())
        );

        $ariaLabel = WCF::getLanguage()->get('wcf.global.button.enable');
        $checked = !$row->{$this->propertyName} ? 'checked' : '';

        return <<<HTML
            <woltlab-core-toggle-button aria-label="{$ariaLabel}" data-enable-endpoint="{$enableEndpoint}" data-disable-endpoint="{$disableEndpoint}" {$checked}></woltlab-core-toggle-button>
            HTML;
    }

    #[\Override]
    public function renderInitialization(AbstractGridView $gridView): ?string
    {
        $id = StringUtil::encodeJS($gridView->getID());

        return <<<HTML
            <script data-relocate="true">
                require(['WoltLabSuite/Core/Component/GridView/Action/Toggle'], ({ setup }) => {
                    setup('{$id}_table');
                });
            </script>
            HTML;
    }

    #[\Override]
    public function isQuickAction(): bool
    {
        return true;
    }
}
