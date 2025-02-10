<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\TrophyEditForm;
use wcf\data\DatabaseObjectList;
use wcf\data\trophy\I18nTrophyList;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\GridViewSortButton;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;

/**
 * Grid view for the list of trophies.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class TrophyGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("trophyID")
                ->label("wcf.global.objectID")
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for("title")
                ->titleColumn()
                ->label("wcf.global.title")
                ->renderer(new PhraseColumnRenderer())
                ->filter(new I18nTextFilter())
                ->sortable(sortByDatabaseColumn: "titleI18n"),
            GridViewColumn::for("showOrder")
                ->label("wcf.global.showOrder")
                ->renderer(new NumberColumnRenderer())
                ->filter(new NumericFilter())
                ->sortable(),
        ]);

        // TODO add interaction provider

        // TODO add endpoints
        $this->addQuickInteraction(
            new ToggleInteraction("enable", "core/trophies/%s/enable", "core/trophies/%s/disable")
        );

        $this->setSortButton(new GridViewSortButton("showOrder", "core/trophies/sort"));

        $this->setSortField("showOrder");
        $this->addRowLink(new GridViewRowLink(TrophyEditForm::class));
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return \MODULE_TROPHY
            && WCF::getSession()->getPermission("admin.trophy.canManageTrophy");
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new I18nTrophyList();
    }
}
