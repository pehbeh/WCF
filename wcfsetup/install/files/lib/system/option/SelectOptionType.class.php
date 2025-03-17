<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\system\WCF;

/**
 * Option type implementation for select lists.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SelectOptionType extends RadioButtonOptionType
{
    /**
     * @var bool
     */
    protected $allowEmptyValue = false;

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        $options = $this->parseEnableOptions($option);

        /** @noinspection PhpUndefinedFieldInspection */
        return WCF::getTPL()->render('wcf', 'selectOptionType', [
            'disableOptions' => $options['disableOptions'],
            'enableOptions' => $options['enableOptions'],
            'option' => $option,
            'selectOptions' => $this->getSelectOptions($option),
            'value' => $value,
            'allowEmptyValue' => $this->allowEmptyValue || $option->allowEmptyValue,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getSearchFormElement(Option $option, $value)
    {
        $options = $this->parseEnableOptions($option);

        return WCF::getTPL()->render('wcf', 'selectSearchableOptionType', [
            'disableOptions' => $options['disableOptions'],
            'enableOptions' => $options['enableOptions'],
            'option' => $option,
            'searchOption' => $this->forceSearchOption || ($value !== null && $value !== $option->defaultValue) || isset($_POST['searchOptions'][$option->optionName]),
            'selectOptions' => $this->getSelectOptions($option),
            'value' => $value,
            'allowEmptyValue' => true,
        ]);
    }

    /**
     * Prepares JSON-encoded values for disabling or enabling dependent options.
     *
     * @param Option $option
     * @return array{disableOptions: string, enableOptions: string}
     */
    protected function parseEnableOptions(Option $option)
    {
        $disableOptions = $enableOptions = '';

        if (!empty($option->enableOptions)) {
            $options = $option->parseMultipleEnableOptions();

            foreach ($options as $key => $optionData) {
                $tmp = \explode(',', $optionData);

                foreach ($tmp as $item) {
                    if ($item[0] == '!') {
                        if (!empty($disableOptions)) {
                            $disableOptions .= ',';
                        }
                        $disableOptions .= '{ "value": "' . $key . '", "option": "' . \mb_substr($item, 1) . '" }';
                    } else {
                        if (!empty($enableOptions)) {
                            $enableOptions .= ',';
                        }
                        $enableOptions .= '{ "value": "' . $key . '", "option": "' . $item . '" }';
                    }
                }
            }
        }

        return [
            'disableOptions' => $disableOptions,
            'enableOptions' => $enableOptions,
        ];
    }

    /**
     * @inheritDoc
     */
    public function hideLabelInSearch()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCSSClassName()
    {
        return '';
    }
}
