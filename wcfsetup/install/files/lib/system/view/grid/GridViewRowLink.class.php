<?php

namespace wcf\system\view\grid;

use wcf\data\DatabaseObject;
use wcf\system\request\LinkHandler;
use wcf\util\StringUtil;

class GridViewRowLink
{
    public function __construct(
        private readonly string $controllerClass = '',
        private readonly array $parameters = [],
        private readonly string $cssClass = ''
    ) {}

    public function render(mixed $value, mixed $context = null, bool $isPrimaryColumn = false): string
    {
        $href = '';
        if ($this->controllerClass) {
            \assert($context instanceof DatabaseObject);
            $href = LinkHandler::getInstance()->getControllerLink(
                $this->controllerClass,
                \array_merge($this->parameters, ['object' => $context])
            );
        }

        $attributes = [];
        $isButton = true;
        if ($href) {
            $attributes[] = 'href="' . $href . '"';
            $isButton = false;
        }
        $attributes[] = 'class="gridView__rowLink ' . StringUtil::encodeHTML($this->cssClass) . '"';
        $attributes[] = 'tabindex="' . ($isPrimaryColumn ? '0' : '-1') . '"';

        if ($isButton) {
            return '<button type="button" ' . implode(' ', $attributes) . '>'
                . $value
                . '</button>';
        } else {
            return '<a ' . implode(' ', $attributes) . '>'
                . $value
                . '</a>';
        }
    }
}
