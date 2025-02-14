<?php

namespace wcf\system\language;

use wcf\data\language\Language;
use wcf\data\language\LanguageEditor;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\SystemException;
use wcf\system\Regex;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Provides internationalization support for input fields.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class I18nHandler extends SingletonFactory
{
    /**
     * list of available languages
     * @var Language[]
     */
    protected $availableLanguages = [];

    /**
     * list of element ids
     * @var string[]
     */
    protected $elementIDs = [];

    /**
     * list of plain values for elements
     * @var string[]
     */
    protected $plainValues = [];

    /**
     * i18n values for elements
     * @var string[][]
     */
    protected $i18nValues = [];

    /**
     * element options
     * @var mixed[][]
     */
    protected $elementOptions = [];

    /**
     * language variable regex object
     * @var Regex
     */
    protected $regex;

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->availableLanguages = LanguageFactory::getInstance()->getLanguages();
    }

    /**
     * Registers a new element id, returns false if element id is already set.
     */
    public function register(string $elementID): bool
    {
        if (\in_array($elementID, $this->elementIDs)) {
            return false;
        }

        $this->elementIDs[] = $elementID;

        return true;
    }

    /**
     * Unregisters the element with the given id.
     *
     * Does nothing if no such element exists.
     *
     * @since   5.2
     */
    public function unregister(string $elementID): void
    {
        $index = \array_search($elementID, $this->elementIDs);
        if ($index !== false) {
            unset($this->elementIDs[$index]);
        }

        unset($this->plainValues[$elementID], $this->i18nValues[$elementID]);
    }

    /**
     * Reads plain and i18n values from request data.
     *
     * @param $requestData used request data (if `null`, `$_POST` is used)
     * @throws  SystemException
     */
    public function readValues(?array $requestData = null): void
    {
        if ($requestData === null) {
            $requestData = $_POST;
        }

        foreach ($this->elementIDs as $elementID) {
            if (isset($requestData[$elementID])) {
                // you should trim the string before using it; prevents unwanted newlines
                $this->plainValues[$elementID] = StringUtil::unifyNewlines(StringUtil::trim($requestData[$elementID]));
                continue;
            }

            $i18nElementID = $elementID . '_i18n';
            if (isset($requestData[$i18nElementID]) && \is_array($requestData[$i18nElementID])) {
                $this->i18nValues[$elementID] = [];

                foreach ($requestData[$i18nElementID] as $languageID => $value) {
                    $this->i18nValues[$elementID][$languageID] = StringUtil::unifyNewlines(StringUtil::trim($value));
                }

                continue;
            }

            throw new SystemException("Missing expected value for element id '" . $elementID . "'");
        }
    }

    /**
     * Returns true if given element has disabled i18n functionality.
     */
    public function isPlainValue(string $elementID): bool
    {
        if (isset($this->plainValues[$elementID])) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if given element has enabled i18n functionality.
     */
    public function hasI18nValues(string $elementID): bool
    {
        if (isset($this->i18nValues[$elementID])) {
            return true;
        }

        return false;
    }

    /**
     * Returns the plain value for the given element.
     *
     * @see     \wcf\system\language\I18nHandler::isPlainValue()
     */
    public function getValue(string $elementID): string
    {
        return $this->plainValues[$elementID];
    }

    /**
     * Returns the values for the given element. If the element is multilingual,
     * the multilingual values are returned, otherwise the plain value is
     * returned for each language id.
     *
     * @return  string[]
     */
    public function getValues(string $elementID): array
    {
        if ($this->hasI18nValues($elementID)) {
            return $this->i18nValues[$elementID];
        }

        $plainValue = $this->getValue($elementID);

        $values = [];
        foreach ($this->availableLanguages as $language) {
            $values[$language->languageID] = $plainValue;
        }

        return $values;
    }

    /**
     * Sets the value for the given element. If the element is multilingual,
     * the given value is set for every available language.
     *
     * @param $forceAsPlainValue if `true`, the value is added as a plain value in any case
     */
    public function setValue(string $elementID, string $plainValue, bool $forceAsPlainValue = false): void
    {
        if (!$this->isPlainValue($elementID) && !$forceAsPlainValue) {
            $i18nValues = [];
            foreach ($this->availableLanguages as $language) {
                $i18nValues[$language->languageID] = StringUtil::trim($plainValue);
            }
            $this->setValues($elementID, $i18nValues);
        } else {
            $this->plainValues[$elementID] = StringUtil::trim($plainValue);
        }
    }

    /**
     * Sets the values for the given element. If the element is not multilingual,
     * use I18nHandler::setValue() instead.
     *
     * @param string[] $i18nValues
     * @throws  SystemException
     */
    public function setValues(string $elementID, array $i18nValues): void
    {
        if (empty($i18nValues)) {
            throw new SystemException(
                'Invalid argument for parameter $i18nValues',
                0,
                'Expected filled array as second argument. Empty array given.'
            );
        }
        if (!$this->isPlainValue($elementID)) {
            $this->i18nValues[$elementID] = $i18nValues;
        } else {
            $plainValue = \array_shift($i18nValues);
            $this->setValue($elementID, $plainValue);
        }
    }

    /**
     * Returns true if the value with the given id is valid.
     */
    public function validateValue(string $elementID, bool $requireI18n = false, bool $permitEmptyValue = false): bool
    {
        // do not force i18n if only one language is available
        if ($requireI18n && \count($this->availableLanguages) == 1) {
            $requireI18n = false;
        }

        if ($this->isPlainValue($elementID)) {
            // plain values may be left empty
            if ($permitEmptyValue) {
                return true;
            }

            if ($requireI18n || $this->getValue($elementID) == '') {
                return false;
            }
        } elseif ($requireI18n && (!isset($this->i18nValues[$elementID]) || empty($this->i18nValues[$elementID]))) {
            return false;
        } else {
            foreach ($this->availableLanguages as $language) {
                if (!isset($this->i18nValues[$elementID][$language->languageID])) {
                    return false;
                }

                if (!$permitEmptyValue && empty($this->i18nValues[$elementID][$language->languageID])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Saves language variable for i18n.
     *
     * @param string|string[] $elementID either the id of the element or externally passed array `languageID => value`
     */
    public function save($elementID, string $languageVariable, string $languageCategory, int $packageID = PACKAGE_ID)
    {
        LanguageEditor::validateItemName($languageVariable, $languageCategory);

        // get language category id
        $sql = "SELECT  languageCategoryID
                FROM    wcf1_language_category
                WHERE   languageCategory = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$languageCategory]);
        $row = $statement->fetchArray();
        $languageCategoryID = $row['languageCategoryID'];

        if (\count($this->availableLanguages) == 1) {
            $languageIDs = \array_keys($this->availableLanguages);
        } else {
            if (\is_array($elementID)) {
                $languageIDs = \array_keys($elementID);
            } else {
                $languageIDs = \array_keys($this->i18nValues[$elementID]);
            }
        }

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add("languageID IN (?)", [$languageIDs]);
        $conditions->add("languageItem = ?", [$languageVariable]);

        $sql = "SELECT  languageItemID, languageID
                FROM    wcf1_language_item
                {$conditions}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditions->getParameters());
        $languageItemIDs = $statement->fetchMap('languageID', 'languageItemID');

        $insertLanguageIDs = $updateLanguageIDs = [];
        foreach ($languageIDs as $languageID) {
            if (isset($languageItemIDs[$languageID])) {
                $updateLanguageIDs[] = $languageID;
            } else {
                $insertLanguageIDs[] = $languageID;
            }
        }

        // insert language items
        if (!empty($insertLanguageIDs)) {
            $sql = "INSERT INTO wcf1_language_item
                                (languageID, languageItem, languageItemValue, languageItemOriginIsSystem, languageCategoryID, packageID)
                    VALUES      (?, ?, ?, ?, ?, ?)";
            $statement = WCF::getDB()->prepare($sql);

            foreach ($insertLanguageIDs as $languageID) {
                if (\is_array($elementID)) {
                    $value = $elementID[$languageID];
                } elseif (isset($this->i18nValues[$elementID])) {
                    $value = $this->i18nValues[$elementID][$languageID];
                } else {
                    $value = $this->plainValues[$elementID];
                }

                $statement->execute([
                    $languageID,
                    $languageVariable,
                    $value,
                    0,
                    $languageCategoryID,
                    $packageID,
                ]);
            }
        }

        // update language items
        if (!empty($updateLanguageIDs)) {
            $sql = "UPDATE  wcf1_language_item
                    SET     languageItemValue = ?,
                            languageItemOriginIsSystem = ?
                    WHERE   languageItemID = ?";
            $statement = WCF::getDB()->prepare($sql);

            foreach ($updateLanguageIDs as $languageID) {
                if (\is_array($elementID)) {
                    $value = $elementID[$languageID];
                } elseif (isset($this->i18nValues[$elementID])) {
                    $value = $this->i18nValues[$elementID][$languageID];
                } else {
                    $value = $this->plainValues[$elementID];
                }

                $statement->execute([
                    $value,
                    0,
                    $languageItemIDs[$languageID],
                ]);
            }
        }

        // reset language cache
        LanguageFactory::getInstance()->deleteLanguageCache();
    }

    /**
     * Removes previously created i18n language variables.
     */
    public function remove(string $languageVariable): void
    {
        $sql = "DELETE FROM wcf1_language_item
                WHERE       languageItem = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$languageVariable]);

        // reset language cache
        LanguageFactory::getInstance()->deleteLanguageCache();
    }

    /**
     * Sets additional options for elements, required if updating values.
     *
     * @param int $elementID
     */
    public function setOptions($elementID, int $packageID, string $value, string $pattern): void
    {
        $this->elementOptions[$elementID] = [
            'packageID' => $packageID,
            'pattern' => $pattern,
            'value' => $value,
        ];
    }

    /**
     * Assigns element values to template. Using request data once reading
     * initial database data is explicitly disallowed.
     */
    public function assignVariables(bool $useRequestData = true): void
    {
        $elementValues = [];
        $elementValuesI18n = [];

        foreach ($this->elementIDs as $elementID) {
            $value = '';
            $i18nValues = [];

            // use POST values instead of querying database
            if ($useRequestData) {
                if ($this->isPlainValue($elementID)) {
                    $value = $this->getValue($elementID);
                } else {
                    if ($this->hasI18nValues($elementID)) {
                        $i18nValues = $this->i18nValues[$elementID];
                        // encoding the entries for javascript
                        foreach ($i18nValues as $languageID => $value) {
                            $i18nValues[$languageID] = StringUtil::encodeJS(StringUtil::unifyNewlines($value));
                        }
                    } else {
                        $i18nValues = [];
                    }
                }
            } else {
                $isI18n = Regex::compile('^' . $this->elementOptions[$elementID]['pattern'] . '$')
                    ->match($this->elementOptions[$elementID]['value']);
                if (!$isI18n) {
                    // check if it's a regular language variable
                    $isI18n = Regex::compile('^([a-zA-Z0-9-_]+\.)+[a-zA-Z0-9-_]+$')
                        ->match($this->elementOptions[$elementID]['value']);
                }

                if ($isI18n) {
                    // use i18n values from language items
                    $sql = "SELECT  languageID, languageItemValue
                            FROM    wcf1_language_item
                            WHERE   languageItem = ?";
                    $statement = WCF::getDB()->prepare($sql);
                    $statement->execute([
                        $this->elementOptions[$elementID]['value'],
                    ]);
                    while ($row = $statement->fetchArray()) {
                        $languageItemValue = StringUtil::unifyNewlines($row['languageItemValue']);
                        $i18nValues[$row['languageID']] = StringUtil::encodeJS($languageItemValue);

                        if ($row['languageID'] == LanguageFactory::getInstance()->getDefaultLanguageID()) {
                            $value = $languageItemValue;
                        }
                    }

                    // item appeared to be a language item but either is not or does not exist
                    if (empty($i18nValues) && empty($value)) {
                        $value = $this->elementOptions[$elementID]['value'];
                    }
                } else {
                    // use data provided by setOptions()
                    $value = $this->elementOptions[$elementID]['value'];
                }
            }

            $elementValues[$elementID] = $value;
            $elementValuesI18n[$elementID] = $i18nValues;
        }

        WCF::getTPL()->assign([
            'availableLanguages' => $this->availableLanguages,
            'i18nPlainValues' => $elementValues,
            'i18nValues' => $elementValuesI18n,
        ]);
    }

    /**
     * Resets internally stored data after creating a new object through a form.
     */
    public function reset(): void
    {
        $this->i18nValues = $this->plainValues = [];
    }

    /**
     * Returns true if given string equals a language variable.
     */
    protected function isLanguageVariable(string $string): bool
    {
        if ($this->regex === null) {
            $this->regex = new Regex('^([a-zA-Z0-9-_]+\.)+[a-zA-Z0-9-_]+$');
        }

        return !!$this->regex->match($string);
    }
}
