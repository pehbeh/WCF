<?php

namespace wcf\system\form\builder\field;

use wcf\system\WCF;

/**
 * Implementation of a badge color form field for selecting a single color or a custom color.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @since       6.2
 */
final class BadgeColorFormField extends RadioButtonFormField implements IPatternFormField
{
    use TPatternFormField;

    public const AVAILABLE_CSS_CLASSNAMES = [
        'yellow',
        'orange',
        'brown',
        'red',
        'pink',
        'purple',
        'blue',
        'green',
        'black',

        'none', /* not a real value */
        'custom', /* not a real value */
    ];
    /**
     * @inheritDoc
     */
    protected $templateName = 'shared_badgeColorFormField';
    protected ?string $textReferenceNodeId;
    protected string $defaultLabelText;
    protected string $customClassName = '';

    public function __construct()
    {
        $this
            ->addFieldClass('labelSelection__radio')
            ->defaultLabelText(WCF::getLanguage()->get('wcf.acp.label.defaultValue'))
            ->options(\array_combine(self::AVAILABLE_CSS_CLASSNAMES, self::AVAILABLE_CSS_CLASSNAMES))
            ->pattern('^-?[_a-zA-Z]+[_a-zA-Z0-9-]+$');
    }

    public function defaultLabelText(string $text): self
    {
        $this->defaultLabelText = $text;

        return $this;
    }

    public function textReferenceNode(IFormField $field): self
    {
        $this->textReferenceNodeId = $field->getId();

        return $this;
    }

    public function textReferenceNodeId(string $id): self
    {
        $this->textReferenceNodeId = $id;

        return $this;
    }

    public function getDefaultLabelText(): string
    {
        return $this->defaultLabelText;
    }

    public function getTextReferenceNodeId(): ?string
    {
        return $this->textReferenceNodeId;
    }

    public function getCustomClassName(): string
    {
        return $this->customClassName;
    }
}
