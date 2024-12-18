<?php

namespace wcf\system\gridView\action;

enum ActionConfirmationType
{
    case None;
    case SoftDelete;
    case SoftDeleteWithReason;
    case Restore;
    case Delete;
    case Custom;

    public function toString(): string
    {
        return match ($this) {
            self::None => 'None',
            self::SoftDelete => 'SoftDelete',
            self::SoftDeleteWithReason => 'SoftDeleteWithReason',
            self::Restore => 'Restore',
            self::Delete => 'Delete',
            self::Custom => 'Custom',
        };
    }
}
