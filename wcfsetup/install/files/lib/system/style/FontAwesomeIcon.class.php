<?php

namespace wcf\system\style;

use wcf\system\style\exception\InvalidIconFormat;
use wcf\system\style\exception\InvalidIconSize;
use wcf\system\style\exception\UnknownIcon;

/**
 * Represents an icon for Font Awesome.
 *
 * @author Alexander Ebert
 * @copyright 2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.0
 */
final class FontAwesomeIcon implements IFontAwesomeIcon, \Stringable
{
    public const SIZES = IFontAwesomeIcon::SIZES;

    /**
     * Uses the icon name as the key and a boolean value that
     * indicates that an icon has a non-solid variant.
     *
     * @var array<string, bool>
     */
    private static array $icons;

    private function __construct(
        private readonly string $name,
        private readonly bool $forceSolid
    ) {
        self::validateName($name);
    }

    /**
     * Serializes the icon name and the use of the solid style into a
     * semicolon separated string that is understood by `fromString`.
     */
    public function __toString(): string
    {
        return \sprintf(
            "%s;%s",
            $this->name,
            $this->forceSolid ? 'true' : 'false'
        );
    }

    /**
     * @inheritDoc
     */
    public function toHtml(int $size = 16): string
    {
        if (!\in_array($size, self::SIZES)) {
            throw new InvalidIconSize($size);
        }

        if ($this->forceSolid) {
            return \sprintf(
                '<fa-icon size="%d" name="%s" solid></fa-icon>',
                $size,
                $this->name,
            );
        }

        return \sprintf(
            '<fa-icon size="%d" name="%s"></fa-icon>',
            $size,
            $this->name,
        );
    }

    /**
     * Uses a string containing the two values separated by a semicolon.
     * The first value is the name of the icon and the second value is
     * the string `true` or `false` to force the solid variant.
     *
     * @throws InvalidIconFormat
     */
    public static function fromString(string $iconData): self
    {
        if (!\str_contains($iconData, ';')) {
            throw new InvalidIconFormat();
        }

        [$name, $solid] = \explode(';', $iconData, 2);
        if ($solid !== 'true' && $solid !== 'false') {
            throw new InvalidIconFormat();
        }

        $forceSolid = $solid === 'true';

        return self::fromValues($name, $forceSolid);
    }

    public static function fromValues(string $name, bool $forceSolid = false): self
    {
        return new self($name, $forceSolid);
    }

    public static function isValidString(string $iconData): bool
    {
        if (!\str_contains($iconData, ';')) {
            return false;
        }

        [$name, $solid] = \explode(';', $iconData, 2);
        if ($solid !== 'true' && $solid !== 'false') {
            return false;
        }

        return self::isValidName($name);
    }

    public static function isValidName(string $name): bool
    {
        if (!isset(self::$icons)) {
            self::$icons = require(__DIR__ . '/fa-metadata.php');
        }

        return isset(self::$icons[$name]);
    }

    private static function validateName(string $name): void
    {
        if (self::isValidName($name)) {
            return;
        }

        // Do not throw an exception when the debug mode and developer tools
        // are disabled. This allows unknown icons to be passed to the template
        // which will throw a proper error without bricking the entire page.
        if (!\ENABLE_DEBUG_MODE || !\ENABLE_DEVELOPER_TOOLS) {
            return;
        }

        throw new UnknownIcon($name);
    }
}
