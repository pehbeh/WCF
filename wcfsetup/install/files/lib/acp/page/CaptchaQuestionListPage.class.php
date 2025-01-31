<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\CaptchaQuestionGridView;

/**
 * Lists the available captcha questions.
 *
 * @author      Olaf Braun, Matthias Schmidt
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    CaptchaQuestionGridView $gridView
 */
class CaptchaQuestionListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.captcha.question.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.captcha.canManageCaptchaQuestion'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new CaptchaQuestionGridView();
    }
}
