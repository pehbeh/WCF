<?php

namespace wcf\system\gridView\user;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueueList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\filter\UserFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\gridView\renderer\UserLinkColumnRenderer;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of moderation queue entries.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ModerationQueueGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("author")
                ->label("wcf.moderation.username")
                ->renderer(
                    new class extends UserLinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            $userID = $row->getAffectedObject()->getUserID();

                            if ($userID) {
                                return parent::render($userID, $row);
                            }

                            if ($row->getAffectedObject()->getUsername()) {
                                return StringUtil::encodeHTML($row->getAffectedObject()->getUsername() ?? '');
                            }

                            return '';
                        }

                        #[\Override]
                        public function prepare(mixed $value, DatabaseObject $row): void
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            parent::prepare($row->getAffectedObject()->getUserID(), $row);
                        }
                    }
                ),
            GridViewColumn::for('title')
                ->label('wcf.global.title')
                ->titleColumn()
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            $link = StringUtil::encodeHTML($row->getLink());
                            $title = StringUtil::encodeHTML($row->getTitle());
                            return <<<HTML
                                <a href="{$link}">{$title}</a>
                            HTML;
                        }
                    }
                ),
            GridViewColumn::for("assignedUser")
                ->label("wcf.moderation.assignedUser")
                ->filter(new UserFilter("moderation_queue.assignedUserID"))
                ->sortable(sortByDatabaseColumn: "assignedUsername")
                ->renderer(
                    new class extends UserLinkColumnRenderer {
                        public function __construct()
                        {
                            parent::__construct(fallbackValue: "assignedUsername");
                        }

                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            return parent::render($row->assignedUserID, $row);
                        }

                        #[\Override]
                        public function prepare(mixed $value, DatabaseObject $row): void
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            parent::prepare($row->assignedUserID, $row);
                        }
                    }
                ),
            GridViewColumn::for("objectType")
                ->label("wcf.moderation.report.reportedContent")
                ->filter(new SelectFilter($this->getModerationQueueObjectTypeIDs(), "moderation_queue.objectTypeID"))
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            return WCF::getLanguage()->getDynamicVariable(
                                "wcf.moderation.type.{$row->getObjectTypeName()}"
                            );
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: "moderation_queue.objectTypeID"),
            GridViewColumn::for("status")
                ->label("wcf.moderation.status")
                ->sortable(sortByDatabaseColumn: "moderation_queue.status")
                ->filter(
                    new class([
                        ModerationQueue::STATUS_OUTSTANDING => "wcf.moderation.status.outstanding",
                        ModerationQueue::STATUS_DONE => "wcf.moderation.status.done",
                    ]) extends SelectFilter {
                        #[\Override]
                        public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
                        {
                            if ($value == ModerationQueue::STATUS_DONE) {
                                $list->getConditionBuilder()->add(
                                    "moderation_queue.status IN (?)",
                                    [
                                        [
                                            ModerationQueue::STATUS_DONE,
                                            ModerationQueue::STATUS_CONFIRMED,
                                            ModerationQueue::STATUS_REJECTED
                                        ]
                                    ]
                                );
                            } else {
                                $list->getConditionBuilder()->add(
                                    "moderation_queue.status IN (?)",
                                    [[ModerationQueue::STATUS_OUTSTANDING, ModerationQueue::STATUS_PROCESSING]]
                                );
                            }
                        }
                    }
                )
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof ViewableModerationQueue);

                            $status = StringUtil::encodeHTML($row->getStatus());
                            return <<<HTML
                                <span class="status status-{$status}">{$status}</span>
                            HTML;
                        }
                    }
                ),
            GridViewColumn::for("comments")
                ->label("wcf.moderation.comments")
                ->sortable(sortByDatabaseColumn: "moderation_queue.comments")
                ->filter(new NumericFilter("moderation_queue.comments"))
                ->renderer(new NumberColumnRenderer()),
            GridViewColumn::for("lastChangeTime")
                ->label("wcf.moderation.lastChangeTime")
                ->sortable(sortByDatabaseColumn: "moderation_queue.lastChangeTime")
                ->filter(new TimeFilter("moderation_queue.lastChangeTime"))
                ->renderer(new TimeColumnRenderer()),
        ]);

        // TODO add interactions

        $this->setSortField("lastChangeTime");
        $this->setSortOrder("DESC");
    }

    private function getModerationQueueObjectTypeIDs(): array
    {
        $objectTypes = [];
        foreach (
            ModerationQueueManager::getInstance()->getDefinitionNamesByObjectTypeIDs() as $objectTypeID => $definition
        ) {
            $objectType = ObjectTypeCache::getInstance()->getObjectType($objectTypeID);
            $objectTypes[$objectTypeID] = \sprintf(
                "%s - %s",
                WCF::getLanguage()->getDynamicVariable('wcf.moderation.type.' . $objectType->objectType),
                WCF::getLanguage()->getDynamicVariable('wcf.moderation.type.' . $definition)
            );
        }


        return $objectTypes;
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('mod.general.canUseModeration');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new ViewableModerationQueueList();
    }
}
