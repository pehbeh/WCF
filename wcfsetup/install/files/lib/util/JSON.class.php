<?php

namespace wcf\util;

use wcf\system\exception\SystemException;

/**
 * Provides methods for JSON.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class JSON
{
    /**
     * Returns the JSON representation of a value.
     */
    public static function encode(mixed $data, int $options = 0): string
    {
        return \json_encode($data, $options);
    }

    /**
     * Decodes a JSON string.
     *
     * @return mixed[]
     * @throws  SystemException
     */
    public static function decode(string $json, bool $asArray = true): array
    {
        $data = @\json_decode($json, $asArray);

        if ($data === null && self::getLastError() !== \JSON_ERROR_NONE) {
            throw new SystemException(\sprintf(
                'Could not decode JSON (error %d): %s',
                self::getLastError(),
                StringUtil::truncate($json, 250, StringUtil::HELLIP, true)
            ));
        }

        return $data;
    }

    /**
     * Returns the last error occurred.
     */
    public static function getLastError(): int
    {
        return \json_last_error();
    }

    /**
     * Forbid creation of JSON objects.
     */
    private function __construct()
    {
        // does nothing
    }
}
