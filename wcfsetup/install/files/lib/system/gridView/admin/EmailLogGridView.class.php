<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserEditForm;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\email\log\entry\EmailLogEntry;
use wcf\data\email\log\entry\EmailLogEntryList;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\filter\UserFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of email logs.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class EmailLogGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('entryID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('subject')
                ->label('wcf.acp.email.log.subject')
                ->titleColumn()
                ->filter(new TextFilter())
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof EmailLogEntry);

                            return \sprintf(
                                '%s<br/><small><kbd class="jsTooltip" title="%s">%s</kbd></small>',
                                StringUtil::encodeHTML($row->subject),
                                StringUtil::encodeHTML($row->getFormattedMessageId()),
                                StringUtil::encodeHTML(
                                    StringUtil::truncate($row->getFormattedMessageId(), 50)
                                )
                            );
                        }
                    }
                ),
            GridViewColumn::for('messageID')
                ->label('wcf.acp.email.log.messageId')
                ->filter(new TextFilter())
                ->hidden(),
            GridViewColumn::for('recipient')
                ->label('wcf.user.email')
                ->filter(WCF::getSession()->getPermission("admin.user.canEditMailAddress") ? new TextFilter() : null)
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof EmailLogEntry);

                            if (WCF::getSession()->getPermission("admin.user.canEditMailAddress")) {
                                $recipient = StringUtil::encodeHTML($row->recipient);
                            } else {
                                $recipient = StringUtil::encodeHTML($row->getRedactedRecipientAddress());
                            }

                            if ($row->getRecipient()) {
                                $userEditLink = StringUtil::encodeHTML(
                                    LinkHandler::getInstance()->getControllerLink(
                                        UserEditForm::class,
                                        ['object' => $row->getRecipient()]
                                    )
                                );
                                $username = StringUtil::encodeHTML($row->getRecipient()->getTitle());

                                $recipient .= <<<HTML
<br>
<small>
    <a href="{$userEditLink}">{$username}</a>
</small>
HTML;
                            }

                            return $recipient;
                        }
                    }
                )
                ->sortable(),
            GridViewColumn::for('recipientID')
                ->label('wcf.user.username')
                ->filter(new UserFilter())
                ->hidden(),
            GridViewColumn::for('time')
                ->label('wcf.acp.email.log.time')
                ->renderer(new TimeColumnRenderer())
                ->filter(new TimeFilter())
                ->sortable(),
            GridViewColumn::for('status')
                ->label('wcf.acp.email.log.status')
                ->filter(
                    new SelectFilter([
                        EmailLogEntry::STATUS_NEW => 'wcf.acp.email.log.status.new',
                        EmailLogEntry::STATUS_SUCCESS => 'wcf.acp.email.log.status.success',
                        EmailLogEntry::STATUS_TRANSIENT_FAILURE => 'wcf.acp.email.log.status.transient_failure',
                        EmailLogEntry::STATUS_PERMANENT_FAILURE => 'wcf.acp.email.log.status.permanent_failure',
                        EmailLogEntry::STATUS_DISCARDED => 'wcf.acp.email.log.status.discarded',
                    ])
                )
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof EmailLogEntry);

                            $badgeClasses = match ($row->status) {
                                EmailLogEntry::STATUS_SUCCESS => ' green',
                                EmailLogEntry::STATUS_TRANSIENT_FAILURE => ' yellow',
                                EmailLogEntry::STATUS_PERMANENT_FAILURE => ' red',
                                default => '',
                            };
                            $attributes = '';
                            $dialog = '';
                            if ($row->message) {
                                $badgeClasses .= ' pointer jsStaticDialog';
                                $attributes = \sprintf(' data-dialog-id="statusMessage%s"', $row->entryID);
                                $message = StringUtil::encodeHTML($row->message);

                                $dialog = <<<HTML
<div class="jsStaticDialogContent" id="statusMessage{$row->entryID}" style="display:none;">
{$message}
</div>
HTML;
                            }

                            return <<<HTML
<span class="badge{$badgeClasses}"{$attributes}>{$row->status}</span>
{$dialog}
HTML;
                        }
                    }
                )
                ->sortable(),
        ]);

        $this->setSortField('time');
        $this->setSortOrder('DESC');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.management.canViewLog');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new EmailLogEntryList();
    }
}
