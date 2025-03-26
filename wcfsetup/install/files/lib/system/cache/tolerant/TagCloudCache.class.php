<?php

namespace wcf\system\cache\tolerant;

use wcf\data\tag\Tag;
use wcf\data\tag\TagCloudTag;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches the tag cloud.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractTolerantCache<array<string, TagCloudTag>>
 */
class TagCloudCache extends AbstractTolerantCache
{
    public function __construct(
        /** @var int[] */
        public readonly array $objectTypeIDs,
        /** @var int[] */
        public readonly array $languageIDs = [],
    ) {
    }

    private static function compareTags(TagCloudTag $tagA, TagCloudTag $tagB): int
    {
        if ($tagA->counter > $tagB->counter) {
            return -1;
        }
        if ($tagA->counter < $tagB->counter) {
            return 1;
        }

        return 0;
    }

    #[\Override]
    public function getLifetime(): int
    {
        return 3_600;
    }

    #[\Override]
    protected function rebuildCacheData(): array
    {
        if ($this->objectTypeIDs === []) {
            return [];
        }

        return $this->fetchTags($this->fetchTagIDs());
    }

    /**
     * @param list<int> $tagIDs
     *
     * @return array<string, TagCloudTag>
     */
    protected function fetchTags(array $tagIDs): array
    {
        if ($tagIDs === []) {
            return [];
        }

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('tagID IN (?)', [\array_keys($tagIDs)]);
        $sql = "SELECT *
                FROM   wcf1_tag
                " . $conditionBuilder;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        $tags = [];
        while ($row = $statement->fetchArray()) {
            $row['counter'] = $tagIDs[$row['tagID']];
            $tags[$row['name']] = new TagCloudTag(new Tag(null, $row));
        }

        // sort by counter
        \uasort($tags, self::compareTags(...));

        return $tags;
    }

    /**
     * @return array<int, int>
     */
    protected function fetchTagIDs(): array
    {
        $conditionBuilder = $this->getConditionBuilder();

        $sql = "SELECT     tag.tagID, COUNT(*) AS counter
                FROM       wcf1_tag_to_object object
                INNER JOIN wcf1_tag tag
                        ON tag.tagID = object.tagID
                " . $this->getJoin() . "
                " . $conditionBuilder . "
                GROUP BY   tag.tagID
                ORDER BY   counter DESC";
        $statement = WCF::getDB()->prepare($sql, $this->getLimit());
        $statement->execute($conditionBuilder->getParameters());

        return $statement->fetchMap('tagID', 'counter');
    }

    protected function getConditionBuilder(): PreparedStatementConditionBuilder
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('object.objectTypeID IN (?)', [$this->objectTypeIDs]);

        if ($this->languageIDs !== []) {
            $conditionBuilder->add('tag.languageID IN (?)', [$this->languageIDs]);
        }

        return $conditionBuilder;
    }

    /**
     * Returns the extra join statement.
     */
    protected function getJoin(): string
    {
        return "";
    }

    /**
     * Returns the number of tags to fetch.
     */
    protected function getLimit(): int
    {
        return 500;
    }
}
