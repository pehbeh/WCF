<?php

namespace wcf\system\file\processor;

enum ImageCropperType
{
    case MinMax;
    case Exact;

    public function toString(): string
    {
        return match ($this) {
            self::MinMax => 'minMax',
            self::Exact => 'exact',
        };
    }

    public static function fromString(string $fileType): self
    {
        return match ($fileType) {
            'minMax' => self::MinMax,
            'exact' => self::Exact,
        };
    }
}
