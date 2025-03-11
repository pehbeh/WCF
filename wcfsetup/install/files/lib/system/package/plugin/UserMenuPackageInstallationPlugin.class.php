<?php

namespace wcf\system\package\plugin;

use wcf\data\user\menu\item\UserMenuItemEditor;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\form\builder\container\IFormContainer;
use wcf\system\form\builder\field\ClassNameFormField;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IconFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidatorUtil;
use wcf\system\form\builder\IFormDocument;
use wcf\system\menu\user\IUserMenuItemProvider;

/**
 * Installs, updates and deletes user menu items.
 *
 * @author  Alexander Ebert, Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserMenuPackageInstallationPlugin extends AbstractMenuPackageInstallationPlugin implements
    IGuiPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $className = UserMenuItemEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'user_menu_item';

    /**
     * @inheritDoc
     */
    public $tagName = 'usermenuitem';

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data)
    {
        $result = parent::prepareImport($data);

        // class name
        if (!empty($data['elements']['classname'])) {
            $result['className'] = $data['elements']['classname'];
        }

        if (isset($data['elements']['iconClassName'])) {
            $result['iconClassName'] = $data['elements']['iconClassName'];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function getElement(\DOMXPath $xpath, array &$elements, \DOMElement $element)
    {
        if ($element->tagName === 'iconclassname') {
            $solid = $element->getAttribute('solid');
            $elements['iconClassName'] = \sprintf(
                "%s;%s",
                $element->nodeValue,
                $solid === 'true' ? 'true' : 'false'
            );
        } else {
            $elements[$element->tagName] = $element->nodeValue;
        }
    }

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function addFormFields(IFormDocument $form)
    {
        parent::addFormFields($form);

        /** @var IFormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        // add menu item className form field

        $classNameFormField = ClassNameFormField::create()
            ->objectProperty('classname')
            ->implementedInterface(IUserMenuItemProvider::class);
        $dataContainer->insertBefore($classNameFormField, 'menuItemController');

        // add menu item icon form field

        $parentMenuItemFormField = $form->getFormField('parentMenuItem');
        $dataContainer->appendChild(IconFormField::create('iconClassName')
            ->objectProperty('iconclassname')
            ->label('wcf.acp.pip.userMenu.iconClassName')
            ->description('wcf.acp.pip.userMenu.iconClassName.description')
            ->required()
            ->addDependency(
                // only first level menu items support icons
                ValueFormFieldDependency::create('parentMenuItem')
                    ->field($parentMenuItemFormField)
                    ->values([''])
            ));

        // add additional data to default fields

        $menuItemFormField = $form->getFormField('menuItem');
        $menuItemFormField
            ->description('wcf.acp.pip.userMenu.menuItem.description')
            ->addValidator(FormFieldValidatorUtil::getRegularExpressionValidator(
                '[a-z]+\.user.menu(\.[A-z0-9])+',
                'wcf.acp.pip.userMenu.menuItem'
            ));

        // add dependencies to default fields

        $menuItemLevels = ['' => 0] + $this->getMenuStructureData()['levels'];

        // menu items on the first and second level do not support links,
        // thus the parent menu item must be at least on the second level
        // for the menu item to support links
        $menuItemsSupportingLinks = \array_keys(\array_filter($menuItemLevels, static function ($menuItemLevel) {
            return $menuItemLevel >= 1;
        }));

        foreach (['menuItemController', 'menuItemLink'] as $nodeId) {
            $formField = $form->getFormField($nodeId);
            $formField->addDependency(
                ValueFormFieldDependency::create('parentMenuItem')
                    ->field($parentMenuItemFormField)
                    ->values($menuItemsSupportingLinks)
            );
        }
    }

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function fetchElementData(\DOMElement $element, $saveData)
    {
        $data = parent::fetchElementData($element, $saveData);

        $className = $element->getElementsByTagName('classname')->item(0);
        if ($className !== null) {
            $data['className'] = $className->nodeValue;
        } elseif ($saveData) {
            $data['className'] = '';
        }

        $icon = $element->getElementsByTagName('iconclassname')->item(0);
        if ($icon !== null && !\str_starts_with($icon->nodeValue, 'fa-')) {
            \assert($icon instanceof \DOMElement);
            $solid = $icon->getAttribute('solid') === 'true';

            $data['iconClassName'] = \sprintf(
                '%s;%s',
                $icon->nodeValue,
                $solid ? 'true' : 'false'
            );
        } elseif ($saveData) {
            $data['iconClassName'] = '';
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form)
    {
        $menuItem = parent::prepareXmlElement($document, $form);

        $this->appendElementChildren(
            $menuItem,
            [
                'classname' => '',
                'iconclassname' => '',
            ],
            $form
        );

        $icon = $menuItem->getElementsByTagName('iconclassname')->item(0);
        if ($icon !== null) {
            \assert($icon instanceof \DOMElement);

            [$name, $solid] = \explode(';', $icon->textContent, 2);
            if ($solid === 'true') {
                $icon->setAttribute('solid', 'true');
            }

            $icon->textContent = $name;
        }

        return $menuItem;
    }
}
