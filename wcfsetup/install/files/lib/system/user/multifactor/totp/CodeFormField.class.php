<?php

namespace wcf\system\user\multifactor\totp;

use wcf\system\form\builder\field\TDefaultIdFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\user\multifactor\Helper;

/**
 * Handles the input of a TOTP code.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2020 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.4
 */
final class CodeFormField extends TextFormField
{
    use TDefaultIdFormField;

    /**
     * @var ?int
     */
    protected $minCounter;

    public function __construct()
    {
        $this->minimumLength(Totp::CODE_LENGTH);
        $this->maximumLength(Totp::CODE_LENGTH);
        $this->fieldAttribute('size', (string)Totp::CODE_LENGTH);
        $this->addFieldClass('multifactorTotpCode');
        $this->autoComplete('off');
        $this->inputMode('numeric');
        $this->pattern('[0-9]*');

        $placeholder = '';
        $gen = Helper::digitStream();
        for ($i = 0; $i < $this->getMinimumLength(); $i++) {
            $placeholder .= $gen->current();
            $gen->next();
        }
        $this->placeholder($placeholder);
    }

    /**
     * Used to carry the minCounter value along.
     *
     * @return $this
     */
    public function minCounter(int $minCounter): static
    {
        $this->minCounter = $minCounter;

        return $this;
    }

    /**
     * @inheritDoc
     * @return array{value: mixed, minCounter: number}
     */
    public function getSaveValue(): array
    {
        if ($this->minCounter === null) {
            throw new \BadMethodCallException('No minCounter was set. Did you validate this field?');
        }

        return [
            'value' => $this->getValue(),
            'minCounter' => $this->minCounter,
        ];
    }

    /**
     * @inheritDoc
     */
    protected static function getDefaultId(): string
    {
        return 'onetimecode';
    }
}
