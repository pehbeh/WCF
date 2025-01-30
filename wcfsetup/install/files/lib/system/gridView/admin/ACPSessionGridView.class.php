<?php

namespace wcf\system\gridView\admin;

use wcf\data\acp\session\access\log\ACPSessionAccessLogList;
use wcf\data\DatabaseObjectList;
use wcf\event\gridView\admin\ACPSessionGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\WCF;

/**
 * Grid view for the list of admin session.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class ACPSessionGridView extends AbstractGridView
{
    public function __construct(private readonly int $sessionLogID)
    {
        $this->addColumns([
            GridViewColumn::for('sessionAccessLogID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('ipAddress')
                ->label('wcf.user.ipAddress')
                ->sortable()
                ->filter(new NumericFilter()),
            GridViewColumn::for('time')
                ->label('wcf.acp.sessionLog.time')
                ->sortable()
                ->filter(new TimeFilter()),
            GridViewColumn::for('className')
                ->label('wcf.acp.sessionLog.className')
                ->sortable(),
            GridViewColumn::for('requestURI')
                ->label('wcf.acp.sessionLog.requestURI')
                ->titleColumn()
                ->sortable(),
            GridViewColumn::for('requestMethod')
                ->label('wcf.acp.sessionLog.requestMethod')
                ->sortable()
                ->filter(
                    new SelectFilter([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'DELETE' => 'DELETE',
                    ])
                ),
        ]);

        $this->setSortField('time');
        $this->setSortOrder('DESC');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.management.canViewLog');
    }

    public function getParameters(): array
    {
        return [
            'sessionLogID' => $this->sessionLogID,
        ];
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new ACPSessionAccessLogList();
        $list->getConditionBuilder()->add('sessionLogID = ?', [$this->sessionLogID]);

        return $list;
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new ACPSessionGridViewInitialized($this);
    }
}
