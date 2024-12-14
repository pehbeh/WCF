<?php

namespace wcf\system\gridView;

use wcf\acp\form\UserOptionEditForm;
use wcf\data\DatabaseObjectList;
use wcf\data\user\option\UserOption;
use wcf\data\user\option\UserOptionList;
use wcf\event\gridView\UserOptionGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\action\DeleteAction;
use wcf\system\gridView\action\EditAction;
use wcf\system\gridView\action\ToggleAction;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of user options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserOptionGridView extends DatabaseObjectListGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('optionID')
                ->label('wcf.global.objectID')
                ->renderer(new NumberColumnRenderer())
                ->sortable(),
            GridViewColumn::for('optionName')
                ->label('wcf.global.name')
                ->sortable()
                ->titleColumn()
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, mixed $context = null): string
                        {
                            \assert($context instanceof UserOption);

                            return StringUtil::encodeHTML($context->getTitle());
                        }
                    }
                ]),
            GridViewColumn::for('categoryName')
                ->label('wcf.global.category')
                ->sortable()
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, mixed $context = null): string
                        {
                            \assert($context instanceof UserOption);

                            return StringUtil::encodeHTML(
                                WCF::getLanguage()->get('wcf.user.option.category.' . $context->categoryName)
                            );
                        }
                    }
                ]),
            GridViewColumn::for('optionType')
                ->label('wcf.acp.user.option.optionType')
                ->sortable(),
            GridViewColumn::for('showOrder')
                ->label('wcf.global.showOrder')
                ->sortable()
                ->renderer(new NumberColumnRenderer()),
        ]);

        $this->addActions([
            new ToggleAction('core/users/options/%s/enable', 'core/users/options/%s/disable'),
            new EditAction(UserOptionEditForm::class),
            new DeleteAction('core/users/options/%s', fn(UserOption $row) => $row->canDelete()),
        ]);
        $this->addRowLink(new GridViewRowLink(UserOptionEditForm::class));
        $this->setSortField('showOrder');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.user.canManageUserOption');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new UserOptionList();
        $list->getConditionBuilder()->add(
            "option_table.categoryName IN (
                SELECT  categoryName
                FROM    wcf" . WCF_N . "_user_option_category
                WHERE   parentCategoryName = ?
            )",
            ['profile']
        );

        return $list;
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new UserOptionGridViewInitialized($this);
    }
}
