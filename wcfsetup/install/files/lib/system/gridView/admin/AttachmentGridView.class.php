<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserEditForm;
use wcf\data\attachment\AdministrativeAttachment;
use wcf\data\attachment\AdministrativeAttachmentList;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\event\gridView\admin\AttachmentGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\filter\UserFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\FilesizeColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\interaction\admin\AttachmentInteractions;
use wcf\system\interaction\bulk\admin\AttachmentBulkInteractions;
use wcf\system\request\LinkHandler;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of attachments.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class AttachmentGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('attachmentID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for('filename')
                ->label('wcf.attachment.filename')
                ->titleColumn()
                ->filter(new TextFilter('file_table.filename'))
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof AdministrativeAttachment);

                            $isImage = $row->isImage;
                            $link = $row->getLink();
                            $fancyBox = $isImage ? ' data-fancybox="attachments" data-caption="' . $row->filename . '"' : '';
                            if ($row->tinyThumbnailType) {
                                $thumbnailLink = \sprintf(
                                    '<img src="%s" class="attachmentTinyThumbnail" alt="">',
                                    $row->getThumbnailLink('tiny')
                                );
                            } else {
                                $thumbnailLink = FontAwesomeIcon::fromValues($row->getIconName())->toHtml(64);
                            }
                            $filename = StringUtil::wordwrap($row->filename, 30, "\xE2\x80\x8B");
                            if ($row->getContainerObject()) {
                                $containerObject = \sprintf(
                                    '<p><small><a href="%s">%s</a></small></p>',
                                    $row->getContainerObject()->getLink(),
                                    StringUtil::encodeHTML(
                                        StringUtil::wordwrap($row->getContainerObject()->getTitle(), 30, "\xE2\x80\x8B")
                                    )
                                );
                            } else {
                                $containerObject = "";
                            }

                            return <<<HTML
                                <div class="box64">
                                    <a href="{$link}"{$fancyBox}>
                                        {$thumbnailLink}
                                    </a>
                                    <div>
                                        <p><a href="{$link}">{$filename}</a></p>
                                        {$containerObject}
                                    </div>
                                </div>
                                HTML;
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: 'file_table.filename'),
            GridViewColumn::for('fileType')
                ->label('wcf.attachment.fileType')
                ->filter(new SelectFilter($this->getAvailableFileTypes(), 'file_table.mimeType'))
                ->hidden(),
            GridViewColumn::for('username')
                ->label('wcf.user.username')
                ->filter(new UserFilter('user_table.username'))
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof AdministrativeAttachment);

                            if (!$row->userID) {
                                return WCF::getLanguage()->get('wcf.user.guest');
                            }

                            if (WCF::getSession()->getPermission('admin.user.canEditUser')) {
                                return \sprintf(
                                    '<a href="%s">%s</a>',
                                    LinkHandler::getInstance()->getControllerLink(UserEditForm::class, [
                                        'id' => $row->userID,
                                    ]),
                                    $row->username
                                );
                            } else {
                                return $row->username;
                            }
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: 'user_table.username'),
            GridViewColumn::for('uploadTime')
                ->label('wcf.attachment.uploadTime')
                ->renderer(new TimeColumnRenderer())
                ->filter(new TimeFilter())
                ->sortable(),
            GridViewColumn::for('downloads')
                ->label('wcf.attachment.downloads')
                ->filter(new NumericFilter())
                ->sortable(),
            GridViewColumn::for('filesize')
                ->label('wcf.attachment.filesize')
                ->renderer(new FilesizeColumnRenderer())
                ->sortable(sortByDatabaseColumn: 'file_table.filesize'),
            GridViewColumn::for('lastDownloadTime')
                ->label('wcf.attachment.lastDownloadTime')
                ->renderer(new TimeColumnRenderer())
                ->filter(new TimeFilter())
                ->sortable(),
        ]);

        $interaction = new AttachmentInteractions();
        $this->setInteractionProvider($interaction);
        $this->setBulkInteractionProvider(new AttachmentBulkInteractions());

        $this->setSortOrder('DESC');
        $this->setSortField('uploadTime');
    }

    private function getAvailableFileTypes(): array
    {
        $sql = "SELECT    DISTINCT file_table.mimeType
                FROM      wcf1_attachment attachment
                LEFT JOIN wcf1_file file_table
                ON        (file_table.fileID = attachment.fileID)
                WHERE     attachment.fileID IS NOT NULL";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        $fileTypes = $statement->fetchAll(\PDO::FETCH_COLUMN);

        \ksort($fileTypes);

        return \array_combine($fileTypes, $fileTypes);
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.attachment.canManageAttachment');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new AdministrativeAttachmentList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new AttachmentGridViewInitialized($this);
    }
}
