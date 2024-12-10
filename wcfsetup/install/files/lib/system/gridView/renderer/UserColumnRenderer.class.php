<?php

namespace wcf\system\gridView\renderer;

use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\util\StringUtil;

/**
 * Formats the content of a column as a user. The value of the column must be a user id.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class UserColumnRenderer extends DefaultColumnRenderer
{
    public function __construct(
        public readonly string $fallbackValue = 'username'
    ) {}

    #[\Override]
    public function render(mixed $value, mixed $context = null): string
    {
        if (!$value) {
            if ($this->fallbackValue) {
                if (\is_array($context)) {
                    return StringUtil::encodeHTML($context[$this->fallbackValue] ?? '');
                } else {
                    return StringUtil::encodeHTML($context->{$this->fallbackValue} ?? '');
                }
            }

            return '';
        }

        $user = UserRuntimeCache::getInstance()->getObject($value);
        if ($user === null) {
            return '';
        }

        return StringUtil::encodeHTML($user->username);
    }

    #[\Override]
    public function prepare(mixed $value, mixed $context = null): void
    {
        if (!$value) {
            return;
        }

        UserRuntimeCache::getInstance()->cacheObjectID($value);
    }
}
