<?php

namespace wcf\system\gridView\renderer;

class TitleColumnRenderer extends DefaultColumnRenderer
{
    public function getClasses(): string
    {
        return 'gridView__column--title';
    }
}
