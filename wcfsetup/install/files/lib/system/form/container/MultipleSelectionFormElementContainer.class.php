<?php

namespace wcf\system\form\container;

/**
 * Provides a multiple selection form element container.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class MultipleSelectionFormElementContainer extends SelectionFormElementContainer
{
    /**
     * container value
     * @var string[]
     */
    protected $value = [];

    /**
     * Sets container value.
     *
     * @param string[] $value
     * @return void
     */
    public function setValue(array $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function getHTML($formName)
    {
        $content = '';
        foreach ($this->getChildren() as $element) {
            $content .= '<dd>' . $element->getHTML($formName) . '</dd>';
        }

        return <<<HTML
<section class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{$this->getLabel()}</h2>
		<p class="sectionDescription">{$this->getDescription()}</p>
	</header>

	<dl class="wide">
		{$content}
	</dl>
</section>
HTML;
    }
}
