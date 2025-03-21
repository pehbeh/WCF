<?php

namespace wcf\system\listView;

use wcf\system\WCF;

final class ListViewSortField
{
    public function __construct(
        public readonly string $id,
        public readonly string $languageItem,
        public readonly string $sortByDatabaseColumn = ''
    ) {}

    public function __toString(): string
    {
        return WCF::getLanguage()->get($this->languageItem);
    }
}
