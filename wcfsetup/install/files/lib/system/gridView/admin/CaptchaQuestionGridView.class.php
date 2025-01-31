<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\CaptchaQuestionEditForm;
use wcf\data\captcha\question\I18nCaptchaQuestionList;
use wcf\data\DatabaseObjectList;
use wcf\event\gridView\admin\CaptchaQuestionGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\CaptchaQuestionInteractions;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;

/**
 * Grid view for the list of user ranks.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class CaptchaQuestionGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('questionID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('question')
                ->label('wcf.acp.captcha.question.question')
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->sortable(sortByDatabaseColumn: 'questionI18n'),
            GridViewColumn::for('views')
                ->label('wcf.acp.captcha.question.views')
                ->sortable()
                ->filter(new NumericFilter()),
            GridViewColumn::for('correctSubmissions')
                ->label('wcf.acp.captcha.question.correctSubmissions')
                ->sortable()
                ->filter(new NumericFilter()),
            GridViewColumn::for('incorrectSubmissions')
                ->label('wcf.acp.captcha.question.incorrectSubmissions')
                ->sortable()
                ->filter(new NumericFilter()),
        ]);

        $interaction = new CaptchaQuestionInteractions();
        $this->setInteractionProvider($interaction);

        $this->addQuickInteraction(
            new ToggleInteraction('enable', 'core/captchas/questions/%s/enable', 'core/captchas/questions/%s/disable')
        );

        $this->setSortField('questionID');
        $this->addRowLink(new GridViewRowLink(CaptchaQuestionEditForm::class));
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.captcha.canManageCaptchaQuestion');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new I18nCaptchaQuestionList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new CaptchaQuestionGridViewInitialized($this);
    }
}
