<?php

namespace wcf\system\gridView\renderer;

use wcf\system\cache\runtime\AbstractRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\util\StringUtil;

/**
 * Formats the content of a column as a user link. The value of the column must be a user id.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class UserLinkColumnRenderer extends ObjectLinkColumnRenderer
{
    public function __construct(
        string $controllerClass = '',
        array $parameters = [],
        string $titleLanguageItem = '',
        public readonly string $fallbackValue = 'username'
    ) {
        parent::__construct($controllerClass, $parameters, $titleLanguageItem);
    }

    #[\Override]
    protected function getRuntimeCache(): AbstractRuntimeCache
    {
        return UserRuntimeCache::getInstance();
    }

    #[\Override]
    public function render(mixed $value, mixed $context = null): string
    {
        if ($value) {
            return parent::render($value);
        }

        if ($this->fallbackValue) {
            if (\is_array($context)) {
                return StringUtil::encodeHTML($context[$this->fallbackValue] ?? '');
            } else {
                return StringUtil::encodeHTML($context->{$this->fallbackValue} ?? '');
            }
        }

        return '';
    }
}
