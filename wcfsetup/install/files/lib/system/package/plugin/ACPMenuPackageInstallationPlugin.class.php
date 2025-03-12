<?php

namespace wcf\system\package\plugin;

use wcf\data\acp\menu\item\ACPMenuItemEditor;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\form\builder\container\IFormContainer;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IconFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\validation\FormFieldValidatorUtil;
use wcf\system\form\builder\IFormDocument;

/**
 * Installs, updates and deletes ACP menu items.
 *
 * @author  Alexander Ebert, Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ACPMenuPackageInstallationPlugin extends AbstractMenuPackageInstallationPlugin implements
    IGuiPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $className = ACPMenuItemEditor::class;

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data)
    {
        $returnValue = parent::prepareImport($data);

        $returnValue['icon'] = $data['elements']['icon'] ?? '';

        return $returnValue;
    }

    /**
     * @inheritDoc
     */
    protected function getElement(\DOMXPath $xpath, array &$elements, \DOMElement $element)
    {
        if ($element->tagName === 'icon') {
            $solid = $element->getAttribute('solid');
            $elements[$element->tagName] = \sprintf(
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
     * @since   3.0
     */
    public static function getDefaultFilename()
    {
        return 'acpMenu.xml';
    }

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function getXsdFilename()
    {
        return 'acpMenu';
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

        // add menu item icon form field

        $parentMenuItemFormField = $form->getFormField('parentMenuItem');

        $menuItemLevels = ['' => 0] + $this->getMenuStructureData()['levels'];

        // icons are only available for menu items on the first or fourth level
        // thus the parent menu item must be on zeroth level (no parent menu item)
        // or on the third level
        $iconParentMenuItems = \array_keys(\array_filter($menuItemLevels, static function ($value) {
            return $value === 0 || $value == 3;
        }));

        $dataContainer->appendChild(
            IconFormField::create('icon')
                ->label('wcf.acp.pip.acpMenu.icon')
                ->description('wcf.acp.pip.acpMenu.icon.description')
                ->required()
                ->addDependency(
                    ValueFormFieldDependency::create('parentMenuItem')
                        ->field($parentMenuItemFormField)
                        ->values($iconParentMenuItems)
                )
        );

        // add additional data to default fields

        $menuItemFormField = $form->getFormField('menuItem');
        $menuItemFormField
            ->description('wcf.acp.pip.acpMenu.menuItem.description')
            ->addValidator(FormFieldValidatorUtil::getRegularExpressionValidator(
                '[a-z]+\.acp\.menu\.link(\.[A-z0-9])+',
                'wcf.acp.pip.acpMenu.menuItem'
            ));

        $menuItemControllerFormField = $form->getFormField('menuItemController');
        $menuItemControllerFormField->addValidator(new FormFieldValidator(
            'acpController',
            static function (TextFormField $formField) {
                // the controller must be an ACP controller
                if (
                    $formField->getSaveValue() !== ''
                    && !\preg_match("~^[a-z]+\\\\acp\\\\~", $formField->getSaveValue())
                ) {
                    $formField->addValidationError(
                        new FormFieldValidationError(
                            'noAcpController',
                            'wcf.acp.pip.acpMenu.menuItemController.error.noAcpController'
                        )
                    );
                }
            }
        ));

        // add dependencies to default fields

        // menu items on the first and second level do not support links,
        // thus the parent menu item must be at least on the second level
        // for the menu item to support links
        $menuItemsSupportingLinks = \array_keys(\array_filter($menuItemLevels, static function ($menuItemLevel) {
            return $menuItemLevel >= 2;
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
     * @param bool $saveData
     * @return array<string, int|string>
     * @since   5.2
     */
    protected function fetchElementData(\DOMElement $element, $saveData)
    {
        $data = parent::fetchElementData($element, $saveData);

        $icon = $element->getElementsByTagName('icon')->item(0);
        if ($icon !== null && !\str_starts_with($icon->nodeValue, 'fa-')) {
            \assert($icon instanceof \DOMElement);
            $solid = $icon->getAttribute('solid') === 'true';

            $data['icon'] = \sprintf(
                '%s;%s',
                $icon->nodeValue,
                $solid ? 'true' : 'false'
            );
        } else {
            $data['icon'] = '';
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @return \DOMElement
     * @since   5.2
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form)
    {
        $menuItem = parent::prepareXmlElement($document, $form);

        $this->appendElementChildren($menuItem, ['icon' => null], $form);

        $icon = $menuItem->getElementsByTagName('icon')->item(0);
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
