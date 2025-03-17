<?php

namespace wcf\system\form\builder\field;

/**
 * Represents a form field that supports displaying a suffix.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 */
interface ISuffixedFormField extends IFormField
{
    /**
     * Returns the suffix of this field or `null` if no suffix has been set.
     *
     * @return ?string
     */
    public function getSuffix();

    /**
     * Sets the suffix of this field using the given language item and returns
     * this element. If `null` is passed, the suffix is removed.
     *
     * @param ?string $languageItem language item containing the suffix or `null` to unset suffix
     * @param array<string, mixed> $variables additional variables used when resolving the language item
     * @return static this field
     *
     * @throws \InvalidArgumentException if the given language item is invalid
     */
    public function suffix($languageItem = null, array $variables = []);
}
