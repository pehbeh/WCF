<?php

namespace wcf\system\interaction;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ILinkableObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents an interaction that links to the url returned by `ILinkableObject::getLink()`.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class LinkableObjectInteraction extends AbstractInteraction
{
    public function __construct(
        string $identifier,
        protected readonly string|\Closure $languageItem,
        ?\Closure $isAvailableCallback = null
    ) {
        parent::__construct($identifier, $isAvailableCallback);
    }

    #[\Override]
    public function render(DatabaseObject $object): string
    {
        $href = $this->getLink($object);

        if (\is_string($this->languageItem)) {
            $title = WCF::getLanguage()->get($this->languageItem);
        } else {
            $title = ($this->languageItem)($object);
        }

        return \sprintf('<a href="%s">%s</a>', StringUtil::encodeHTML($href), $title);
    }

    private function getLink(DatabaseObject $object): string
    {
        if ($object instanceof ILinkableObject) {
            return $object->getLink();
        }

        if ($object instanceof DatabaseObjectDecorator) {
            $decoratedObject = $object->getDecoratedObject();
            if ($decoratedObject instanceof ILinkableObject) {
                return $decoratedObject->getLink();
            }
        }

        throw new \BadMethodCallException("LinkableObjectInteraction expects object to be an implementation of ILinkableObject.");
    }
}
