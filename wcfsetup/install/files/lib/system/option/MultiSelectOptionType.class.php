<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Option type implementation for multiple select lists.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class MultiSelectOptionType extends SelectOptionType
{
    /**
     * name of the form element template
     * @var string
     */
    protected $formElementTemplate = 'multiSelectOptionType';

    /**
     * name of the searchable form element template
     * @var string
     */
    protected $searchableFormElementTemplate = 'multiSelectSearchableOptionType';

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        return WCF::getTPL()->render('wcf', $this->formElementTemplate, [
            'option' => $option,
            'selectOptions' => $this->getSelectOptions($option),
            'value' => !\is_array($value) ? \explode("\n", $value ?? '') : $value,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getSearchFormElement(Option $option, $value)
    {
        return WCF::getTPL()->render('wcf', $this->searchableFormElementTemplate, [
            'option' => $option,
            'searchOption' => $this->forceSearchOption || ($value !== null && $value !== $option->defaultValue) || isset($_POST['searchOptions'][$option->optionName]),
            'selectOptions' => $this->getSelectOptions($option),
            'value' => !\is_array($value) ? \explode("\n", $value ?? '') : $value,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }
        $options = $this->getSelectOptions($option);
        foreach ($newValue as $value) {
            if (!isset($options[$value])) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }

        return \implode("\n", $newValue);
    }

    /**
     * @inheritDoc
     */
    public function getCondition(PreparedStatementConditionBuilder &$conditions, Option $option, $value)
    {
        if (!isset($_POST['searchOptions'][$option->optionName])) {
            return false;
        }

        if (!\is_array($value) || empty($value)) {
            return false;
        }
        $value = ArrayUtil::trim($value, false);

        foreach ($value as $entry) {
            $escapedEntry = \addcslashes($entry, '%_');
            $conditions->add(
                "(
                    option_value.userOption" . $option->optionID . " LIKE ?
                    OR option_value.userOption" . $option->optionID . " LIKE ?
                    OR option_value.userOption" . $option->optionID . " LIKE ?
                    OR option_value.userOption" . $option->optionID . " = ?
                )",
                [
                    "%\n{$escapedEntry}\n%",
                    "%\n{$escapedEntry}",
                    "{$escapedEntry}\n%",
                    $entry,
                ]
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function addCondition(UserList $userList, Option $option, $value)
    {
        if (!\is_array($value) || empty($value)) {
            return;
        }
        $value = ArrayUtil::trim($value, false);

        foreach ($value as $entry) {
            $escapedEntry = \addcslashes($entry, '%_');
            $userList->getConditionBuilder()->add(
                "(
                    user_option_value.userOption" . $option->optionID . " LIKE ?
                    OR user_option_value.userOption" . $option->optionID . " LIKE ?
                    OR user_option_value.userOption" . $option->optionID . " LIKE ?
                    OR user_option_value.userOption" . $option->optionID . " = ?
                )",
                [
                    "%\n{$escapedEntry}\n%",
                    "%\n{$escapedEntry}",
                    "{$escapedEntry}\n%",
                    $entry,
                ]
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function checkUser(User $user, Option $option, $value)
    {
        if (!\is_array($value) || empty($value)) {
            return false;
        }

        $optionValues = \explode("\n", $user->getUserOption($option->optionName));

        // check if the user has selected all options in $value array
        return \count(\array_intersect($value, $optionValues)) === \count($value);
    }

    /**
     * @inheritDoc
     */
    public function getConditionData(Option $option, $newValue)
    {
        return $newValue;
    }
}
