<?php

namespace wcf\system\gridView\admin;

use wcf\acp\page\PackagePage;
use wcf\data\DatabaseObject;
use wcf\data\package\I18nPackageList;
use wcf\data\package\Package;
use wcf\event\gridView\admin\PackageGridViewInitialized;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ILinkColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\interaction\admin\PackageInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of installed packages.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends AbstractGridView<Package, I18nPackageList>
 */
final class PackageGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('packageID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('packageName')
                ->label('wcf.acp.package.name')
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->renderer(
                    new class extends PhraseColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            $renderedValue = parent::render($value, $row);

                            \assert($row instanceof Package);

                            if ($row->isTainted()) {
                                $title = WCF::getLanguage()->getDynamicVariable("wcf.acp.package.application.isTainted");
                                $renderedValue .= <<<HTML
                                    <span class="jsTooltip" title="{$title}">
                                        <fa-icon name="triangle-exclamation"></fa-icon>
                                    </span>
                                HTML;
                            }

                            return $renderedValue;
                        }
                    }
                )
                ->renderer(new PhraseColumnRenderer())
                ->sortable(sortByDatabaseColumn: "packageNameI18n"),
            GridViewColumn::for('author')
                ->label('wcf.acp.package.author')
                ->filter(new TextFilter())
                ->renderer([
                    new class extends DefaultColumnRenderer implements ILinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Package);

                            if (!$row->authorURL) {
                                return $value;
                            }

                            return \sprintf(
                                '<a href="%s" class="externalURL">%s</a>',
                                StringUtil::encodeHTML($row->authorURL),
                                $value
                            );
                        }
                    },
                ])
                ->sortable(),
            GridViewColumn::for('packageVersion')
                ->label('wcf.acp.package.version')
                ->sortable(),
            GridViewColumn::for('updateDate')
                ->label('wcf.acp.package.updateDate')
                ->sortable()
                ->renderer(new TimeColumnRenderer()),
        ]);

        $provider = new PackageInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(PackagePage::class)
        ]);
        $this->setInteractionProvider($provider);

        $this->setRowsPerPage(50);
        $this->setSortField('packageID');
        $this->addRowLink(new GridViewRowLink(PackagePage::class));
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.configuration.package.canUpdatePackage')
            || WCF::getSession()->getPermission('admin.configuration.package.canUninstallPackage');
    }

    #[\Override]
    protected function createObjectList(): I18nPackageList
    {
        return new I18nPackageList();
    }

    #[\Override]
    protected function getInitializedEvent(): PackageGridViewInitialized
    {
        return new PackageGridViewInitialized($this);
    }
}
