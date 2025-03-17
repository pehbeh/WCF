<?php

namespace wcf\util;

/**
 * Contains option-related functions.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class OptionUtil
{
    /**
     * Returns a list of the available options.
     *
     * @return array<string, string>
     */
    public static function parseSelectOptions(string $selectOptions): array
    {
        $result = [];
        $options = \explode("\n", StringUtil::trim(StringUtil::unifyNewlines($selectOptions)));
        foreach ($options as $option) {
            $key = $value = $option;
            if (\str_contains($option, ':')) {
                $optionData = \explode(':', $option);
                $key = \array_shift($optionData);
                $value = \implode(':', $optionData);
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Returns a list of the enable options.
     *
     * @return array<string, string>
     */
    public static function parseMultipleEnableOptions(string $enableOptions): array
    {
        $result = [];
        if (!empty($enableOptions)) {
            $options = \explode("\n", StringUtil::trim(StringUtil::unifyNewlines($enableOptions)));
            $key = -1;
            foreach ($options as $option) {
                if (\str_contains($option, ':')) {
                    $optionData = \explode(':', $option);
                    $key = \array_shift($optionData);
                    $value = \implode(':', $optionData);
                } else {
                    $key++;
                    $value = $option;
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Forbid creation of OptionUtil objects.
     */
    private function __construct()
    {
        // does nothing
    }
}
