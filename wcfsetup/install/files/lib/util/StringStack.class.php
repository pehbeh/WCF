<?php

namespace wcf\util;

/**
 * Replaces quoted strings in a text.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class StringStack
{
    /**
     * hash index
     */
    private static int $i = 0;

    /**
     * local string stack
     * @var array<string, array<string, string>>
     */
    private static array $stringStack = [];

    /**
     * Replaces a string with an unique hash value.
     */
    public static function pushToStringStack(
        string $string,
        string $type = 'default',
        string $delimiter = '@@'
    ): string {
        self::$i++;
        $hash = $delimiter . StringUtil::getRandomID() . $delimiter;

        if (!isset(self::$stringStack[$type])) {
            self::$stringStack[$type] = [];
        }

        self::$stringStack[$type][$hash] = $string;

        return $hash;
    }

    /**
     * Reinserts strings that have been replaced by unique hash values.
     */
    public static function reinsertStrings(string $string, string $type = 'default'): string
    {
        if (isset(self::$stringStack[$type])) {
            foreach (self::$stringStack[$type] as $hash => $value) {
                if (\str_contains($string, $hash)) {
                    $string = \str_replace($hash, $value, $string);
                    unset(self::$stringStack[$type][$hash]);
                }
            }
        }

        return $string;
    }

    /**
     * Returns the stack.
     *
     * @return array<string, string>
     */
    public static function getStack(string $type = 'default'): array
    {
        if (isset(self::$stringStack[$type])) {
            return self::$stringStack[$type];
        }

        return [];
    }

    /**
     * Forbid creation of StringStack objects.
     */
    private function __construct()
    {
        // does nothing
    }
}
