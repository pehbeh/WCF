<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserRankEditForm;
use wcf\data\cronjob\Cronjob;
use wcf\data\cronjob\I18nCronjobList;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\package\PackageCache;
use wcf\event\gridView\admin\CronjobGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\WCF;

/**
 * Grid view for the list of cronjobs.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class CronjobGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('cronjobID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('expression')
                ->label('wcf.acp.cronjob.expression')
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Cronjob);

                            return $row->getExpression();
                        }
                    }
                ),
            GridViewColumn::for('description')
                ->label('wcf.acp.cronjob.description')
                ->sortable(sortByDatabaseColumn: 'descriptionI18n')
                ->filter(new I18nTextFilter())
                ->renderer(new PhraseColumnRenderer())
                ->titleColumn(),
            GridViewColumn::for('packageID')
                ->label('wcf.acp.package.name')
                ->filter(new SelectFilter(PackageCache::getInstance()->getPackages()))
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Cronjob);

                            return $row->getPackage()->getTitle();
                        }
                    }
                )
                ->sortable(),
            GridViewColumn::for('nextExec')
                ->label('wcf.acp.cronjob.nextExec')
                ->renderer(
                    new class extends TimeColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Cronjob);

                            if ($row->isDisabled || $row->nextExec === 1) {
                                return '';
                            }

                            return parent::render($value, $row);
                        }
                    }
                )
                ->filter(new TimeFilter())
                ->sortable(),
        ]);

        $this->addRowLink(new GridViewRowLink(UserRankEditForm::class));
        $this->setSortField('description');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.management.canManageCronjob');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new I18nCronjobList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new CronjobGridViewInitialized($this);
    }
}
