<?php

namespace wcf\system\form\builder\container;

use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\language\LanguageFactory;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class MultilingualFormContainer extends FormContainer
{
    public readonly TabMenuFormContainer $tabContainer;

    public function __construct()
    {
        parent::__construct();

        $this->appendChild(
            BooleanFormField::create('isMultilingual')
                ->label('wcf.global.isMultilingual')
                ->available(\count(LanguageFactory::getInstance()->getLanguages()) > 1)
        );

        $this->tabContainer = TabMenuFormContainer::create('languages')
            ->addDependency(
                NonEmptyFormFieldDependency::create('isMultilingual')
                    ->fieldId('isMultilingual')
            )
            ->available(\count(LanguageFactory::getInstance()->getLanguages()) > 1);
        $this->appendChild($this->tabContainer);
    }

    #[\Override]
    public static function create($id): static
    {
        $formField = (new static())->id($id);

        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $tab = TabFormContainer::create("{$formField->getId()}_language_{$language->languageCode}")
                ->label($language->languageName)
                ->appendChildren([
                    FormContainer::create("{$formField->getId()}_{$language->languageCode}"),
                ]);

            $formField->tabContainer->appendChild($tab);
        }

        return $formField;
    }

    /**
     * Returns a map of language codes to their respective tab containers.
     *
     * @return array<string, TabFormContainer>
     */
    public function getLangaugeTabs(): array
    {
        $result = [];
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $tab = $this->tabContainer->getNodeById("{$this->getId()}_language_{$language->languageCode}");
            \assert($tab instanceof TabFormContainer);

            $result[$language->languageCode] = $tab;
        }

        return $result;
    }

    /**
     * Returns a map of language codes to their respective form containers in the tab.
     *
     * @return array<string, FormContainer>
     */
    public function getLangaugeContainers(): array
    {
        $result = [];
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $container = $this->tabContainer->getNodeById("{$this->getId()}_{$language->languageCode}");
            \assert($container instanceof FormContainer);

            $result[$language->languageCode] = $container;
        }

        return $result;
    }
}
