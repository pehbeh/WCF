<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\ReactionTypeGridView;

/**
 * Shows the list of reaction types.
 *
 * @author  Olaf Braun, Joshua Ruesweg
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<ReactionTypeGridView>
 */
final class ReactionTypeListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.reactionType.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_LIKE'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.reaction.canManageReactionType'];

    #[\Override]
    protected function createGridView(): ReactionTypeGridView
    {
        return new ReactionTypeGridView();
    }
}
