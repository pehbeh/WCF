<?php

namespace wcf\system\view\grid\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\DateRangeFormField;
use wcf\system\WCF;

class TimeFilter implements IGridViewFilter
{
    #[\Override]
    public function getFormField(string $id, string $label): AbstractFormField
    {
        return DateRangeFormField::create($id)
            ->label($label)
            ->supportTime();
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
    {
        $timestamps = $this->getTimestamps($value);

        if (!$timestamps['from'] && !$timestamps['to']) {
            return;
        }

        if (!$timestamps['to']) {
            $list->getConditionBuilder()->add("$id >= ?", [$timestamps['from']]);
        } else {
            $list->getConditionBuilder()->add("$id BETWEEN ? AND ?", [$timestamps['from'], $timestamps['to']]);
        }
    }

    #[\Override]
    public function matches(string $filterValue, string $rowValue): bool
    {
        $timestamps = $this->getTimestamps($filterValue);

        if (!$timestamps['from'] && !$timestamps['to']) {
            return true;
        }

        if (!$timestamps['to']) {
            return $rowValue >= $timestamps['from'];
        } else {
            return $rowValue >= $timestamps['from'] && $rowValue <= $timestamps['to'];
        }
    }

    #[\Override]
    public function renderValue(string $value): string
    {
        $values = explode(';', $value);
        if (\count($values) !== 2) {
            return '';
        }

        $locale = WCF::getLanguage()->getLocale();;
        $fromString = $toString = '';
        if ($values[0] !== '') {
            $fromDateTime = \DateTime::createFromFormat(
                'Y-m-d\TH:i:sP',
                $values[0],
                WCF::getUser()->getTimeZone()
            );
            if ($fromDateTime !== false) {
                $fromString = \IntlDateFormatter::formatObject(
                    $fromDateTime,
                    [
                        \IntlDateFormatter::LONG,
                        \IntlDateFormatter::SHORT,
                    ],
                    $locale
                );
            }
        }
        if ($values[1] !== '') {
            $toDateTime = \DateTime::createFromFormat(
                'Y-m-d\TH:i:sP',
                $values[1],
                WCF::getUser()->getTimeZone()
            );
            if ($toDateTime !== false) {
                $toString = \IntlDateFormatter::formatObject(
                    $toDateTime,
                    [
                        \IntlDateFormatter::LONG,
                        \IntlDateFormatter::SHORT,
                    ],
                    $locale
                );
            }
        }

        if ($fromString && $toString) {
            return $fromString . ' ‐ ' . $toString;
        } else if ($fromString) {
            return '>= ' . $fromString;
        } else if ($toString) {
            return '<= ' . $toString;
        }

        return '';
    }

    private function getTimestamps(string $value): array
    {
        $from = 0;
        $to = 0;

        $values = explode(';', $value);
        if (\count($values) === 2) {
            $from = $this->getTimestamp($values[0]);
            $to = $this->getTimestamp($values[1]);
        }

        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    private function getTimestamp(string $date): int
    {
        $dateTime = \DateTime::createFromFormat(
            'Y-m-d\TH:i:sP',
            $date
        );

        if ($dateTime !== false) {
            return $dateTime->getTimestamp();
        }

        return 0;
    }
}
