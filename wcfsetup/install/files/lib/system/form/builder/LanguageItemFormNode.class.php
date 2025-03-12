<?php

namespace wcf\system\form\builder;

use wcf\system\WCF;

/**
 * Form node that shows a language item without any surrounding HTML code.
 *
 * @author      Matthias Schmidt
 * @copyright   2001-2020 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.4
 */
class LanguageItemFormNode implements IFormChildNode
{
    use TFormChildNode;
    use TFormNode;

    /**
     * language item shown in the form node
     * @var ?string
     */
    protected $languageItem;

    /**
     * template variables passed to the language item
     * @var array<string ,mixed>
     */
    protected $variables = [];

    /**
     * @inheritDoc
     */
    public function getHtml()
    {
        return WCF::getLanguage()->getDynamicVariable($this->getLanguageItem(), $this->getVariables());
    }

    /**
     * Returns the name of the language item shown in the form node.
     *
     * @throws \BadMethodCallException if language item has not been set yet
     */
    public function getLanguageItem(): string
    {
        if ($this->languageItem === null) {
            throw new \BadMethodCallException("Language item has not been set yet for node '{$this->getId()}'.");
        }

        return $this->languageItem;
    }

    /**
     * Returns the template variables passed to the language item.
     *
     * @return array<string, mixed>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Sets the language item shown in the form node and returns this form node.
     *
     * @return static this form field
     */
    public function languageItem(string $languageItem)
    {
        $this->languageItem = $languageItem;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        // does nothing
    }

    /**
     * Sets the template variables passed to the language item and returns this form node.
     *
     * @param array<string, mixed> $variables
     * @return static this form field
     */
    public function variables(array $variables)
    {
        $this->variables = $variables;

        return $this;
    }
}
