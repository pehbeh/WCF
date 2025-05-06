<?php

/**
 * This script converts existing contact options to the new form option system.
 */

use wcf\data\contact\option\ContactOptionEditor;
use wcf\data\contact\option\ContactOptionList;
use wcf\util\JSON;
use wcf\util\OptionUtil;

$contactOptionList = new ContactOptionList();
$contactOptionList->readObjects();
$contactOptionList->getConditionBuilder()->add('configurationData IS NULL');

foreach ($contactOptionList as $contactOption) {
    $configurationData = [];
    $optionType = '';
    $optionType = match ($contactOption->optionType) {
        'multiSelect' => 'checkboxes',
        'message' => 'wysiwyg',
        'URL' => 'url',
        default => $contactOption->optionType,
    };

    if ($contactOption->required) {
        $configurationData['required'] = 1;
    }
    if ($contactOption->defaultValue && $contactOption->optionType == 'text') {
        $configurationData['defaultValue'] = $contactOption->defaultValue;
    }
    if ($contactOption->selectOptions) {
        $configurationData['required'] = convertSelectOptions($contactOption->selectOptions);
    }

    $editor = new ContactOptionEditor($contactOption);
    $editor->update([
        'optionType' => $optionType,
        'configurationData' => JSON::encode($configurationData),
    ]);
}

function convertSelectOptions(string $selectOptions): string
{
    $options = [];

    $parsedSelectOptions = OptionUtil::parseSelectOptions($selectOptions);
    foreach ($parsedSelectOptions as $key => $value) {
        $options[] = [
            'key' => $key,
            'value' => [
                0 => $value
            ]
        ];
    }

    return JSON::encode($options);
}
