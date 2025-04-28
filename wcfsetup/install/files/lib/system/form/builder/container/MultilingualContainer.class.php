<?php

namespace wcf\system\form\builder\container;

use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\IFormField;
use wcf\system\language\LanguageFactory;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class MultilingualContainer extends FormContainer
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
     * @template T of IFormField
     *
     * @param class-string<T> $fieldClass
     * @param string $id
     * @param null|callable(T): void $callback
     *
     * @return self
     */
    public function appendMultilingualFormField(string $fieldClass, string $id, ?callable $callback = null): self
    {
        $field = \call_user_func([$fieldClass, "create"], $id);
        /** @var T $field */
        $field->addDependency(
            EmptyFormFieldDependency::create('isMultilingual')
                ->fieldId('isMultilingual')
        );
        $this->appendChild($field);

        if ($callback !== null) {
            $callback($field);
        }

        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $tab = $this->tabContainer->getNodeById("{$this->getId()}_language_{$language->languageCode}");
            \assert($tab instanceof TabFormContainer);

            $field = \call_user_func([$fieldClass, "create"], "{$id}_{$language->languageCode}");
            /** @var T $field */
            if ($field instanceof IFormContainer) {
                $tab->appendChild($field);
            } else {
                $container = $this->tabContainer->getNodeById("{$this->getId()}_{$language->languageCode}");
                \assert($container instanceof FormContainer);

                $container->appendChild($field);
            }

            if ($callback !== null) {
                $callback($field);
            }
        }

        return $this;
    }
}
