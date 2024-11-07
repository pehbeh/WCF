<?php

namespace wcf\system\view\grid\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

class I18nTextFilter extends TextFilter
{
    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
    {
        $list->getConditionBuilder()->add("($id LIKE ? OR $id IN (SELECT languageItem FROM wcf1_language_item WHERE languageID = ? AND languageItemValue LIKE ?))", [
            '%' . $value . '%',
            WCF::getLanguage()->languageID,
            '%' . $value . '%'
        ]);
    }
}
