<?php

namespace wcf\event\form\option;

use wcf\event\IPsr14Event;
use wcf\system\form\option\IFormOption;

/**
 * Requests the collection of supported form options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class FormOptionCollecting implements IPsr14Event
{
    /**
     * @var array<string, IFormOption>
     */
    private array $options = [];

    /**
     * Registers a new form option.
     */
    public function register(IFormOption $option): void
    {
        $this->options[$option->getId()] = $option;
    }

    /**
     * @return array<string, IFormOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
