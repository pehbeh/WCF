<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\AttachmentGridView;
use wcf\system\WCF;

/**
 * Shows a list of attachments.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    AttachmentGridView $gridView
 */
class AttachmentListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.attachment.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.attachment.canManageAttachment'];

    #[\Override]
    protected function createGridViewController(): AttachmentGridView
    {
        return new AttachmentGridView();
    }

    #[\Override]
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'stats' => $this->getAttachmentStats()
        ]);
    }

    private function getAttachmentStats(): array
    {
        $sql = "SELECT  COUNT(*) AS count,
                        COALESCE(SUM(file.fileSize), 0) AS size,
                        COALESCE(SUM(downloads), 0) AS downloads
                FROM    wcf1_attachment attachment
                LEFT JOIN   wcf1_file file
                ON          (file.fileID = attachment.fileID)";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();

        return $statement->fetchArray();
    }
}
