<?php

namespace wcf\system\gridView\renderer;

interface IColumnRenderer
{
    public function render(mixed $value, mixed $context = null): string;

    public function getClasses(): string;
}
