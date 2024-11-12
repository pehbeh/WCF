<?php

namespace wcf\system\gridView\renderer;

abstract class AbstractColumnRenderer implements IColumnRenderer
{
    public function getClasses(): string
    {
        return '';
    }
}
