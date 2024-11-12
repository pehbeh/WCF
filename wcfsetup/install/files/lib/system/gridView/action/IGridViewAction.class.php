<?php

namespace wcf\system\gridView\action;

use wcf\system\gridView\AbstractGridView;

interface IGridViewAction
{
    public function render(mixed $row): string;

    public function renderInitialization(AbstractGridView $gridView): ?string;

    public function isQuickAction(): bool;

    public function isAvailable(mixed $row): bool;
}
