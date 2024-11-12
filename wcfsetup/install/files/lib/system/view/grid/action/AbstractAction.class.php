<?php

namespace wcf\system\view\grid\action;

use Closure;

abstract class AbstractAction implements IGridViewAction
{
    public function __construct(
        private readonly ?Closure $isAvailableCallback = null
    ) {}

    #[\Override]
    public function isAvailable(mixed $row): bool
    {
        if ($this->isAvailableCallback === null) {
            return true;
        }

        return ($this->isAvailableCallback)($row);
    }

    #[\Override]
    public function isQuickAction(): bool
    {
        return false;
    }
}
