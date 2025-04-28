<?php

namespace wcf\system\form\option;

use wcf\event\form\option\FormOptionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\SingletonFactory;

/**
 * Provides the available form options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class FormOptionHandler extends SingletonFactory
{
    /**
     * @var array<string, IFormOption>
     */
    private array $options;

    #[\Override]
    protected function init()
    {
        $this->options = \array_merge($this->getDefaultFormOptions(), $this->getEventFormOptions());
    }

    /**
     * @return array<string, IFormOption>
     */
    private function getDefaultFormOptions(): array
    {
        $options = [];

        foreach (
            [
                new BooleanFormOption(),
                new CheckboxesFormOption(),
                new CurrencyFormOption(),
                new DateFormOption(),
                new EmailFormOption(),
                new FloatFormOption(),
                new IconFormOption(),
                new IntegerFormOption(),
                new RadioButtonFormOption(),
                new RatingFormOption(),
                new SelectFormOption(),
                new SourceCodeFormOption(),
                new TextFormOption(),
                new TextareaFormOption(),
                new UrlFormOption(),
            ] as $defaultOption
        ) {
            $options[$defaultOption->getId()] = $defaultOption;
        }

        return $options;
    }

    /**
     * @return array<string, IFormOption>
     */
    private function getEventFormOptions(): array
    {
        $event = new FormOptionCollecting();
        EventHandler::getInstance()->fire($event);

        return $event->getOptions();
    }

    /**
     * @return array<string, IFormOption>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $identifier): ?IFormOption
    {
        return $this->options[$identifier] ?? null;
    }
}
