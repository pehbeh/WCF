<?php

namespace wcf\system\gridView\renderer;

use wcf\util\StringUtil;

class DefaultColumnRenderer extends AbstractColumnRenderer
{
    public function render(mixed $value, mixed $context = null): string
    {
        return StringUtil::encodeHTML($value);
    }

    public function getClasses(): string
    {
        return 'gridView__column--text';
    }
}
