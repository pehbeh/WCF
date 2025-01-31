<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\BBCodeMediaProviderEditForm;
use wcf\data\bbcode\media\provider\BBCodeMediaProviderList;
use wcf\data\DatabaseObjectList;
use wcf\event\gridView\admin\BBCodeMediaProviderGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\BBCodeMediaProviderInteractions;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;

/**
 * Grid view for the list of bb code media providers.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class BBCodeMediaProviderGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('providerID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for('title')
                ->label('wcf.acp.bbcode.mediaProvider.title')
                ->renderer(new DefaultColumnRenderer())
                ->titleColumn()
                ->filter(new TextFilter())
                ->sortable(),
        ]);

        $interaction = new BBCodeMediaProviderInteractions();
        $this->setInteractionProvider($interaction);
        $this->addQuickInteraction(
            new ToggleInteraction(
                'enable',
                'core/bb-codes/media/providers/%s/enable',
                'core/bb-codes/media/providers/%s/disable'
            )
        );

        $this->addRowLink(new GridViewRowLink(BBCodeMediaProviderEditForm::class));
        $this->setSortField('title');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.content.bbcode.canManageBBCode');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new BBCodeMediaProviderList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new BBCodeMediaProviderGridViewInitialized($this);
    }
}
