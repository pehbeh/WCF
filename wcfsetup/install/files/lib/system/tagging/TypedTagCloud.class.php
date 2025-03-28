<?php

namespace wcf\system\tagging;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\tolerant\TagCloudCache;

/**
 * This class provides the function to filter the tag cloud by object types.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class TypedTagCloud extends TagCloud
{
    /**
     * object type ids
     * @var int[]
     */
    protected $objectTypeIDs = [];

    /**
     * Constructs a new TypedTagCloud object.
     *
     * @param string $objectType
     * @param int[] $languageIDs
     */
    public function __construct($objectType, array $languageIDs = [])
    {
        $objectTypeObj = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.tagging.taggableObject', $objectType);
        $this->objectTypeIDs[] = $objectTypeObj->objectTypeID;

        parent::__construct($languageIDs);
    }

    /**
     * Loads the tag cloud cache.
     *
     * @return void
     */
    protected function loadCache()
    {
        $this->tags = (new TagCloudCache($this->objectTypeIDs, $this->languageIDs))->getCache();
    }
}
