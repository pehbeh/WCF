<?php

namespace wcf\system\gridView;

use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\gridView\filter\IGridViewFilter;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\IColumnRenderer;
use wcf\system\gridView\renderer\ILinkColumnRenderer;
use wcf\system\gridView\renderer\TitleColumnRenderer;
use wcf\system\WCF;

/**
 * Represents a column of a grid view.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class GridViewColumn
{
    /**
     * @var IColumnRenderer[]
     */
    private array $renderer = [];
    private string $label = '';
    private static DefaultColumnRenderer $defaultRenderer;
    private bool $sortable = false;
    private string $sortById = '';
    private ?IGridViewFilter $filter = null;
    private bool $hidden = false;
    private bool $valueEncoding = true;

    private function __construct(private readonly string $id) {}

    /**
     * Creates a new column with the given id.
     */
    public static function for(string $id): static
    {
        return new static($id);
    }

    /**
     * Renders the column with the given value.
     */
    public function render(mixed $value, mixed $context = null): string
    {
        if ($this->getRenderers() === []) {
            return self::getDefaultRenderer()->render($value, $context);
        }

        foreach ($this->getRenderers() as $renderer) {
            $value = $renderer->render($value, $context);
        }

        return $value;
    }

    /**
     * Returns the css classes of this column.
     */
    public function getClasses(): string
    {
        if ($this->getRenderers() === []) {
            return self::getDefaultRenderer()->getClasses();
        }

        return \implode(' ', \array_map(
            static function (IColumnRenderer $renderer) {
                return $renderer->getClasses();
            },
            $this->getRenderers()
        ));
    }

    /**
     * Sets the renderer of this column.
     */
    public function renderer(array|IColumnRenderer $renderers): static
    {
        if (!\is_array($renderers)) {
            $renderers = [$renderers];
        }

        foreach ($renderers as $renderer) {
            \assert($renderer instanceof IColumnRenderer);
            $this->renderer[] = $renderer;
        }

        return $this;
    }

    /**
     * Sets the label of this column.
     */
    public function label(string $languageItem): static
    {
        $this->label = WCF::getLanguage()->get($languageItem);

        return $this;
    }

    /**
     * Sets the sortable state of this column.
     */
    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Defines the ID by which this column is to be sorted.
     */
    public function sortById(string $id): static
    {
        $this->sortById = $id;

        return $this;
    }

    /**
     * Returns the renderers of this column.
     * @return IColumnRenderer[]
     */
    public function getRenderers(): array
    {
        return $this->renderer;
    }

    /**
     * Returns the id of this column.
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Returns the label of this column.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Returns true if this column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Returns the ID by which this column is to be sorted.
     */
    public function getSortById(): string
    {
        return $this->sortById;
    }

    /**
     * Sets a filter for this column.
     */
    public function filter(?IGridViewFilter $filter): static
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Returns the filter of this column.
     */
    public function getFilter(): ?IGridViewFilter
    {
        return $this->filter;
    }

    /**
     * Returns the filter form field of this column.
     */
    public function getFilterFormField(): AbstractFormField
    {
        if ($this->getFilter() === null) {
            throw new \LogicException('This column has no filter.');
        }

        return $this->getFilter()->getFormField($this->getID(), $this->getLabel());
    }

    /**
     * Returns true if this column is a title column.
     */
    public function isTitleColumn(): bool
    {
        foreach ($this->getRenderers() as $renderer) {
            if ($renderer instanceof TitleColumnRenderer) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the hidden state of this column.
     */
    public function hidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Returns true if this column is hidden.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Determines whether the value of this column should be encoded before rendering.
     */
    public function valueEncoding(bool $valueEncoding = true): static
    {
        $this->valueEncoding = $valueEncoding;

        return $this;
    }

    /**
     * Returns true if the value of this column should be encoded before rendering.
     */
    public function encodeValue(): bool
    {
        return $this->valueEncoding;
    }

    /**
     * Returns true if the row link should be applied to this column.
     */
    public function applyRowLink(): bool
    {
        return \count(\array_filter(
            $this->getRenderers(),
            fn(IColumnRenderer $renderer) => $renderer instanceof ILinkColumnRenderer
        )) === 0;
    }

    /**
     * Returns the default renderer for the rendering of columns.
     */
    private static function getDefaultRenderer(): DefaultColumnRenderer
    {
        if (!isset(self::$defaultRenderer)) {
            self::$defaultRenderer = new DefaultColumnRenderer();
        }

        return self::$defaultRenderer;
    }
}
